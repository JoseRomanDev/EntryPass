import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth.service';
import { catchError, switchMap, throwError } from 'rxjs';

/**
 * Interceptor funcional que adjunta automáticamente el token JWT
 * en el header Authorization de cada petición HTTP saliente.
 *
 * Si el backend responde con 401 (token expirado o inválido),
 * limpia la sesión y reintenta la petición sin token para que
 * las rutas públicas (como GET /api/events) sigan funcionando.
 */
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const token = authService.token();

  if (token) {
    const cloned = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });

    return next(cloned).pipe(
      catchError(error => {
        if (error.status === 401) {
          // Token expirado o inválido: limpiar sesión
          authService.logout();

          // Reintentar la petición original sin token
          return next(req);
        }
        return throwError(() => error);
      })
    );
  }

  return next(req);
};
