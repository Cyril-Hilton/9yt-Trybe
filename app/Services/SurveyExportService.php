<?php

namespace App\Services;

use App\Models\Survey;
use Illuminate\Support\Facades\Response;

class SurveyExportService
{
    public function exportResponses(Survey $survey, string $format = 'csv')
    {
        $responses = $survey->responses()->with(['answers.question'])->completed()->get();

        if ($format === 'csv') {
            return $this->exportToCsv($survey, $responses);
        }

        // Add more formats as needed (Excel, PDF)
        return $this->exportToCsv($survey, $responses);
    }

    private function exportToCsv(Survey $survey, $responses)
    {
        $filename = 'survey-responses-' . $survey->slug . '-' . date('Y-m-d') . '.csv';

        $handle = fopen('php://temp', 'r+');

        // Build header row
        $headers = [
            'Response ID',
            'Respondent Name',
            'Respondent Email',
            'Submitted At',
            'Time Taken',
            'Device',
            'Status',
        ];

        // Add question headers
        $questions = $survey->questions()->orderBy('order')->get();
        foreach ($questions as $question) {
            $headers[] = $question->question_text;
        }

        fputcsv($handle, $headers);

        // Add data rows
        foreach ($responses as $response) {
            $row = [
                $response->id,
                $response->respondent_name ?? 'Anonymous',
                $response->respondent_email ?? 'N/A',
                $response->completed_at ? $response->completed_at->format('Y-m-d H:i:s') : 'N/A',
                $response->getTimeTakenFormatted(),
                ucfirst($response->device_type ?? 'Unknown'),
                $response->is_completed ? 'Completed' : 'Incomplete',
            ];

            // Add answers
            foreach ($questions as $question) {
                $answer = $response->answers->where('survey_question_id', $question->id)->first();
                $row[] = $answer ? $answer->getDisplayValue() : 'No answer';
            }

            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
