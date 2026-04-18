import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Event } from '../models/event.model';

@Injectable({
  providedIn: 'root'
})
export class EventService {

  private readonly http = inject(HttpClient);
  private readonly apiUrl = '/api/events';

  getEvents(): Observable<Event[]> {
    return this.http.get<Event[]>(this.apiUrl);
  }

  getEventById(id: string): Observable<Event> {
    return this.http.get<Event>(`${this.apiUrl}/${id}`);
  }

  createEvent(eventData: Partial<Event>): Observable<any> {
    return this.http.post<any>(this.apiUrl, eventData);
  }

  updateEvent(id: string, eventData: Partial<Event>): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/${id}`, eventData);
  }

  deleteEvent(id: string): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }
}
