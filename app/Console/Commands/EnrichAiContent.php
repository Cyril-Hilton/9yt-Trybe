<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Event;
use App\Models\ShopProduct;
use App\Services\AI\AiEnrichmentService;
use Illuminate\Console\Command;

class EnrichAiContent extends Command
{
    protected $signature = 'ai:enrich-content {--types=} {--limit=} {--only-missing}';

    protected $description = 'Generate AI tags and FAQs for events, organizers, and products.';

    public function handle(AiEnrichmentService $enrichment): int
    {
        $limit = (int) ($this->option('limit') ?: config('services.ai.enrichment.limit', 40));

        $onlyMissing = (bool) $this->option('only-missing');
        $typesOption = (string) ($this->option('types') ?: '');
        $types = $typesOption !== ''
            ? collect(explode(',', $typesOption))->map(fn ($t) => trim(strtolower($t)))->filter()->values()->all()
            : ['events', 'companies', 'products'];

        $total = 0;

        if (in_array('events', $types, true)) {
            $total += $this->enrichEvents($enrichment, $limit, $onlyMissing);
        }

        if (in_array('companies', $types, true)) {
            $total += $this->enrichCompanies($enrichment, $limit, $onlyMissing);
        }

        if (in_array('products', $types, true)) {
            $total += $this->enrichProducts($enrichment, $limit, $onlyMissing);
        }

        $this->info('AI enrichment completed. Updated: ' . $total);

        return Command::SUCCESS;
    }

    private function enrichEvents(AiEnrichmentService $enrichment, int $limit, bool $onlyMissing): int
    {
        $query = Event::approved();

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('ai_tags')
                    ->orWhereNull('ai_faqs');
            });
        }

        $events = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($events as $event) {
            $data = $enrichment->generateEventTagsFaqs($event);
            if (!$data) {
                continue;
            }

            $event->fill($data);
            if ($event->isDirty(['ai_tags', 'ai_faqs'])) {
                $event->save();
                $updated++;
            }
        }

        $this->info('Events enriched: ' . $updated);

        return $updated;
    }

    private function enrichCompanies(AiEnrichmentService $enrichment, int $limit, bool $onlyMissing): int
    {
        $query = Company::query()
            ->where(function ($q) {
                $q->where('is_suspended', false)->orWhereNull('is_suspended');
            });

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('ai_tags')
                    ->orWhereNull('ai_faqs');
            });
        }

        $companies = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($companies as $company) {
            $data = $enrichment->generateOrganizerTagsFaqs($company);
            if (!$data) {
                continue;
            }

            $company->fill($data);
            if ($company->isDirty(['ai_tags', 'ai_faqs'])) {
                $company->save();
                $updated++;
            }
        }

        $this->info('Organizers enriched: ' . $updated);

        return $updated;
    }

    private function enrichProducts(AiEnrichmentService $enrichment, int $limit, bool $onlyMissing): int
    {
        $query = ShopProduct::query()
            ->where('status', 'approved')
            ->where('is_active', true);

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('ai_tags')
                    ->orWhereNull('ai_faqs');
            });
        }

        $products = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($products as $product) {
            $data = $enrichment->generateProductTagsFaqs($product);
            if (!$data) {
                continue;
            }

            $product->fill($data);
            if ($product->isDirty(['ai_tags', 'ai_faqs'])) {
                $product->save();
                $updated++;
            }
        }

        $this->info('Products enriched: ' . $updated);

        return $updated;
    }
}
