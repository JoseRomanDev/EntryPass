<?php

namespace App\Application\Message;

/**
 * Mensaje asíncrono que el Worker consume para:
 * 1. Generar el código QR del ticket
 * 2. Enviar el email de confirmación de compra al usuario
 */
class SendPurchaseEmailMessage
{
    public function __construct(
        public readonly string $purchaseId
    ) {}
}
