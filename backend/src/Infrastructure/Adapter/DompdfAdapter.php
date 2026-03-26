<?php

namespace App\Infrastructure\Adapter;

use App\Application\Port\PdfGeneratorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfAdapter implements PdfGeneratorInterface
{
    public function generateFromHtml(string $html): string
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
