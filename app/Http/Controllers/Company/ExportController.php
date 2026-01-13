<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExportController extends Controller
{
    use AuthorizesRequests;

    private ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function export(Request $request, Conference $conference, string $format)
    {
        $this->authorize('view', $conference);

        $validated = $request->validate([
            'type' => ['nullable', 'in:all,online,in_person'],
        ]);

        $type = $validated['type'] ?? 'all';

        switch ($format) {
            case 'pdf':
                return $this->exportService->exportFiltered($conference, $type, 'pdf');
            case 'excel':
                return $this->exportService->exportFiltered($conference, $type, 'excel');
            case 'csv':
                return $this->exportService->exportFiltered($conference, $type, 'csv');
            default:
                abort(404);
        }
    }
}