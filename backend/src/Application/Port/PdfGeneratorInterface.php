<?php

namespace App\Application\Port;

interface PdfGeneratorInterface
{
    /**
     * Generates a PDF document from HTML and returns the binary content.
     */
    public function generateFromHtml(string $html): string;
}
