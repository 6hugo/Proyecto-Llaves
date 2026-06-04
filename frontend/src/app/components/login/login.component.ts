import { Component, signal } from '@angular/core';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './login.component.html'
})
export class LoginComponent {
  email = signal('');
  password = signal('');
  error = signal<string | null>(null);
  loading = signal(false);

  constructor(
    private authService: AuthService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  onSubmit(): void {
    if (!this.email() || !this.password()) {
      this.error.set('Por favor completa todos los campos');
      return;
    }

    this.loading.set(true);
    this.error.set(null);

    this.authService.login(this.email(), this.password()).subscribe({
      next: () => {
        const returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/products';
        this.router.navigate([returnUrl]);
      },
      error: (err) => {
        this.error.set('Email o contraseña incorrectos');
        this.loading.set(false);
      }
    });
  }

  updateEmail(value: string): void {
    this.email.set(value);
  }

  updatePassword(value: string): void {
    this.password.set(value);
  }
}
