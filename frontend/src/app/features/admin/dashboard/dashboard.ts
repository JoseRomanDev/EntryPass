import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule, CurrencyPipe, DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { EventService } from '../../../core/services/event.service';
import { Event } from '../../../core/models/event.model';

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink, CurrencyPipe, DatePipe],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.css'
})
export class DashboardComponent implements OnInit {
  private readonly eventService = inject(EventService);

  events = signal<Event[]>([]);
  isLoading = signal(true);
  error = signal<string | null>(null);
  
  // Track deleting states
  deletingId = signal<string | null>(null);

  ngOnInit(): void {
    this.loadEvents();
  }

  loadEvents(): void {
    this.isLoading.set(true);
    this.eventService.getEvents().subscribe({
      next: (data) => {
        this.events.set(data);
        this.isLoading.set(false);
      },
      error: () => {
        this.error.set('Error al cargar la lista de eventos.');
        this.isLoading.set(false);
      }
    });
  }

  deleteEvent(id: string, title: string): void {
    if (confirm(`¿Estás seguro de que deseas eliminar eliminar el evento "${title}"?`)) {
      this.deletingId.set(id);
      this.eventService.deleteEvent(id).subscribe({
        next: () => {
          this.events.update(current => current.filter(e => e.id !== id));
          this.deletingId.set(null);
        },
        error: () => {
          alert('No se pudo eliminar el evento. Asegúrate de que no tenga compras asociadas.');
          this.deletingId.set(null);
        }
      });
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
