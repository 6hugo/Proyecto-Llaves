import { Component, inject, signal, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AppointmentService } from '../../services/appointment.service';
import { AuthService } from '../../services/auth.service';
import { Appointment } from '../../models/shop.models';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-appointment',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: './appointment.component.html'
})
export class AppointmentComponent implements OnInit {
  appointmentService = inject(AppointmentService);
  authService = inject(AuthService);
  router = inject(Router);
  route = inject(ActivatedRoute);

  appointment = signal<Appointment>({
    customerName: '',
    customerEmail: '',
    customerPhone: '',
    appointmentDate: '',
    serviceAddress: '',
    vehicleMake: '',
    vehicleModel: '',
    vehicleYear: '',
    serviceType: 'key_duplication',
    status: 'pending'
  } as Appointment);
  isSubmitting = signal(false);
  errorMessage = signal('');
  successMessage = signal('');

  isEditing = signal(false);
  appointmentId = signal<number | null>(null);

  serviceTypes = [
    { value: 'key_duplication', label: 'Duplicado de Llave' },
    { value: 'key_programming', label: 'Programación de Llave' },
    { value: 'remote_programming', label: 'Programación de Control Remoto' },
    { value: 'transponder_key', label: 'Llave con Transponder' },
    { value: 'smart_key', label: 'Llave Inteligente' },
    { value: 'key_extraction', label: 'Extracción de Llave Rota' },
    { value: 'emergency_opening', label: 'Apertura de Emergencia' },
    { value: 'other', label: 'Otro' }
  ];

  ngOnInit() {
    if (!this.authService.isAuthenticated()) {
      this.router.navigate(['/login']);
    }

    const user = this.authService.currentUser();
    if (user) {
      this.appointment.update(a => ({
        ...a,
        customerName: (user as any).fullName || (user as any).username || 'Cliente',
        customerEmail: user.email
      }));
    }

    this.route.queryParams.subscribe((params: any) => {
      if (params['id']) {
        this.isEditing.set(true);
        this.appointmentId.set(+params['id']);
        this.loadAppointment(+params['id']);
      }
    });
  }

  loadAppointment(id: number) {
    this.appointmentService.getAppointment(id).subscribe({
      next: (apt) => {
        // Formatear fecha para input datetime-local (yyyy-MM-ddThh:mm)
        if (apt.appointmentDate) {
          const dateStr = new Date(apt.appointmentDate).toISOString().slice(0, 16);
          // @ts-ignore - Forzamos el string para el input
          apt.appointmentDate = dateStr;
        }
        this.appointment.set(apt);
      },
      error: (err) => {
        console.error('Error loading appointment:', err);
        this.errorMessage.set('Error al cargar la cita para editar.');
      }
    });
  }

  updateField(field: keyof Appointment, value: any) {
    this.appointment.update(app => ({ ...app, [field]: value }));
  }

  handleInputChange(event: Event, field: keyof Appointment) {
    const value = (event.target as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement).value;
    this.updateField(field, value);
  }

  submitAppointment() {
    if (!this.appointment().appointmentDate) {
      this.errorMessage.set('Por favor, selecciona una fecha y hora.');
      return;
    }

    this.isSubmitting.set(true);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const dateValue = this.appointment().appointmentDate;
      const formattedDate = new Date(dateValue!).toISOString();
      const formValues = this.appointment();

      // Crear carga útil limpia con solo campos editables
      const appointmentData = {
        customerName: formValues.customerName,
        customerEmail: formValues.customerEmail,
        customerPhone: formValues.customerPhone,
        appointmentDate: formattedDate,
        serviceAddress: formValues.serviceAddress,
        vehicleMake: formValues.vehicleMake,
        vehicleModel: formValues.vehicleModel,
        vehicleYear: formValues.vehicleYear,
        serviceType: formValues.serviceType,
        notes: formValues.notes
      };

      if (this.isEditing() && this.appointmentId()) {
        // MODO ACTUALIZACIÓN
        // console.warn('Ejecutando modo ACTUALIZAR para ID:', this.appointmentId());
        this.appointmentService.updateAppointment(this.appointmentId()!, appointmentData).subscribe({
          next: () => {
            this.handleSuccess('¡Cita actualizada exitosamente!');
          },
          error: (err) => this.handleError(err)
        });
      } else {
        // MODO CREACIÓN
        // console.warn('Ejecutando modo CREAR. isEditing:', this.isEditing(), 'ID:', this.appointmentId());
        this.appointmentService.createAppointment(appointmentData).subscribe({
          next: () => {
            this.handleSuccess('¡Cita agendada exitosamente! Te contactaremos pronto.');
          },
          error: (err) => this.handleError(err)
        });
      }

    } catch (e) {
      this.isSubmitting.set(false);
      this.errorMessage.set('Formato de fecha inválido.');
    }
  }

  private handleSuccess(message: string) {
    this.isSubmitting.set(false);
    this.successMessage.set(message);
    setTimeout(() => {
      this.router.navigate(['/my-appointments']);
    }, 1500);
  }

  private handleError(err: any) {
    this.isSubmitting.set(false);
    console.error('API Error:', err);
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Manejo de error IRI (éxito disfrazado)
    if (err.status === 400 && err.error?.detail?.includes("Unable to generate an IRI")) {
      this.handleSuccess(this.isEditing() ? '¡Cita actualizada exitosamente!' : '¡Cita agendada exitosamente!');
      return;
    }

    // Errores HTTP específicos
    if (err.status === 401) {
      this.errorMessage.set('Tu sesión ha expirado. ID: 401. Por favor, inicia sesión.');
      return;
    }
    if (err.status === 403) {
      this.errorMessage.set('No tienes permiso para realizar esta acción.');
      return;
    }
    if (err.status === 415) {
      this.errorMessage.set('Error de comunicación con el servidor (Formato no soportado).');
      return;
    }
    if (err.status >= 500) {
      this.errorMessage.set('Error interno del servidor. Inténtalo más tarde.');
      return;
    }

    // Validaciones detalladas de API Platform
    if (err.error?.violations && Array.isArray(err.error.violations)) {
      const messages = err.error.violations.map((v: any) => `${v.propertyPath}: ${v.message}`).join('. ');
      this.errorMessage.set('Datos inválidos: ' + messages);
      return;
    }

    // Fallbacks
    const errorMsg = err.error?.['hydra:description'] || err.error?.detail || err.error?.message || 'Error al procesar la cita.';
    this.errorMessage.set(errorMsg);

    // Desplazar hacia arriba para mostrar el error
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}