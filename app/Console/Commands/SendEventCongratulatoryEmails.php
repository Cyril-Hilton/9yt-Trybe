<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventPayout;
use App\Mail\EventCongratulatoryEmail;
use App\Services\FeeCalculatorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendEventCongratulatoryEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-congratulatory-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send congratulatory emails for completed events with payout details';

    /**
     * Execute the console command.
     */
    public function handle(FeeCalculatorService $feeCalculator)
    {
        $this->info('🔍 Looking for completed events...');

        // Find events that ended in the last 24-48 hours
        // This ensures we catch events that just ended
        $completedEvents = Event::whereDate('end_date', '>=', now()->subHours(48))
                                ->whereDate('end_date', '<=', now()->subHours(24))
                                ->where('status', 'approved')
                                ->whereDoesntHave('payouts', function ($query) {
                                    $query->whereNotNull('congratulatory_email_sent_at');
                                })
                                ->with(['company', 'orders', 'attendees'])
                                ->get();

        if ($completedEvents->isEmpty()) {
            $this->info('✅ No events found that need congratulatory emails.');
            return Command::SUCCESS;
        }

        $this->info("📧 Found {$completedEvents->count()} event(s) to process...");

        $successCount = 0;
        $errorCount = 0;

        foreach ($completedEvents as $event) {
            try {
                DB::beginTransaction();

                // Calculate event performance metrics
                $grossRevenue = $event->orders()
                                      ->where('payment_status', 'completed')
                                      ->sum('subtotal');

                $ticketsSold = DB::table('event_attendees')
                                 ->join('event_orders', 'event_attendees.event_order_id', '=', 'event_orders.id')
                                 ->where('event_orders.event_id', $event->id)
                                 ->where('event_orders.payment_status', 'completed')
                                 ->count();

                $attendeesCheckedIn = $event->attendees()
                                           ->where('checked_in', true)
                                           ->count();

                // Calculate payout using competitive 4% model
                $payoutCalc = $feeCalculator->calculatePayout($grossRevenue);

                // Create payout record
                $payout = EventPayout::create([
                    'company_id' => $event->company_id,
                    'event_id' => $event->id,
                    'gross_amount' => $grossRevenue,
                    'platform_fees' => $payoutCalc['platform_commission'],
                    'net_amount' => $payoutCalc['net_payout'],
                    'currency' => 'GHS',
                    'status' => 'pending',
                    'payout_method' => null, // To be filled by organizer
                    'total_tickets_sold' => $ticketsSold,
                    'total_attendees' => $attendeesCheckedIn,
                ]);

                // Send congratulatory email
                Mail::to($event->company->email)
                    ->send(new EventCongratulatoryEmail($payout));

                // Mark email as sent
                $payout->markCongratulatoryEmailSent();

                DB::commit();

                $this->line("✅ {$event->title} - Email sent to {$event->company->name}");
                $this->line("   💰 Net Payout: GH₵" . number_format($payout->net_amount, 2));
                $successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("❌ Error processing {$event->title}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("   ✅ Successful: {$successCount}");
        if ($errorCount > 0) {
            $this->error("   ❌ Failed: {$errorCount}");
        }

        return Command::SUCCESS;
    }
}
