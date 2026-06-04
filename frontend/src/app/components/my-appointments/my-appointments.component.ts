import { Component, inject, signal, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { AppointmentService } from '../../services/appointment.service';
import { AuthService } from '../../services/auth.service';
import { Appointment } from '../../models/shop.models';

@Component({
  selector: 'app-my-appointments',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './my-appointments.component.html',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MyAppointmentsComponent implements OnInit {
  private appointmentService = inject(AppointmentService);
  authService = inject(AuthService);

  appointments = signal<Appointment[]>([]);
  isLoading = signal(true);
  errorMessage = signal('');

  serviceTypeLabels: Record<string, string> = {
    'key_duplication': 'Duplicado de Llave',
    'key_programming': 'Programación de Llave',
    'remote_programming': 'Programación de Control Remoto',
    'transponder_key': 'Llave con Transponder',
    'smart_key': 'Llave Inteligente',
    'key_extraction': 'Extracción de Llave Rota',
    'emergency_opening': 'Apertura de Emergencia',
    'other': 'Otro'
  };

  statusLabels: Record<string, string> = {
    'pending': 'Pendiente',
    'confirmed': 'Confirmada',
    'completed': 'Completada',
    'cancelled': 'Cancelada'
  };

  statusColors: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'confirmed': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  };

  constructor(
    private router: Router
  ) { }

  ngOnInit() {
    this.loadMyAppointments();
  }

  loadMyAppointments() {
    this.isLoading.set(true);
    this.errorMessage.set('');

    this.appointmentService.getMyAppointments().subscribe({
      next: (appointments) => {
        this.appointments.set(appointments);
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error('Error loading appointments:', err);
        this.errorMessage.set('Error al cargar tus citas');
        this.isLoading.set(false);
      }
    });
  }

  cancelAppointment(id: number) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta cita? Desaparecerá de tu lista.')) {
      return;
    }

    this.appointmentService.deleteAppointment(id).subscribe({
      next: () => {
        alert('Cita eliminada correctamente');
        this.loadMyAppointments();
      },
      error: (err) => {
        console.error('Error deleting appointment:', err);
        alert('Error al eliminar la cita');
      }
    });
  }

  editAppointment(id: number) {
    this.router.navigate(['/appointment'], { queryParams: { id: id } });
  }

  getServiceTypeLabel(serviceType: string): string {
    return this.serviceTypeLabels[serviceType] || serviceType;
  }

  getStatusLabel(status: string): string {
    return this.statusLabels[status] || status;
  }

  getStatusColor(status: string): string {
    return this.statusColors[status] || 'bg-gray-100 text-gray-800';
  }

  formatDate(date: string | Date): string {
    const d = new Date(date);
    return d.toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
}
