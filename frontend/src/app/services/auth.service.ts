import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '../../environments/environment';

export interface LoginResponse {
  token: string;
  refresh_token: string;
  user: {
    id: number;
    email: string;
    roles: string[];
  };
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = `${environment.apiUrl}/auth/login`;
  private registerUrl = `${environment.apiUrl}/auth/register`;
  private refreshUrl = `${environment.apiUrl}/auth/refresh`;
  private tokenKey = 'auth_token';
  private refreshTokenKey = 'refresh_token';
  private userKey = 'auth_user';
  
  isAuthenticated = signal<boolean>(this.hasToken());
  currentUser = signal<LoginResponse['user'] | null>(this.getStoredUser());

  constructor(private http: HttpClient) {}

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(this.apiUrl, { username: email, password }).pipe(
      tap(response => {
        localStorage.setItem(this.tokenKey, response.token);
        localStorage.setItem(this.refreshTokenKey, response.refresh_token);
        localStorage.setItem(this.userKey, JSON.stringify(response.user));
        this.isAuthenticated.set(true);
        this.currentUser.set(response.user);
      })
    );
  }

  register(email: string, password: string): Observable<any> {
    return this.http.post(this.registerUrl, { email, password });
  }

  refreshToken(): Observable<{ token: string; refresh_token: string }> {
    const refreshToken = this.getRefreshToken();
    return this.http.post<{ token: string; refresh_token: string }>(
      this.refreshUrl,
      { refresh_token: refreshToken }
    ).pipe(
      tap(response => {
        localStorage.setItem(this.tokenKey, response.token);
        localStorage.setItem(this.refreshTokenKey, response.refresh_token);
      })
    );
  }

  logout(): void {
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.refreshTokenKey);
    localStorage.removeItem(this.userKey);
    this.isAuthenticated.set(false);
    this.currentUser.set(null);
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  getRefreshToken(): string | null {
    return localStorage.getItem(this.refreshTokenKey);
  }

  hasToken(): boolean {
    return !!this.getToken();
  }

  isAdmin(): boolean {
    const user = this.currentUser();
    return user?.roles?.includes('ROLE_ADMIN') ?? false;
  }

  private getStoredUser(): LoginResponse['user'] | null {
    const user = localStorage.getItem(this.userKey);
    return user ? JSON.parse(user) : null;
  }
}
