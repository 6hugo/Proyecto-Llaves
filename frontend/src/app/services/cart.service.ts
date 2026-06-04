import { Injectable, signal, computed } from '@angular/core';
import { CartItem, Product } from '../models/shop.models';

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private readonly CART_KEY = 'shopping_cart';
  private cartItems = signal<CartItem[]>(this.loadCart());

  // Computados
  items = computed(() => this.cartItems());
  itemCount = computed(() => 
    this.cartItems().reduce((sum, item) => sum + item.quantity, 0)
  );
  total = computed(() => 
    this.cartItems().reduce((sum, item) => 
      sum + (parseFloat(item.product.price) * item.quantity), 0
    )
  );

  constructor() {}

  private loadCart(): CartItem[] {
    const stored = localStorage.getItem(this.CART_KEY);
    return stored ? JSON.parse(stored) : [];
  }

  private saveCart(): void {
    localStorage.setItem(this.CART_KEY, JSON.stringify(this.cartItems()));
  }

  addItem(product: Product, quantity: number = 1): void {
    const items = this.cartItems();
    const existingItem = items.find(item => item.product.id === product.id);

    if (existingItem) {
      // Verificar que no supere el stock
      const newQuantity = existingItem.quantity + quantity;
      if (newQuantity <= product.stock) {
        existingItem.quantity = newQuantity;
        this.cartItems.set([...items]);
      } else {
        alert(`Stock insuficiente. Disponible: ${product.stock}`);
      }
    } else {
      if (quantity <= product.stock) {
        this.cartItems.set([...items, { product, quantity }]);
      } else {
        alert(`Stock insuficiente. Disponible: ${product.stock}`);
      }
    }

    this.saveCart();
  }

  removeItem(productId: number): void {
    this.cartItems.set(
      this.cartItems().filter(item => item.product.id !== productId)
    );
    this.saveCart();
  }

  updateQuantity(productId: number, quantity: number): void {
    const items = this.cartItems();
    const item = items.find(item => item.product.id === productId);

    if (item) {
      if (quantity <= 0) {
        this.removeItem(productId);
      } else if (quantity <= item.product.stock) {
        item.quantity = quantity;
        this.cartItems.set([...items]);
        this.saveCart();
      } else {
        alert(`Stock insuficiente. Disponible: ${item.product.stock}`);
      }
    }
  }

  clear(): void {
    this.cartItems.set([]);
    localStorage.removeItem(this.CART_KEY);
  }

  getItems(): CartItem[] {
    return this.cartItems();
  }
}
