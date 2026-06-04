import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Order, CreateOrderDTO } from '../models/shop.models';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class OrderService {
  private apiUrl = `${environment.apiUrl}/orders`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<{ member: Order[] }> {
    return this.http.get<{ member: Order[] }>(this.apiUrl);
  }

  getById(id: number): Observable<Order> {
    return this.http.get<Order>(`${this.apiUrl}/${id}`);
  }

  create(order: CreateOrderDTO): Observable<Order> {
    return this.http.post<Order>(this.apiUrl, order, {
      headers: { 'Content-Type': 'application/ld+json' }
    });
  }

  updateStatus(id: number, status: string): Observable<Order> {
    return this.http.patch<Order>(
      `${this.apiUrl}/${id}`,
      { status },
      { headers: { 'Content-Type': 'application/merge-patch+json' } }
    );
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }
}
