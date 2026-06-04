import { Component, inject, OnInit, signal } from '@angular/core';
import { AppointmentService } from '../../services/appointment.service';
import { Appointment } from '../../models/shop.models';
import { DatePipe } from '@angular/common';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-admin-appointments',
  standalone: true,
  imports: [DatePipe, CommonModule],
  templateUrl: './admin-appointments.component.html'
})
export class AdminAppointmentsComponent implements OnInit {
  private appointmentService = inject(AppointmentService);
  
  appointments = signal<Appointment[]>([]);

  ngOnInit() {
    this.loadAppointments();
  }

  loadAppointments() {
    this.appointmentService.getAppointments().subscribe({
      next: (appointments) => this.appointments.set(appointments),
      error: (err) => console.error('Error loading appointments:', err)
    });
  }

  updateStatus(id: number, status: string) {
    this.appointmentService.updateAppointment(id, { status }).subscribe({
      next: () => this.loadAppointments(),
      error: (err) => console.error('Error updating appointment:', err)
    });
  }

  onStatusChange(event: Event, id: number) {
    const target = event.target as HTMLSelectElement;
    if (target && target.value) {
      this.updateStatus(id, target.value);
    }
  }

  deleteAppointment(id: number) {
    if (confirm('¿Estás seguro de eliminar esta cita?')) {
      this.appointmentService.deleteAppointment(id).subscribe({
        next: () => this.loadAppointments(),
        error: (err) => console.error('Error deleting appointment:', err)
      });
    }
  }

  getStatusColor(status: string): string {
    const colors: any = {
      'pending': 'bg-yellow-100 text-yellow-800',
      'confirmed': 'bg-blue-100 text-blue-800',
      'in_progress': 'bg-purple-100 text-purple-800',
      'completed': 'bg-green-100 text-green-800',
      'cancelled': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
  }

  getStatusLabel(status: string): string {
    const labels: any = {
      'pending': 'Pendiente',
      'confirmed': 'Confirmada',
      'in_progress': 'En Progreso',
      'completed': 'Completada',
      'cancelled': 'Cancelada'
    };
    return labels[status] || status;
  }
}
