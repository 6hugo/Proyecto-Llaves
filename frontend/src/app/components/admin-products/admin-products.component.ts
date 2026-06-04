import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ProductService } from '../../services/product.service';
import { Product } from '../../models/shop.models';

@Component({
  selector: 'app-admin-products',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-products.component.html'
})
export class AdminProductsComponent implements OnInit {
  products = signal<Product[]>([]);
  loading = signal(true);
  showModal = signal(false);
  editingProduct = signal<Product | null>(null);

  formData: any = {
    brand: '',
    model: '',
    year: new Date().getFullYear(),
    price: '',
    stock: 0,
    description: '',
    imageUrl: ''
  };

  constructor(private productService: ProductService) {}

  ngOnInit(): void {
    this.loadProducts();
  }

  loadProducts(): void {
    this.productService.getAll().subscribe({
      next: (response) => {
        this.products.set(response.member);
        this.loading.set(false);
      },
      error: (err) => {
        console.error('Error:', err);
        this.loading.set(false);
      }
    });
  }

  openModal(product?: Product): void {
    if (product) {
      this.editingProduct.set(product);
      this.formData = { ...product };
    } else {
      this.editingProduct.set(null);
      this.formData = {
        brand: '',
        model: '',
        year: new Date().getFullYear(),
        price: '',
        stock: 0,
        description: '',
        imageUrl: ''
      };
    }
    this.showModal.set(true);
  }

  closeModal(): void {
    this.showModal.set(false);
    this.editingProduct.set(null);
  }

  saveProduct(): void {
    const productData: Product = {
      brand: this.formData.brand,
      model: this.formData.model,
      year: parseInt(this.formData.year),
      price: this.formData.price.toString(),
      stock: parseInt(this.formData.stock),
      description: this.formData.description,
      imageUrl: this.formData.imageUrl
    };

    const editing = this.editingProduct();
    const request = editing 
      ? this.productService.update(editing.id!, productData)
      : this.productService.create(productData);

    request.subscribe({
      next: () => {
        this.closeModal();
        this.loadProducts();
      },
      error: (err) => {
        alert('Error al guardar: ' + (err.error?.message || 'Error desconocido'));
        console.error(err);
      }
    });
  }

  deleteProduct(id: number): void {
    if (confirm('¿Estás seguro de eliminar este producto?')) {
      this.productService.delete(id).subscribe({
        next: () => {
          this.loadProducts();
        },
        error: (err) => {
          alert('Error al eliminar: ' + (err.error?.message || 'Error desconocido'));
        }
      });
    }
  }
}
