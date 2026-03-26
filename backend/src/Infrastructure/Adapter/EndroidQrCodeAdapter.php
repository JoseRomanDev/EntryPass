<?php

namespace App\Infrastructure\Adapter;

use App\Application\Port\QrCodeGeneratorInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class EndroidQrCodeAdapter implements QrCodeGeneratorInterface
{
    public function generateBase64(string $data): string
    {
        $qrCode = new QrCode(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer  = new PngWriter();
        $result  = $writer->write($qrCode);
        $pngData = $result->getString();

        return base64_encode($pngData);
    }
}
