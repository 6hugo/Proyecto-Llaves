# 🔐 Guía de Variables de Entorno y Despliegue

Esta guía explica cómo manejar variables de entorno para desarrollo y producción.

## 📋 ¿Qué son las Variables de Entorno?

Son valores que cambian según el entorno (desarrollo, producción):
- Claves secretas de API
- URLs de base de datos
- Contraseñas
- Configuraciones de servicios

**⚠️ NUNCA subas contraseñas reales a Git**

---

## 🏠 Desarrollo Local (Tu Equipo)

### 1. Backend (Symfony)

**Archivo:** `backend/.env`

```env
# Este archivo SÍ se sube a Git con valores de desarrollo
APP_ENV=dev
APP_SECRET=cambiar_este_secreto_en_produccion

# Base de datos de desarrollo
DATABASE_URL="postgresql://llaves_user:llaves_password@db:5432/llaves_db?serverVersion=16&charset=utf8"
```

**Archivo:** `backend/.env.local` (Crear si necesitas)

```env
# Este archivo NO se sube a Git (.gitignore lo ignora)
# Úsalo para sobrescribir valores locales si lo necesitas
DATABASE_URL="postgresql://otro_usuario:otra_pass@localhost:5432/otra_db"
```

### 2. Frontend (Angular)

Angular usa archivos de entorno en:

**Archivo:** `frontend/src/environments/environment.ts` (Desarrollo)

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8080/api'
};
```

**Archivo:** `frontend/src/environments/environment.prod.ts` (Producción)

```typescript
export const environment = {
  production: true,
  apiUrl: 'https://tu-dominio.com/api'
};
```

**Uso en tu código:**

```typescript
import { environment } from '../environments/environment';

// Usar la URL de la API
fetch(`${environment.apiUrl}/usuarios`);
```

---

## 🚀 Producción

### Paso 1: Configurar Variables de Entorno de Producción

**Crear archivo:** `.env` en la raíz (NO subir a Git)

```env
# Variables de producción
DB_NAME=llaves_produccion
DB_USER=usuario_produccion
DB_PASSWORD=TuPasswordSegura123!@#
APP_SECRET=UnSecretoMuyLargoYAleatorio789xyz

# Otras configuraciones
DOMAIN=tu-dominio.com
EMAIL_HOST=smtp.gmail.com
EMAIL_USER=tu-email@gmail.com
EMAIL_PASSWORD=tu-password-email
```

**Archivo de ejemplo:** `.env.example` (Este SÍ se sube a Git)

```env
# Copia este archivo a .env y rellena con tus valores reales
DB_NAME=nombre_base_datos
DB_USER=usuario_db
DB_PASSWORD=password_seguro
APP_SECRET=secreto_muy_largo
DOMAIN=tu-dominio.com
```

### Paso 2: Actualizar docker-compose.prod.yml

**Ver archivo:** `docker-compose.prod.yml`

```yaml
version: '3.8'

services:
  db:
    image: postgres:16-alpine
    container_name: proyecto-llaves-db-prod
    environment:
      POSTGRES_DB: ${DB_NAME}
      POSTGRES_USER: ${DB_USER}
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - postgres_data:/var/lib/postgresql/data
    networks:
      - llaves-network
    restart: unless-stopped

  php:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
    container_name: proyecto-llaves-php-prod
    volumes:
      - ./backend:/var/www/backend
    environment:
      DATABASE_URL: "postgresql://${DB_USER}:${DB_PASSWORD}@db:5432/${DB_NAME}?serverVersion=16&charset=utf8"
      APP_ENV: prod
      APP_SECRET: ${APP_SECRET}
    depends_on:
      - db
    networks:
      - llaves-network
    restart: unless-stopped

  nginx-backend:
    image: nginx:alpine
    container_name: proyecto-llaves-nginx-backend-prod
    volumes:
      - ./backend:/var/www/backend
      - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "8080:80"
    depends_on:
      - php
    networks:
      - llaves-network
    restart: unless-stopped

  frontend:
    build:
      context: .
      dockerfile: docker/angular/Dockerfile
      target: production
    container_name: proyecto-llaves-frontend-prod
    ports:
      - "80:80"
    networks:
      - llaves-network
    restart: unless-stopped

