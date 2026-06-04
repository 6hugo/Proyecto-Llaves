import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth.service';
import { catchError, switchMap, throwError } from 'rxjs';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const token = authService.getToken();

  // No agregar token a login, register o refresh
  if (req.url.includes('/login') || req.url.includes('/register') || req.url.includes('/refresh')) {
    return next(req);
  }

  if (token) {
    req = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });
  }

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      // Si es 401 (token expirado), intentar renovar
      if (error.status === 401 && authService.getRefreshToken()) {
        return authService.refreshToken().pipe(
          switchMap(() => {
            // Reintentar la petición con el nuevo token
            const newToken = authService.getToken();
            const clonedReq = req.clone({
              setHeaders: {
                Authorization: `Bearer ${newToken}`
              }
            });
            return next(clonedReq);
          }),
          catchError((refreshError) => {
            // Si falla el refresh, cerrar sesión
            authService.logout();
            return throwError(() => refreshError);
          })
        );
      }
      return throwError(() => error);
    })
  );
};
