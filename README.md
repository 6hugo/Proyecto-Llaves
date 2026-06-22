https://arbor-kodak-carefully-twenty.trycloudflare.com/

# Proyecto Llaves - Symfony API + Angular + Docker

Sistema completo con backend en Symfony (API REST) y frontend en Angular, completamente dockerizado para desarrollo y producción.

## 📋 Requisitos Previos

### 1. Instalar Docker Desktop (Windows)

1. **Descargar Docker Desktop**: 
   - Ve a https://www.docker.com/products/docker-desktop/
   - Descarga la versión para Windows

2. **Instalar Docker Desktop**:
   - Ejecuta el instalador `Docker Desktop Installer.exe`
   - Sigue el asistente de instalación
   - Marca la opción "Use WSL 2 instead of Hyper-V" (recomendado)
   - Reinicia tu PC cuando se te solicite

3. **Configurar Docker Desktop**:
   - Abre Docker Desktop
   - Espera a que el motor de Docker inicie (ícono en la bandeja del sistema)
   - En Settings → Resources, asigna al menos:
     - **CPU**: 4 cores
     - **Memory**: 4 GB RAM
     - **Disk**: 20 GB

4. **Verificar instalación**:
   ```powershell
   docker --version
   docker compose version
   ```

### 2. Instalar Git (si no lo tienes)

1. Descarga desde: https://git-scm.com/download/win
2. Instala con las opciones por defecto
3. Verifica: `git --version`

### 3. Instalar Composer (para desarrollo local del backend)

1. Descarga: https://getcomposer.org/Composer-Setup.exe
2. Ejecuta el instalador
3. Reinicia PowerShell/Terminal
4. Verifica: `composer --version`

### 4. Instalar Node.js (para desarrollo local del frontend)

1. Descarga LTS desde: https://nodejs.org/
2. Instala con las opciones por defecto
3. Verifica:
   ```powershell
   node --version
   npm --version
   ```

## 🚀 Instalación del Proyecto

### Paso 1: Clonar el Repositorio

```powershell
cd C:\Users\TU_USUARIO\Desktop
git clone https://github.com/LaBestiaEZ/proyecto-llaves.git
cd proyecto-llaves
```

### Paso 2: Verificar la Estructura

Tu proyecto debe tener esta estructura:

```
proyecto-llaves/
├── backend/              # API Symfony
│   ├── src/
│   ├── config/
│   ├── composer.json
│   └── ...
├── frontend/             # App Angular
│   ├── src/
│   ├── package.json
│   └── ...
├── docker/               # Configuración Docker
│   ├── php/
│   ├── nginx/
│   └── angular/
├── docker-compose.yml
└── README.md
```

### Paso 3: Levantar el Proyecto con Docker

**⚠️ IMPORTANTE**: Asegúrate de que Docker Desktop esté ejecutándose (ícono verde en la bandeja).

```powershell
# Desde la raíz del proyecto
cd proyecto-llaves

# Levantar todos los servicios
docker compose up -d
```

**Primera vez**: El proceso puede tardar 5-10 minutos descargando imágenes e instalando dependencias.

### Paso 4: Verificar que Todo Funciona

```powershell
# Ver contenedores corriendo
docker ps

# Ver logs del frontend
docker logs proyecto-llaves-frontend

# Ver logs del backend
docker logs proyecto-llaves-php
```

Deberías ver 4 contenedores corriendo:
- `proyecto-llaves-frontend` - Angular
- `proyecto-llaves-nginx` - Servidor web backend
- `proyecto-llaves-php` - PHP-FPM para Symfony
- `proyecto-llaves-db` - PostgreSQL

## 🌐 Acceder a la Aplicación

Una vez levantado, accede a:

| Servicio | URL | Descripción |
|----------|-----|-------------|
| **Frontend** | http://localhost:4200 | Aplicación Angular |
| **Backend API** | http://localhost:8080 | API Symfony |
| **API Docs** | http://localhost:8080/api | Documentación API Platform |
| **Base de Datos** | localhost:5432 | PostgreSQL (usuario: `llaves_user`, password: `llaves_password`) |

## 🛠️ Comandos Útiles

### Docker

```powershell
# Levantar servicios
docker compose up -d

# Detener servicios
docker compose down

# Ver logs en tiempo real
docker logs -f proyecto-llaves-frontend
docker logs -f proyecto-llaves-php

# Reiniciar un servicio específico
docker restart proyecto-llaves-frontend

# Reconstruir un servicio
docker compose build --no-cache frontend
docker compose up -d frontend

# Limpiar todo y empezar de cero
docker compose down -v
docker compose build --no-cache
docker compose up -d
```

