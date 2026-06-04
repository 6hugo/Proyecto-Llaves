import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { CartService } from '../../services/cart.service';
import { OrderService } from '../../services/order.service';
import { AuthService } from '../../services/auth.service';
import { CreateOrderDTO } from '../../models/shop.models';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './cart.component.html'
})
export class CartComponent {
  // Cambiados de computed a signal para que funcionen
  processing = signal(false);
  orderMessage = signal('');
  orderSuccess = signal(false);

  constructor(
    public cartService: CartService,
    private orderService: OrderService,
    private authService: AuthService,
    private router: Router
  ) { }

  getImageUrl(imageUrl: string | undefined | null): string {
    // 1. Si no hay valor, imagen por defecto
    if (!imageUrl) {
      return 'https://placehold.co/300x200?text=No+Image';
    }

    // 2. Si ya es una URL de internet (como las de tu base de datos)
    if (imageUrl.startsWith('http')) {
      // Corregimos via.placeholder si existe
      if (imageUrl.includes('via.placeholder.com')) {
        return imageUrl.replace('via.placeholder.com', 'placehold.co');
      }
      return imageUrl;
    }

    // 3. Si es una ruta relativa de assets
    if (imageUrl.startsWith('/assets') || imageUrl.startsWith('assets/')) {
      return imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`;
    }

    // 4. Por defecto, usamos el servicio de placeholders con el texto de la BD
    return `https://placehold.co/${imageUrl}`;
  }

  updateQuantity(productId: number, quantity: number): void {
    this.cartService.updateQuantity(productId, quantity);
  }

  removeItem(productId: number): void {
    if (confirm('¿Estás seguro de eliminar este producto?')) {
      this.cartService.removeItem(productId);
    }
  }

  clearCart(): void {
    if (confirm('¿Estás seguro de vaciar el carrito?')) {
      this.cartService.clear();
    }
  }

  checkout(): void {
    if (!this.authService.isAuthenticated()) {
      if (confirm('Debes iniciar sesión para realizar un pedido. ¿Deseas ir al login?')) {
        this.router.navigate(['/login'], { queryParams: { returnUrl: '/cart' } });
      }
      return;
    }

    const items = this.cartService.getItems();
    if (items.length === 0) return;

    this.processing.set(true);
    this.orderMessage.set('');

    const orderDTO: CreateOrderDTO = {
      items: items.map(item => ({
        product: `/api/products/${item.product.id}`,
        quantity: item.quantity
      }))
    };

    this.orderService.create(orderDTO).subscribe({
      next: (order) => {
        this.processing.set(false);
        this.orderSuccess.set(true);
        this.orderMessage.set('¡Pedido realizado con éxito!');
        this.cartService.clear();
        setTimeout(() => this.router.navigate(['/orders']), 2000);
      },
      error: (err) => {
        this.processing.set(false);
        this.orderSuccess.set(false);
        this.orderMessage.set(err.error?.message || 'Error al procesar el pedido');
      }
    });
  }
}