# 👥 Guía para Desarrolladores del Equipo

Esta guía es para los miembros del equipo que trabajarán en el **frontend (Angular)**.

## 📋 Requisitos Previos

1. **Instalar Git**: https://git-scm.com/download/win
2. **Instalar Node.js (LTS)**: https://nodejs.org/
3. **Configurar Git** (primera vez):
   ```powershell
   git config --global user.name "Tu Nombre"
   git config --global user.email "tu.email@ejemplo.com"
   ```

## 🚀 Configuración Inicial (Solo Primera Vez)

### Paso 1: Clonar el Repositorio

```powershell
# Ve a tu carpeta de proyectos
cd C:\Users\TU_USUARIO\Desktop

# Clona el repositorio
git clone https://github.com/LaBestiaEZ/proyecto-llaves.git

# Entra al proyecto
cd proyecto-llaves
```

### Paso 2: Instalar Dependencias del Frontend

```powershell
cd frontend
npm install
```

### Paso 3: Crear Tu Rama de Trabajo

⚠️ **IMPORTANTE**: NUNCA trabajes directamente en la rama `main`

```powershell
# Crear y cambiar a tu rama (usa un nombre descriptivo)
git checkout -b feature/tu-nombre-feature

# Ejemplos:
# git checkout -b feature/login-page
# git checkout -b feature/dashboard
# git checkout -b feature/user-profile
```

## 💻 Desarrollo Diario

### 1. Levantar el Servidor de Desarrollo

```powershell
# Asegúrate de estar en la carpeta frontend
cd C:\Users\TU_USUARIO\Desktop\proyecto-llaves\frontend

# Iniciar el servidor
npm start
```

Accede a: **http://localhost:4200**

### 2. Hacer Cambios

Trabaja normalmente en tu editor de código (VS Code, WebStorm, etc.) modificando archivos dentro de la carpeta `frontend/`.

### 3. Ver Qué Archivos Has Modificado

```powershell
# Ver archivos modificados
git status

# Ver cambios específicos
git diff
```

### 4. Guardar Tus Cambios (Commit)

```powershell
# 1. Agregar los archivos modificados
git add frontend/

# O agregar archivos específicos
git add frontend/src/app/componente.ts

# 2. Hacer commit con un mensaje descriptivo
git commit -m "Descripción clara de los cambios"

# Ejemplos de buenos mensajes:
# git commit -m "Añadido componente de login"
# git commit -m "Corregido error en validación de formulario"
# git commit -m "Actualizado diseño del dashboard"
```

### 5. Subir Tus Cambios al Repositorio

```powershell
# Subir tu rama
git push origin feature/tu-nombre-feature

# Si es la primera vez, Git te dirá que uses:
git push --set-upstream origin feature/tu-nombre-feature
```

## 🔄 Mantener Tu Rama Actualizada

Si el proyecto está avanzando y necesitas los últimos cambios de `main`:

```powershell
# 1. Asegúrate de haber guardado tus cambios
git add frontend/
git commit -m "Mi progreso actual"

# 2. Ir a main y actualizar
git checkout main
git pull origin main

# 3. Volver a tu rama
git checkout feature/tu-nombre-feature

# 4. Traer cambios de main a tu rama
git merge main

# Si hay conflictos, Git te avisará qué archivos resolver
```

## 📤 Entregar Tu Trabajo (Pull Request)

Cuando termines tu feature:

### Opción A: GitHub/GitLab (Recomendado)

1. Sube tu rama: `git push origin feature/tu-nombre-feature`
2. Ve al repositorio en GitHub/GitLab
3. Verás un botón "Create Pull Request" o "Merge Request"
4. Añade descripción de tus cambios
5. Asigna al líder del proyecto para revisión

### Opción B: Avisar al Equipo

Simplemente avisa al líder del proyecto que tu rama está lista:

```
"Mi rama feature/login-page está lista para integrar"
```

## 🆘 Comandos Útiles

### Ver en Qué Rama Estás

```powershell
git branch
# La rama actual aparece con un asterisco (*)
```