### Symfony (Backend)

```powershell
# Ejecutar comandos dentro del contenedor PHP
docker exec -it proyecto-llaves-php bash

# Crear una entidad
docker exec -it proyecto-llaves-php bin/console make:entity

# Crear migración
docker exec -it proyecto-llaves-php bin/console make:migration

# Ejecutar migraciones
docker exec -it proyecto-llaves-php bin/console doctrine:migrations:migrate

# Limpiar caché
docker exec -it proyecto-llaves-php bin/console cache:clear
```

### Angular (Frontend)

```powershell
# Ejecutar comandos dentro del contenedor
docker exec -it proyecto-llaves-frontend sh

# Generar componente
docker exec -it proyecto-llaves-frontend ng generate component nombre

# Generar servicio
docker exec -it proyecto-llaves-frontend ng generate service services/nombre

# Instalar dependencia
docker exec -it proyecto-llaves-frontend npm install paquete
```

### Base de Datos

```powershell
# Acceder a PostgreSQL
docker exec -it proyecto-llaves-db psql -U llaves_user -d llaves_db

# Backup de la base de datos
docker exec proyecto-llaves-db pg_dump -U llaves_user llaves_db > backup.sql

# Restaurar backup
docker exec -i proyecto-llaves-db psql -U llaves_user llaves_db < backup.sql
```

## 💻 Desarrollo Local (sin Docker)

Si prefieres desarrollar sin Docker:

### Backend

```powershell
cd backend
composer install
php -S localhost:8080 -t public
```

### Frontend

```powershell
cd frontend
npm install
npm start
```

## 🐛 Solución de Problemas

### El puerto 4200 o 8080 ya está en uso

```powershell
# Ver qué proceso usa el puerto
netstat -ano | findstr :4200
netstat -ano | findstr :8080

# Matar el proceso (usa el PID del comando anterior)
taskkill /PID <número> /F
```

### Docker no levanta los contenedores

1. Asegúrate de que Docker Desktop esté ejecutándose
2. Reinicia Docker Desktop
3. Elimina contenedores y volúmenes:
   ```powershell
   docker compose down -v
   docker system prune -a
   docker compose up -d
   ```

### El frontend da error de compilación

```powershell
# Reconstruir sin caché
docker compose down
docker compose build --no-cache frontend
docker compose up -d
```

### Error de permisos en archivos

```powershell
# En el contenedor PHP
docker exec -it proyecto-llaves-php chown -R www-data:www-data /var/www/backend
```

### La base de datos no se conecta

1. Verifica que el contenedor esté "healthy":
   ```powershell
   docker ps
   ```

2. Revisa los logs:
   ```powershell
   docker logs proyecto-llaves-db
   ```

3. Verifica la conexión:
   ```powershell
   docker exec proyecto-llaves-db pg_isready -U llaves_user
   ```

## 📦 Tecnologías Incluidas

- **Backend**: 
  - Symfony 7.1
  - API Platform 4.1
  - Doctrine ORM
  - PostgreSQL 16
  - PHP 8.3

- **Frontend**:
  - Angular 21
  - Tailwind CSS 3.4
  - TypeScript 5.9
  - Vitest

- **DevOps**:
  - Docker & Docker Compose
  - Nginx
  - Git

## 🚢 Despliegue a Producción

Para producción, usa el archivo `docker-compose.prod.yml`:

```powershell
# 1. Configurar variables de entorno
copy .env.example .env
# Edita .env con tus valores de producción

# 2. Levantar en modo producción
docker compose -f docker-compose.prod.yml up -d --build
```

## 👥 Trabajo en Equipo

### Para nuevos miembros del equipo:

1. **Instalar Docker Desktop** (ver arriba)
2. **Clonar el repositorio**:
   ```powershell
   git clone https://github.com/LaBestiaEZ/proyecto-llaves.git
   cd proyecto-llaves
   ```
3. **Levantar el proyecto**:
   ```powershell
   docker compose up -d
   ```
4. **Listo!** Accede a http://localhost:4200

### Flujo de trabajo Git:

```powershell
# Crear rama para tu feature
git checkout -b feature/nombre-feature

# Hacer cambios y commit
git add .
git commit -m "Descripción del cambio"

# Subir cambios
git push origin feature/nombre-feature

# Crear Pull Request en GitHub/GitLab
```

## 📞 Soporte

Si tienes problemas:
1. Revisa la sección "Solución de Problemas"
2. Verifica los logs: `docker logs <nombre-contenedor>`
3. Consulta con el equipo

## 📄 Licencia

Este proyecto es privado.
