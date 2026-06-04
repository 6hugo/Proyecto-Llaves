import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { OrderService } from '../../services/order.service';
import { Order } from '../../models/shop.models';

@Component({
  selector: 'app-my-orders',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './my-orders.component.html'
})
export class MyOrdersComponent implements OnInit {
  orders = signal<Order[]>([]);
  loading = signal(true);

  constructor(private orderService: OrderService) {}

  parseFloat(value: string): number {
    return parseFloat(value);
  }

  ngOnInit(): void {
    this.loadOrders();
  }

  loadOrders(): void {
    this.orderService.getAll().subscribe({
      next: (response) => {
        this.orders.set(response.member);
        this.loading.set(false);
      },
      error: (err) => {
        console.error('Error al cargar pedidos:', err);
        this.loading.set(false);
      }
    });
  }

  getStatusLabel(status: string): string {
    const labels: any = {
      'pending': 'Pendiente',
      'confirmed': 'Confirmado',
      'shipped': 'Enviado',
      'completed': 'Completado',
      'cancelled': 'Cancelado'
    };
    return labels[status] || status;
  }

  getStatusClass(status: string): string {
    const classes: any = {
      'pending': 'bg-yellow-100 text-yellow-800',
      'confirmed': 'bg-blue-100 text-blue-800',
      'shipped': 'bg-purple-100 text-purple-800',
      'completed': 'bg-green-100 text-green-800',
      'cancelled': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
  }
}