volumes:
  postgres_data:

networks:
  llaves-network:
    driver: bridge
```

### Paso 3: Backend - Configuración de Producción

**Actualizar:** `backend/.env`

```env
APP_ENV=prod
APP_SECRET=${APP_SECRET}
DATABASE_URL=${DATABASE_URL}
```

**Optimizar Symfony:**

```powershell
# Limpiar caché
docker exec proyecto-llaves-php-prod bin/console cache:clear --env=prod

# Instalar dependencias sin dev
docker exec proyecto-llaves-php-prod composer install --no-dev --optimize-autoloader
```

### Paso 4: Frontend - Build de Producción

Angular automáticamente usa `environment.prod.ts` al hacer build.

---

## 🌍 Despliegue a Producción

### Opción 1: Servidor VPS (DigitalOcean, AWS, etc.)

#### 1. Preparar el Servidor

```bash
# Conectar por SSH
ssh root@tu-servidor-ip

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Instalar Docker Compose
apt install docker-compose-plugin -y

# Instalar Git
apt install git -y
```

#### 2. Clonar el Proyecto

```bash
# Clonar en el servidor
cd /var/www
git clone https://github.com/LaBestiaEZ/proyecto-llaves.git proyecto-llaves
cd proyecto-llaves
```

#### 3. Configurar Variables de Entorno

```bash
# Crear archivo .env
nano .env
```

Pega tus variables de producción:

```env
DB_NAME=llaves_produccion
DB_USER=usuario_prod
DB_PASSWORD=PasswordSeguro123!
APP_SECRET=SecretoMuyLargoYAleatorio789
```

#### 4. Levantar en Producción

```bash
# Levantar con docker-compose de producción
docker compose -f docker-compose.prod.yml up -d --build

# Ver logs
docker logs -f proyecto-llaves-frontend-prod
docker logs -f proyecto-llaves-php-prod
```

#### 5. Configurar Dominio y SSL

**Opción A: Nginx Proxy Manager (Recomendado - Fácil)**

1. Instalar Nginx Proxy Manager:
```bash
docker run -d \
  --name nginx-proxy-manager \
  -p 80:80 \
  -p 443:443 \
  -p 81:81 \
  -v nginx_data:/data \
  -v nginx_letsencrypt:/etc/letsencrypt \
  jc21/nginx-proxy-manager:latest
