import { Component, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './register.component.html'
})
export class RegisterComponent {
  email = signal('');
  password = signal('');
  confirmPassword = signal('');
  error = signal<string | null>(null);
  loading = signal(false);

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  onSubmit(): void {
    this.error.set(null);

    if (!this.email() || !this.password() || !this.confirmPassword()) {
      this.error.set('Por favor completa todos los campos');
      return;
    }

    if (!this.isValidEmail(this.email())) {
      this.error.set('Por favor ingresa un email válido');
      return;
    }

    if (this.password().length < 6) {
      this.error.set('La contraseña debe tener al menos 6 caracteres');
      return;
    }

    if (this.password() !== this.confirmPassword()) {
      this.error.set('Las contraseñas no coinciden');
      return;
    }

    this.loading.set(true);

    this.authService.register(this.email(), this.password()).subscribe({
      next: () => {
        // Después de registrarse, hacer login automáticamente
        this.authService.login(this.email(), this.password()).subscribe({
          next: () => {
            this.router.navigate(['/products']);
          },
          error: () => {
            // Si falla el login, redirigir a login manual
            this.router.navigate(['/login']);
          }
        });
      },
      error: (err) => {
        this.error.set(err.error?.error || 'Error al registrarse. Intenta de nuevo.');
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

  updateConfirmPassword(value: string): void {
    this.confirmPassword.set(value);
  }

  private isValidEmail(email: string): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
}
