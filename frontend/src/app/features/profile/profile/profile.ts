import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule, CurrencyPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { PurchaseService } from '../../../core/services/purchase.service';
import { AuthService } from '../../../core/services/auth.service';
import { Purchase } from '../../../core/models/purchase.model';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, RouterLink, CurrencyPipe],
  templateUrl: './profile.html',
  styleUrl: './profile.css'
})
export class ProfileComponent implements OnInit {
  private readonly authService = inject(AuthService);
  private readonly purchaseService = inject(PurchaseService);

  user$ = this.authService.getProfile();
  purchases = signal<Purchase[]>([]);
  isLoading = signal(true);
  loadError = signal(false);

  /** ID de la compra cuyo panel de tickets está expandido */
  expandedPurchaseId = signal<string | null>(null);

  ngOnInit(): void {
    this.purchaseService.getMyPurchases().subscribe({
      next: (data) => {
        this.purchases.set(data);
        this.isLoading.set(false);
      },
      error: () => {
        this.loadError.set(true);
        this.isLoading.set(false);
      }
    });
  }

  toggleTickets(purchaseId: string): void {
    this.expandedPurchaseId.update(current =>
      current === purchaseId ? null : purchaseId
    );
  }

  /**
   * Genera la URL de imagen QR a partir del hash del ticket.
   * Usa la API pública qrserver.com — sin dependencias adicionales.
   */
  getQrImageUrl(qrCodeHash: string): string {
    return `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(qrCodeHash)}&size=180x180&bgcolor=0a0a0a&color=26b1c4&margin=10`;
  }

  getStatusClass(status: string): string {
    switch (status.toLowerCase()) {
      case 'completed':  return 'status-completed';
      case 'pending':    return 'status-pending';
      case 'cancelled':  return 'status-cancelled';
      default:           return '';
    }
  }

  getStatusLabel(status: string): string {
    switch (status.toLowerCase()) {
      case 'completed':  return '✓ Completada';
      case 'pending':    return '⏳ Pendiente';
      case 'cancelled':  return '✕ Cancelada';
      default:           return status;
    }
  }

  getGradient(id: string): string {
    const colors = [
      ['#1e5e65', '#0a2d30'],
      ['#24a8ae', '#1a4d4f'],
      ['#1a4a5a', '#0d2a35'],
      ['#06b6d4', '#0891b2'],
      ['#115e59', '#0f766e'],
      ['#164e63', '#083344']
    ];
    const index = id.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length;
    const [c1, c2] = colors[index];
    return `linear-gradient(135deg, ${c1}, ${c2})`;
  }
}
