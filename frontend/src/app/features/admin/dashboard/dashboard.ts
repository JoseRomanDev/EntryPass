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

  // Modal states
  showConfirmDelete = signal(false);
  eventToDelete = signal<{id: string, title: string} | null>(null);

  showConfirmRestore = signal(false);
  eventToRestore = signal<{id: string, title: string} | null>(null);

  openDeleteConfirm(id: string, title: string): void {
    this.eventToDelete.set({id, title});
    this.showConfirmDelete.set(true);
  }

  cancelDelete(): void {
    this.showConfirmDelete.set(false);
    this.eventToDelete.set(null);
  }

  confirmDelete(): void {
    const ev = this.eventToDelete();
    if (!ev) return;
    
    this.deletingId.set(ev.id);
    this.showConfirmDelete.set(false);
    
    this.eventService.deleteEvent(ev.id).subscribe({
      next: () => {
        this.events.update(current => current.map(e => e.id === ev.id ? { ...e, status: false } : e));
        this.deletingId.set(null);
        this.eventToDelete.set(null);
      },
      error: () => {
        this.error.set(`No se pudo dar de baja el evento "${ev.title}".`);
        setTimeout(() => this.error.set(null), 4000);
        this.deletingId.set(null);
        this.eventToDelete.set(null);
      }
    });
  }

  openRestoreConfirm(id: string, title: string): void {
    this.eventToRestore.set({id, title});
    this.showConfirmRestore.set(true);
  }

  cancelRestore(): void {
    this.showConfirmRestore.set(false);
    this.eventToRestore.set(null);
  }

  confirmRestore(): void {
    const ev = this.eventToRestore();
    if (!ev) return;
    
    this.deletingId.set(ev.id); // reuse same spinner visual
    this.showConfirmRestore.set(false);
    
    this.eventService.updateEvent(ev.id, { status: true }).subscribe({
      next: () => {
        this.events.update(current => current.map(e => e.id === ev.id ? { ...e, status: true } : e));
        this.deletingId.set(null);
        this.eventToRestore.set(null);
      },
      error: () => {
        this.error.set(`No se pudo dar de alta el evento "${ev.title}".`);
        setTimeout(() => this.error.set(null), 4000);
        this.deletingId.set(null);
        this.eventToRestore.set(null);
      }
    });
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
