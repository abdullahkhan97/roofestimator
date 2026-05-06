<?php

namespace App\Services;

use App\Models\Estimate;
use Barryvdh\DomPDF\Facade\Pdf;

class EstimatePdfRenderer
{
    public function render(Estimate $estimate): string
    {
        $estimate->load(['roofType', 'roofPitch', 'roofComplexity']);

        $pdf = Pdf::loadView('estimates.pdf', compact('estimate'))
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'defaultFont'          => 'helvetica',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 150,
            ]);

        return $pdf->output();
    }
}
