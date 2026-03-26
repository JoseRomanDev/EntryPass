<?php

namespace App\Application\Port;

interface QrCodeGeneratorInterface
{
    /**
     * Generates a QR code from a string and returns its Base64 PNG representation.
     */
    public function generateBase64(string $data): string;
}