```

2. Acceder a: `http://tu-ip:81`
3. Login default: `admin@example.com` / `changeme`
4. Añadir proxy host:
   - Domain: `tu-dominio.com`
   - Forward to: `proyecto-llaves-frontend-prod:80`
   - Activar SSL (Let's Encrypt automático)

**Opción B: Certbot Manual**

```bash
# Instalar Certbot
apt install certbot python3-certbot-nginx -y

# Obtener certificado SSL
certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

### Opción 2: Plataformas Cloud Simplificadas

#### Railway.app

1. Conecta tu repositorio de GitHub
2. Railway detecta Docker automáticamente
3. Añade variables de entorno en el dashboard
4. Despliega con un click

#### Render.com

1. Conecta tu repositorio
2. Crea servicios:
   - Web Service (Frontend)
   - Web Service (Backend)
   - PostgreSQL Database
3. Configura variables de entorno
4. Deploy automático

#### DigitalOcean App Platform

1. Conecta GitHub
2. Detecta Docker Compose
3. Configura variables de entorno
4. Deploy automático con dominio incluido

---

## 🔒 Seguridad en Producción

### 1. Actualizar .gitignore

**Verificar que estos archivos NO se suban:**

```gitignore
# Variables de entorno
.env
.env.local
.env.*.local

# Backend
/backend/.env.local
/backend/.env.local.php

# SSL/Certificados
*.key
*.pem
*.crt
```

### 2. Generar Secretos Seguros

```bash
# Generar APP_SECRET para Symfony
openssl rand -base64 32

# Generar password seguro
openssl rand -base64 20
```

### 3. Configurar Firewall

```bash
# Permitir solo puertos necesarios
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
ufw allow 22/tcp    # SSH
ufw enable
```

### 4. Actualizar Contraseñas de BD

```bash
# Cambiar password de PostgreSQL
docker exec -it proyecto-llaves-db-prod psql -U postgres
ALTER USER llaves_user WITH PASSWORD 'NuevaPasswordSegura123!';
```

---

## 📊 Monitoreo y Mantenimiento

### Ver Logs

```bash
# Logs en tiempo real
docker logs -f proyecto-llaves-frontend-prod
docker logs -f proyecto-llaves-php-prod
docker logs -f proyecto-llaves-db-prod

# Últimas 100 líneas
docker logs --tail 100 proyecto-llaves-php-prod
```

### Backups de Base de Datos

```bash
# Backup manual
docker exec proyecto-llaves-db-prod pg_dump -U llaves_user llaves_db > backup_$(date +%Y%m%d).sql

# Backup automático (cron)
0 2 * * * docker exec proyecto-llaves-db-prod pg_dump -U llaves_user llaves_db > /backups/backup_$(date +\%Y\%m\%d).sql
```

### Actualizar la Aplicación

```bash
# 1. Ir al servidor
ssh root@tu-servidor-ip
cd /var/www/proyecto-llaves

# 2. Descargar últimos cambios
git pull origin main

# 3. Reconstruir y reiniciar
docker compose -f docker-compose.prod.yml up -d --build

# 4. Limpiar caché de Symfony
docker exec proyecto-llaves-php-prod bin/console cache:clear --env=prod
```

---

## 🎯 Checklist de Despliegue

### Antes de Desplegar

- [ ] Probado localmente con Docker
- [ ] Variables de entorno configuradas
- [ ] Contraseñas seguras generadas
- [ ] .env NO está en Git
- [ ] Frontend usa environment.prod.ts
- [ ] Backend en modo producción (APP_ENV=prod)

### Durante el Despliegue

- [ ] Servidor con Docker instalado
- [ ] Proyecto clonado
- [ ] .env creado con valores reales
- [ ] Docker Compose levantado
- [ ] Contenedores corriendo (docker ps)
- [ ] Logs sin errores

### Después del Despliegue

- [ ] Dominio apunta al servidor
- [ ] SSL configurado (HTTPS funciona)
- [ ] Base de datos migrada
- [ ] Frontend accesible
- [ ] Backend API responde
- [ ] Backups configurados

---

## 🆘 Problemas Comunes

### "Database connection failed"

**Solución:**
1. Verifica que el contenedor de DB esté corriendo: `docker ps`
2. Revisa las variables de entorno en `.env`
3. Verifica logs: `docker logs proyecto-llaves-db-prod`

### "502 Bad Gateway"

**Solución:**
1. Verifica que PHP esté corriendo: `docker ps`
2. Revisa logs: `docker logs proyecto-llaves-php-prod`
3. Reinicia: `docker restart proyecto-llaves-php-prod`

### "CORS Error" en Frontend

**Solución:**
Actualizar `backend/config/packages/nelmio_cors.yaml`:

```yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['https://tu-dominio.com']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
```

---

## 📚 Recursos

- **Docker**: https://docs.docker.com/
- **Symfony Producción**: https://symfony.com/doc/current/deployment.html
- **Angular Deployment**: https://angular.dev/tools/cli/deployment
- **Let's Encrypt**: https://letsencrypt.org/
- **Nginx Proxy Manager**: https://nginxproxymanager.com/

---

**¡Tu aplicación está lista para producción! 🚀**