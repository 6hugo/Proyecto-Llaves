import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { Faq } from '../models/shop.models';

interface FaqResponse {
  'member': Faq[];
  'totalItems': number;
}

@Injectable({
  providedIn: 'root'
})
export class FaqService {
  private http = inject(HttpClient);
  private apiUrl = `${environment.apiUrl}/faqs`;

  getFaqs(): Observable<Faq[]> {
    return this.http.get<FaqResponse>(this.apiUrl).pipe(
      map(response => response['member'] || [])
    );
  }

  getActiveFaqs(): Observable<Faq[]> {
    const url = `${this.apiUrl}?active=1&order[position]=asc`;
    console.log('Haciendo petición a:', url);
    return this.http.get<FaqResponse>(url).pipe(
      map(response => {
        console.log('Respuesta de la API:', response);
        return response['member'] || [];
      })
    );
  }

  getFaq(id: number): Observable<Faq> {
    return this.http.get<Faq>(`${this.apiUrl}/${id}`);
  }

  createFaq(faq: Partial<Faq>): Observable<Faq> {
    return this.http.post<Faq>(this.apiUrl, faq);
  }

  updateFaq(id: number, faq: Partial<Faq>): Observable<Faq> {
    return this.http.put<Faq>(`${this.apiUrl}/${id}`, faq);
  }

  deleteFaq(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }
}
