import { Routes } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { ProductCatalogComponent } from './components/product-catalog/product-catalog.component';
import { CartComponent } from './components/cart/cart.component';
import { MyOrdersComponent } from './components/my-orders/my-orders.component';
import { AdminProductsComponent } from './components/admin-products/admin-products.component';
import { AdminOrdersComponent } from './components/admin-orders/admin-orders.component';
import { AdminFaqsComponent } from './components/admin-faqs/admin-faqs.component';
import { AdminAppointmentsComponent } from './components/admin-appointments/admin-appointments.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { AppointmentComponent } from './components/appointment/appointment.component';
import { MyAppointmentsComponent } from './components/my-appointments/my-appointments.component';
import { authGuard } from './guards/auth.guard';
import { adminGuard } from './guards/admin.guard';

export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'products', component: ProductCatalogComponent },
  { path: 'cart', component: CartComponent },
  { path: 'appointment', component: AppointmentComponent },
  { path: 'my-appointments', component: MyAppointmentsComponent, canActivate: [authGuard] },
  { path: 'orders', component: MyOrdersComponent, canActivate: [authGuard] },
  { path: 'admin/products', component: AdminProductsComponent, canActivate: [adminGuard] },
  { path: 'admin/orders', component: AdminOrdersComponent, canActivate: [adminGuard] },
  { path: 'admin/faqs', component: AdminFaqsComponent, canActivate: [adminGuard] },
  { path: 'admin/appointments', component: AdminAppointmentsComponent, canActivate: [adminGuard] },
];
