<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportService
{
    /**
     * Export data to Excel with selected columns
     */
    public function exportToExcel($data, $columns, $filename)
    {
        // Create Excel file dynamically
        $excel = Excel::download(function ($sheet) use ($data, $columns) {
            // Add header row
            $sheet->appendRow($columns);

            // Add data rows
            foreach ($data as $row) {
                $rowData = [];
                foreach ($columns as $column) {
                    // Handle nested properties (e.g., 'user.name')
                    if (strpos($column, '.') !== false) {
                        $parts = explode('.', $column);
                        $value = $row;
                        foreach ($parts as $part) {
                            $value = $value[$part] ?? $value->$part ?? null;
                        }
                        $rowData[] = $value;
                    } else {
                        $rowData[] = $row[$column] ?? $row->$column ?? null;
                    }
                }
                $sheet->appendRow($rowData);
            }
        }, "{$filename}.xlsx");

        return $excel;
    }

    /**
     * Export data to PDF with selected columns
     */
    public function exportToPdf($data, $columns, $filename, $title = 'Report')
    {
        $pdf = Pdf::loadView('reports.pdf-template', [
            'data' => $data,
            'columns' => $columns,
            'title' => $title
        ]);

        return $pdf->download("{$filename}.pdf");
    }

    /**
     * Generate report table HTML
     */
    public function generateReportTable($data, $columns)
    {
        $html = '<table class="table table-striped">';
        
        // Header
        $html .= '<thead><tr>';
        foreach ($columns as $column) {
            $html .= "<th>" . ucfirst(str_replace('_', ' ', $column)) . "</th>";
        }
        $html .= '</tr></thead>';

        // Body
        $html .= '<tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                if (strpos($column, '.') !== false) {
                    $parts = explode('.', $column);
                    $value = $row;
                    foreach ($parts as $part) {
                        $value = $value[$part] ?? $value->$part ?? null;
                    }
                } else {
                    $value = $row[$column] ?? $row->$column ?? null;
                }
                $html .= "<td>" . $value . "</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }
}
