/**
 * Modelos de Compra — EntryPass
 * Mapean exactamente la respuesta JSON del backend Symfony.
 */

/** Ticket individual dentro de una compra */
export interface Ticket {
  id: string;
  qrCodeHash: string;
  isUsed: boolean;
}

/** Compra completa del usuario (GET /api/purchases/my) */
export interface Purchase {
  id: string;
  eventId: string;
  eventTitle: string;
  eventDate: string;
  quantity: number;
  totalPrice: number;
  status: string;
  purchasedAt: string;
  tickets: Ticket[];
}

/** Request para realizar una compra (POST /api/purchases) */
export interface PurchaseRequest {
  eventId: string;
  quantity: number;
}

/** Respuesta del backend al realizar una compra */
export interface PurchaseResponse {
  status: string;
  purchaseId: string;
  ticketIds: string[];
  quantity: number;
  totalPrice: number;
}
