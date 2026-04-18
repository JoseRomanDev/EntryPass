import { Injectable, inject, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import {
  LoginRequest,
  LoginResponse,
  RegisterRequest,
  RegisterResponse,
  User
} from '../models/user.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private readonly http = inject(HttpClient);
  private readonly TOKEN_KEY = 'entrypass_token';

  /** Signal reactivo con el token actual */
  private readonly _token = signal<string | null>(this.getStoredToken());

  /** Signal computado: ¿está autenticado el usuario? */
  readonly isAuthenticated = computed(() => !!this._token());

  /** Signal computado: token actual */
  readonly token = computed(() => this._token());

  /** Signal computado: ¿tiene rol de administrador? */
  readonly isAdmin = computed(() => {
    const payload = this.getDecodedToken();
    if (!payload || !payload['roles']) return false;
    return (payload['roles'] as string[]).includes('ROLE_ADMIN');
  });

  login(credentials: LoginRequest): Observable<LoginResponse> {
    return this.http.post<LoginResponse>('/api/auth/login', credentials).pipe(
      tap(response => {
        this.storeToken(response.token);
        this._token.set(response.token);
      })
    );
  }

  register(data: RegisterRequest): Observable<RegisterResponse> {
    return this.http.post<RegisterResponse>('/api/register', data);
  }

  logout(): void {
    localStorage.removeItem(this.TOKEN_KEY);
    this._token.set(null);
  }

  getProfile(): Observable<User> {
    return this.http.get<User>('/api/me');
  }

  getMyPurchases(): Observable<any[]> {
    return this.http.get<any[]>('/api/purchases/my');
  }

  /** Extrae el payload del JWT para obtener datos básicos del usuario */
  getDecodedToken(): Record<string, unknown> | null {
    const token = this._token();
    if (!token) return null;
    try {
      const payload = token.split('.')[1];
      return JSON.parse(atob(payload));
    } catch {
      return null;
    }
  }

  private storeToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  private getStoredToken(): string | null {
    if (typeof localStorage === 'undefined') return null;
    return localStorage.getItem(this.TOKEN_KEY);
  }
}