### Ver Todas las Ramas

```powershell
git branch -a
```

### Cambiar de Rama

```powershell
git checkout nombre-de-la-rama
```

### Ver Historial de Commits

```powershell
git log --oneline
```

### Descartar Cambios No Guardados

⚠️ **CUIDADO**: Esto borra cambios sin guardar

```powershell
# Descartar cambios de un archivo específico
git checkout -- frontend/src/app/archivo.ts

# Descartar todos los cambios
git reset --hard
```

### Crear una Nueva Rama desde Main

```powershell
git checkout main
git pull origin main
git checkout -b feature/nueva-feature
```

## 🛠️ Comandos de Angular

### Generar Componente

```powershell
ng generate component components/mi-componente
# o versión corta:
ng g c components/mi-componente
```

### Generar Servicio

```powershell
ng generate service services/mi-servicio
# o versión corta:
ng g s services/mi-servicio
```

### Generar Módulo

```powershell
ng generate module modulos/mi-modulo
# o versión corta:
ng g m modulos/mi-modulo
```

### Instalar Paquete NPM

```powershell
npm install nombre-del-paquete

# Ejemplo:
npm install moment
```

## 🎯 Buenas Prácticas

### ✅ Hacer

1. **Commits frecuentes** - Guarda cambios pequeños y frecuentes
2. **Mensajes descriptivos** - Explica qué hiciste
3. **Una feature por rama** - Cada tarea en su propia rama
4. **Probar antes de subir** - Asegúrate de que funciona
5. **Usar nombres claros** - `feature/login` mejor que `feature/cosa`

### ❌ NO Hacer

1. **Trabajar en `main`** - Siempre usa ramas
2. **Commits gigantes** - "Cambios varios" no es descriptivo
3. **Subir archivos innecesarios** - No subir `node_modules/`
4. **Ignorar conflictos** - Resuélvelos antes de continuar
5. **Cambios sin probar** - Verifica que compile antes de subir

## 📝 Estructura de Archivos que Modificarás

```
frontend/
├── src/
│   ├── app/
│   │   ├── components/      ← Tus componentes aquí
│   │   ├── services/        ← Tus servicios aquí
│   │   ├── models/          ← Interfaces y modelos
│   │   └── app.routes.ts    ← Rutas de navegación
│   ├── styles.css           ← Estilos globales + Tailwind
│   └── index.html
├── angular.json
├── package.json
└── tailwind.config.js       ← Configuración de Tailwind
```

## 🐛 Problemas Comunes

### Error: "Changes not staged for commit"

**Solución:** Añade los archivos con `git add frontend/`

### Error: "Your branch is behind"

**Solución:** Actualiza tu rama:
```powershell
git pull origin feature/tu-rama
```

### Error: "Merge conflict"

**Solución:**
1. Abre los archivos en conflicto
2. Busca las líneas con `<<<<<<<`, `=======`, `>>>>>>>`
3. Decide qué código mantener
4. Elimina los marcadores de conflicto
5. Guarda y haz commit:
   ```powershell
   git add .
   git commit -m "Resueltos conflictos"
   ```

### El servidor no arranca (puerto 4200 ocupado)

**Solución:**
```powershell
# Matar proceso en puerto 4200
netstat -ano | findstr :4200
taskkill /PID <número_del_proceso> /F
```

### Error de dependencias

**Solución:**
```powershell
# Reinstalar dependencias
rm -rf node_modules
npm install
```

## 📞 Contacto y Ayuda

Si tienes problemas:
1. Revisa esta guía primero
2. Busca el error en Google
3. Pregunta al equipo
4. Muestra tu pantalla si es necesario

## 🎓 Recursos Adicionales

- **Git**: https://git-scm.com/book/es/v2
- **Angular**: https://angular.dev/
- **Tailwind CSS**: https://tailwindcss.com/docs
- **TypeScript**: https://www.typescriptlang.org/docs/

---

**¡Listo para empezar! 🚀**

Recuerda: trabaja en tu rama, haz commits frecuentes, y no tengas miedo de preguntar.