import { CanActivateFn, Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth.service';

/**
 * Guard funcional que protege rutas de administración.
 * Si el usuario no tiene el rol ROLE_ADMIN, redirige al inicio.
 */
export const adminGuard: CanActivateFn = (
  _route: ActivatedRouteSnapshot,
  _state: RouterStateSnapshot
) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  if (authService.isAuthenticated() && authService.isAdmin()) {
    return true;
  }

  // Si no está autorizado o no es admin lo mandamos a la home
  router.navigate(['/']);
  return false;
};
