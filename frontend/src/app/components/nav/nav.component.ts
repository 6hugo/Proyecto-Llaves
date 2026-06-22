import { Component, inject, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CartService } from '../../services/cart.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-nav',
  standalone: true,
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './nav.component.html'
})
export class NavComponent {
  authService = inject(AuthService);
  cartService = inject(CartService);
  private router = inject(Router);

  showAdminMenu = signal(false);
  isMobileMenuOpen = signal(false);

  toggleAdminMenu() {
    this.showAdminMenu.set(!this.showAdminMenu());
  }

  closeAdminMenu() {
    this.showAdminMenu.set(false);
  }

  toggleMobileMenu() {
    this.isMobileMenuOpen.set(!this.isMobileMenuOpen());
  }

  closeMobileMenu() {
    this.isMobileMenuOpen.set(false);
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
