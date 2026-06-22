import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ProductService } from '../../services/product.service';
import { CartService } from '../../services/cart.service';
import { Product } from '../../models/shop.models';
import { RouterLink, ActivatedRoute } from '@angular/router';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-product-catalog',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './product-catalog.component.html'
})
export class ProductCatalogComponent implements OnInit {
  allProducts = signal<Product[]>([]);
  products = signal<Product[]>([]);
  loading = signal(true);
  error = signal('');

  constructor(
    private productService: ProductService,
    public cartService: CartService,
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    this.loadProducts();
  }

  loadProducts(): void {
    this.productService.getAll().subscribe({
      next: (response) => {
        // Eliminar duplicados usando el CONTENIDO (Marca + Modelo + Año)
        // Esto maneja casos en los que la BD tiene múltiples entradas para el mismo coche con diferentes IDs
        const uniqueMap = new Map();

        if (response.member && Array.isArray(response.member)) {
          response.member.forEach(p => {
            // Crear una clave única basada en lo que el USUARIO ve como el producto
            const contentKey = `${p.brand}-${p.model}-${p.year}`.toLowerCase();

            // Si aún no hemos visto este coche, lo agregamos.
            // Si YA lo hemos visto, ignoramos los duplicados subsiguientes.
            if (!uniqueMap.has(contentKey)) {
              uniqueMap.set(contentKey, p);
            }
          });
        }

        const unique = Array.from(uniqueMap.values());
        this.allProducts.set(unique);

        // Comprobar los parámetros de consulta (query params) después de cargar los productos
        const searchTerm = this.route.snapshot.queryParamMap.get('search');
        if (searchTerm) {
          this.filterProducts(searchTerm);
        } else {
          this.products.set(unique);
        }

        this.loading.set(false);
      },
      error: (err) => {
        this.error.set('Error al cargar productos');
        this.loading.set(false);
      }
    });
  }

  filterProducts(term: string): void {
    const searchTerm = term.toLowerCase();
    const filtered = this.allProducts().filter(product =>
      product.brand.toLowerCase().includes(searchTerm) ||
      product.model.toLowerCase().includes(searchTerm)
    );
    this.products.set(filtered);
  }

  onSearch(event: Event): void {
    const searchTerm = (event.target as HTMLInputElement).value;

    if (!searchTerm) {
      this.products.set(this.allProducts());
      return;
    }

    this.filterProducts(searchTerm);
  }

  getImageUrl(imageUrl: string | undefined | null): string {
    // 1. Si no hay valor, imagen por defecto
    if (!imageUrl) {
      return 'https://placehold.co/300x200?text=No+Image';
    }

    // 2. Si ya es una URL de internet, la respetamos
    if (imageUrl.startsWith('http')) {
      // Si la URL que falla es de via.placeholder, la reemplazamos por placehold.co
      if (imageUrl.includes('via.placeholder.com')) {
        return imageUrl.replace('via.placeholder.com', 'placehold.co');
      }
      return imageUrl;
    }

    // 3. Si es una ruta relativa de assets
    if (imageUrl.startsWith('/assets') || imageUrl.startsWith('assets/')) {
      return imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`;
    }

    // 4. Si es una ruta de subida de la API (por ejemplo, uploads/...)
    if (imageUrl.startsWith('/uploads') || imageUrl.startsWith('uploads/')) {
      const backendUrl = environment.apiUrl.replace(/\/api$/, '');
      const cleanPath = imageUrl.startsWith('/') ? imageUrl : `/${imageUrl}`;
      return `${backendUrl}${cleanPath}`;
    }

    // 5. Unimos el dominio de placehold.co con el texto de tu base de datos
    return `https://placehold.co/${imageUrl}`;
  }

  addToCart(product: Product): void {
    this.cartService.addItem(product, 1);
  }
}
