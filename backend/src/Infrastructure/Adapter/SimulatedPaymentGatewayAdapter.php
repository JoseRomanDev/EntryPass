<?php

namespace App\Infrastructure\Adapter;

use App\Application\Port\PaymentGatewayInterface;

class SimulatedPaymentGatewayAdapter implements PaymentGatewayInterface
{
    public function processPayment(float $amount): bool
    {
        // Simulamos un retraso de conexión a un servicio externo
        // sleep(1); // Descomentar para dar realismo a Postman si se desea

        // Simulación: Un 5% de probabilidad de que el pago falle por fondos insuficientes
        $random = mt_rand(1, 100);
        
        if ($random <= 5) {
            throw new \Exception("Pago rechazado: Fondos insuficientes en la tarjeta simulada.");
        }

        // Si es mayor a 5, el pago es exitoso
        return true;
    }
}
