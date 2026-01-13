<?php

namespace App\Services;

use App\Models\Conference;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ExportService
{
  public function exportPdf(Conference $conference)
{

//   public function exportPdf(Conference $conference)
//     {
//         $registrations = $conference->registrations()->with('conference')->get();

//         $pdf = Pdf::loadView('exports.registrations-pdf', [
//             'conference' => $conference,
//             'registrations' => $registrations,
//             'stats' => $this->getStats($conference),
//         ]);

//         return $pdf->download($conference->slug . '-registrations.pdf');
//     }


    $registrations = $conference->registrations()->with('conference')->get();

    $pdf = Pdf::loadView('exports.registrations-pdf', [
        'conference' => $conference,
        'registrations' => $registrations,
        'stats' => $this->getStats($conference),
    ])
    ->setPaper('a4', 'landscape');  // ADD THIS LINE

    return $pdf->download($conference->slug . '-registrations.pdf');
}

    public function exportCsv(Conference $conference)
    {
        $registrations = $conference->registrations()->with('conference')->get();
        
        $filename = Str::slug($conference->title) . '-registrations-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($registrations, $conference) {
            $file = fopen('php://output', 'w');
            
            // Get all custom field names
            $customFields = $conference->customFields->pluck('label', 'field_name')->toArray();
            
            // Header row
            $headerRow = ['ID', 'Name', 'Email', 'Phone', 'Attendance Type', 'Unique ID', 'Attended', 'Attended At', 'Registered At'];
            foreach ($customFields as $fieldName => $label) {
                $headerRow[] = $label;
            }
            fputcsv($file, $headerRow);
            
            // Data rows
            foreach ($registrations as $registration) {
                $row = [
                    $registration->id,
                    $registration->name,
                    $registration->email,
                    $registration->phone,
                    ucfirst(str_replace('_', '-', $registration->attendance_type)),
                    $registration->unique_id ?? 'N/A',
                    $registration->attended ? 'Yes' : 'No',
                    $registration->attended_at ? $registration->attended_at->format('Y-m-d H:i:s') : 'N/A',
                    $registration->created_at->format('Y-m-d H:i:s'),
                ];
                
                // Add custom field values
                foreach ($customFields as $fieldName => $label) {
                    $value = $registration->custom_data[$fieldName] ?? 'N/A';
                    $row[] = is_array($value) ? implode(', ', $value) : $value;
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel(Conference $conference)
    {
        // Excel export uses the same CSV format
        // If you want true Excel format, install PhpSpreadsheet
        return $this->exportCsv($conference);
    }

    private function getStats(Conference $conference): array
    {
        return [
            'total_registrations' => $conference->registrations()->count(),
            'online_registrations' => $conference->onlineRegistrations()->count(),
            'in_person_registrations' => $conference->inPersonRegistrations()->count(),
            'attended_count' => $conference->attendedRegistrations()->count(),
            'attendance_rate' => $conference->attendance_rate,
            'views_count' => $conference->views_count,
            'conversion_rate' => $conference->conversion_rate,
        ];
    }

    public function exportFiltered(Conference $conference, string $type, string $format)
    {
        $filteredConference = clone $conference;
        
        if ($type === 'online') {
            $filteredConference->setRelation('registrations', $conference->onlineRegistrations()->get());
        } elseif ($type === 'in_person') {
            $filteredConference->setRelation('registrations', $conference->inPersonRegistrations()->get());
        }

        switch ($format) {
            case 'pdf':
                return $this->exportPdf($filteredConference);
            case 'excel':
                return $this->exportExcel($filteredConference);
            case 'csv':
            default:
                return $this->exportCsv($filteredConference);
        }
    }
}