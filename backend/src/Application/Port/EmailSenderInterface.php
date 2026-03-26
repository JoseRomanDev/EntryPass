<?php

namespace App\Application\Port;

interface EmailSenderInterface
{
    /**
     * Sends an email with optional attachments and inline images.
     */
    public function send(
        string $to,
        string $subject,
        string $htmlBody,
        array $attachments = [],
        array $inlineImages = []
    ): void;
}
