import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { CommonModule, CurrencyPipe } from '@angular/common';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { EventService } from '../../../core/services/event.service';
import { PurchaseService } from '../../../core/services/purchase.service';
import { AuthService } from '../../../core/services/auth.service';
import { Event } from '../../../core/models/event.model';
import { PurchaseResponse } from '../../../core/models/purchase.model';

@Component({
  selector: 'app-event-detail',
  standalone: true,
  imports: [CommonModule, RouterLink, CurrencyPipe],
  templateUrl: './event-detail.html',
  styleUrl: './event-detail.css'
})
export class EventDetailComponent implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly eventService = inject(EventService);
  private readonly purchaseService = inject(PurchaseService);
  private readonly authService = inject(AuthService);

  event: Event | null = null;
  isLoading = true;

  // --- Signals de estado de la compra ---
  readonly quantity = signal(1);
  readonly showModal = signal(false);
  readonly purchasing = signal(false);
  readonly purchaseError = signal<string | null>(null);
  readonly purchaseSuccess = signal<PurchaseResponse | null>(null);

  // --- Computed ---
  readonly totalPrice = computed(() =>
    this.event ? this.event.price * this.quantity() : 0
  );

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.eventService.getEventById(id).subscribe({
        next: (event) => {
          this.event = event;
          this.isLoading = false;
        },
        error: () => {
          this.isLoading = false;
        }
      });
    }
  }

  // --- Selector de cantidad ---
  decreaseQuantity(): void {
    if (this.quantity() > 1) this.quantity.update(q => q - 1);
  }

  increaseQuantity(): void {
    if (this.quantity() < 4) this.quantity.update(q => q + 1);
  }

  // --- Flujo de compra ---
  openCheckout(): void {
    if (!this.authService.isAuthenticated()) {
      // Redirige a login guardando la URL actual para volver después
      this.router.navigate(['/auth/login'], {
        queryParams: { returnUrl: this.router.url }
      });
      return;
    }
    this.purchaseError.set(null);
    this.purchaseSuccess.set(null);
    this.showModal.set(true);
  }

  closeModal(): void {
    if (!this.purchasing()) {
      this.showModal.set(false);
    }
  }

  confirmPurchase(): void {
    if (!this.event || this.purchasing()) return;

    this.purchasing.set(true);
    this.purchaseError.set(null);

    this.purchaseService.purchase({
      eventId: this.event.id,
      quantity: this.quantity()
    }).subscribe({
      next: (response) => {
        this.purchasing.set(false);
        this.purchaseSuccess.set(response);
        // Actualizar stock local visualmente
        if (this.event) {
          this.event = { ...this.event, capacity: this.event.capacity - this.quantity() };
        }
      },
      error: (err) => {
        this.purchasing.set(false);
        const msg = err?.error?.error ?? 'Ha ocurrido un error. Inténtalo de nuevo.';
        this.purchaseError.set(msg);
      }
    });
  }

  goToProfile(): void {
    this.showModal.set(false);
    this.router.navigate(['/profile']);
  }

  // --- Utilidades visuales ---
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
