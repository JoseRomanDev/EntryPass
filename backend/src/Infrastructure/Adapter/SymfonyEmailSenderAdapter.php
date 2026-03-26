<?php

namespace App\Infrastructure\Adapter;

use App\Application\Port\EmailSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SymfonyEmailSenderAdapter implements EmailSenderInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function send(string $to, string $subject, string $htmlBody, array $attachments = [], array $inlineImages = []): void
    {
        $email = (new Email())
            ->from('entrypass@entrypass.dev')
            ->to($to)
            ->subject($subject)
            ->html($htmlBody);

        foreach ($inlineImages as $image) {
            if (isset($image['path'], $image['cid']) && file_exists($image['path'])) {
                $email->embedFromPath($image['path'], $image['cid']);
            }
        }

        foreach ($attachments as $attachment) {
            if (isset($attachment['content'], $attachment['name'], $attachment['type'])) {
                $email->attach($attachment['content'], $attachment['name'], $attachment['type']);
            }
        }

        $this->mailer->send($email);
    }
}
