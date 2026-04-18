import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { EventService } from '../../../core/services/event.service';
import { Event } from '../../../core/models/event.model';

@Component({
  selector: 'app-admin-event-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './event-form.html',
  styleUrl: './event-form.css'
})
export class EventFormComponent implements OnInit {
  private readonly eventService = inject(EventService);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);

  isEditing = signal(false);
  isLoading = signal(false);
  isSaving = signal(false);
  error = signal<string | null>(null);
  
  // Model
  eventId: string | null = null;
  eventData = signal<Partial<Event>>({
    title: '',
    description: '',
    date: '',
    price: 0,
    capacity: 0,
    status: true
  });

  ngOnInit(): void {
    this.eventId = this.route.snapshot.paramMap.get('id');
    
    if (this.eventId) {
      this.isEditing.set(true);
      this.loadEvent(this.eventId);
    }
  }

  loadEvent(id: string): void {
    this.isLoading.set(true);
    this.eventService.getEventById(id).subscribe({
      next: (data) => {
        // Formatear fecha para el input datetime-local (necesita formato YYYY-MM-DDThh:mm)
        let formattedDate = data.date;
        try {
          const dateObj = new Date(data.date);
          const offsetMs = dateObj.getTimezoneOffset() * 60000;
          formattedDate = new Date(dateObj.getTime() - offsetMs).toISOString().slice(0, 16);
        } catch(e) {}

        this.eventData.set({
          ...data,
          date: formattedDate
        });
        this.isLoading.set(false);
      },
      error: () => {
        this.error.set('No se pudo cargar el evento para su edición.');
        this.isLoading.set(false);
      }
    });
  }

  onSubmit(): void {
    this.isSaving.set(true);
    this.error.set(null);

    // Preparar payload enviando la fecha en formato ISO esperado
    const payload = { ...this.eventData() };
    if (payload.date) {
      payload.date = new Date(payload.date).toISOString();
    }

    const request$ = this.isEditing() && this.eventId
      ? this.eventService.updateEvent(this.eventId, payload)
      : this.eventService.createEvent(payload);

    request$.subscribe({
      next: () => {
        this.isSaving.set(false);
        this.router.navigate(['/admin/events']); // Volver al dashboard
      },
      error: (err) => {
        this.isSaving.set(false);
        this.error.set(err.error?.error || 'Ocurrió un error al guardar el evento.');
      }
    });
  }
}
