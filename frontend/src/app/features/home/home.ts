import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { RouterLink } from '@angular/router';
import { EventService } from '../../core/services/event.service';
import { SearchService } from '../../core/services/search.service';
import { Event } from '../../core/models/event.model';

import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-home',
  imports: [CommonModule, RouterLink],
  templateUrl: './home.html',
  styleUrl: './home.css'
})
export class Home implements OnInit {

  private readonly eventService = inject(EventService);
  private readonly searchService = inject(SearchService);

  /** Lista base de eventos */
  readonly allEvents = signal<Event[]>([]);
  readonly loading = signal(true);
  readonly selectedCategory = signal<string>('Todos');

  /** Lista filtrada por búsqueda y categoría */
  readonly filteredEvents = computed(() => {
    let list = this.allEvents();
    const search = this.searchService.searchTerm().toLowerCase();
    const category = this.selectedCategory();

    // 1. Filtrar por categoría
    if (category !== 'Todos') {
      list = list.filter(e => e.category === category);
    }

    // 2. Filtrar por texto
    if (search) {
      list = list.filter(e => 
        e.title.toLowerCase().includes(search) || 
        e.description.toLowerCase().includes(search)
      );
    }

    return list;
  });

  /** Eventos destacados para el carrusel (últimos 3) */
  readonly featuredEvents = computed(() => {
    return this.allEvents().slice(0, 3);
  });

  readonly currentSlide = signal(0);
  private carouselInterval: any;

  nextSlide(): void {
    this.currentSlide.update(prev => (prev + 1) % this.featuredEvents().length);
  }

  prevSlide(): void {
    this.currentSlide.update(prev => (prev - 1 + this.featuredEvents().length) % this.featuredEvents().length);
  }

  setSlide(index: number): void {
    this.currentSlide.set(index);
    this.resetCarouselTimer();
  }

  private startCarouselTimer(): void {
    this.carouselInterval = setInterval(() => {
      this.nextSlide();
    }, 5000);
  }

  private resetCarouselTimer(): void {
    if (this.carouselInterval) {
      clearInterval(this.carouselInterval);
      this.startCarouselTimer();
    }
  }

  setCategory(category: string): void {
    this.selectedCategory.set(category);
  }



  /** Gradientes para las tarjetas de eventos */
  readonly gradients = [
    'linear-gradient(135deg, #1e5e65, #0a2d30)',
    'linear-gradient(135deg, #24a8ae, #1a4d4f)',
    'linear-gradient(135deg, #1a4a5a, #0d2a35)',
    'linear-gradient(135deg, #06b6d4, #0891b2)',
    'linear-gradient(135deg, #115e59, #0f766e)',
    'linear-gradient(135deg, #164e63, #083344)',
  ];

  ngOnInit(): void {
    this.eventService.getEvents().subscribe({
      next: (events) => {
        this.allEvents.set(events);
        this.loading.set(false);
        if (events.length > 0) {
          this.startCarouselTimer();
        }
      },
      error: () => {
        this.loading.set(false);
      }
    });
  }

  ngOnDestroy(): void {
    if (this.carouselInterval) {
      clearInterval(this.carouselInterval);
    }
  }



  updateSearch(event: any): void {
    const term = event.target.value;
    this.searchService.setSearchTerm(term);
  }

  getGradient(index: number): string {
    return this.gradients[index % this.gradients.length];
  }

  formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
  }
}
