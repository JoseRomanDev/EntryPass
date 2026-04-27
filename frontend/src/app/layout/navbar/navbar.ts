import { Component, inject, signal } from '@angular/core';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css'
})
export class Navbar {
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);
  
  readonly isAuthenticated = this.authService.isAuthenticated;
  readonly isAdmin = this.authService.isAdmin;

  isMenuOpen = signal(false);

  toggleMenu(): void {
    this.isMenuOpen.update(val => !val);
  }

  closeMenu(): void {
    this.isMenuOpen.set(false);
  }

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/auth/login']);
  }
}
