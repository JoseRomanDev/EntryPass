import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Purchase, PurchaseRequest, PurchaseResponse } from '../models/purchase.model';

@Injectable({
  providedIn: 'root'
})
export class PurchaseService {

  private readonly http = inject(HttpClient);
  private readonly apiUrl = '/api/purchases';

  /**
   * Realiza una compra de entradas para un evento.
   * POST /api/purchases — requiere autenticación JWT (inyectada por authInterceptor)
   */
  purchase(request: PurchaseRequest): Observable<PurchaseResponse> {
    return this.http.post<PurchaseResponse>(this.apiUrl, request);
  }

  /**
   * Obtiene todas las compras del usuario autenticado.
   * GET /api/purchases/my — requiere autenticación JWT
   */
  getMyPurchases(): Observable<Purchase[]> {
    return this.http.get<Purchase[]>(`${this.apiUrl}/my`);
  }
}
