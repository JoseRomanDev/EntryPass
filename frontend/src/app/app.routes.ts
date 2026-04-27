import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { adminGuard } from './core/guards/admin.guard';

export const routes: Routes = [
  {
    path: '',
    loadComponent: () => import('./features/home/home').then(m => m.Home),
    title: 'EntryPass | Descubre experiencias únicas'
  },
  {
    path: 'auth/login',
    loadComponent: () => import('./features/auth/login/login').then(m => m.Login),
    title: 'EntryPass | Iniciar Sesión'
  },
  {
    path: 'auth/register',
    loadComponent: () => import('./features/auth/register/register').then(m => m.Register),
    title: 'EntryPass | Registrarse'
  },
  {
    path: 'events/:id',
    loadComponent: () => import('./features/events/event-detail/event-detail').then(m => m.EventDetailComponent),
    title: 'Detalle del Evento - EntryPass'
  },
  {
    path: 'profile',
    loadComponent: () => import('./features/profile/profile/profile').then(m => m.ProfileComponent),
    title: 'Mi Perfil - EntryPass',
    canActivate: [authGuard]
  },
  {
    path: 'admin',
    canActivate: [adminGuard],
    children: [
      {
        path: '',
        redirectTo: 'events',
        pathMatch: 'full'
      },
      {
        path: 'events',
        loadComponent: () => import('./features/admin/dashboard/dashboard').then(m => m.DashboardComponent)
      },
      {
        path: 'events/new',
        loadComponent: () => import('./features/admin/event-form/event-form').then(m => m.EventFormComponent)
      },
      {
        path: 'events/edit/:id',
        loadComponent: () => import('./features/admin/event-form/event-form').then(m => m.EventFormComponent)
      }
    ]
  },
  {
    path: '**',
    loadComponent: () => import('./features/error/not-found/not-found').then(m => m.NotFoundComponent),
    title: 'Página no encontrada - EntryPass'
  }
];
