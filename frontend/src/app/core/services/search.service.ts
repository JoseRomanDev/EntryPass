import { Injectable, signal } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class SearchService {
  /** Término de búsqueda global */
  readonly searchTerm = signal('');

  setSearchTerm(term: string): void {
    this.searchTerm.set(term);
  }
}
