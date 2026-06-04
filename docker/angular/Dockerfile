# Etapa de desarrollo
FROM node:20-alpine as development

WORKDIR /app

# Copiar package.json
COPY frontend/package*.json ./

# Instalar dependencias
RUN npm install

# Copiar código fuente
COPY frontend .

EXPOSE 4200

CMD ["npm", "start", "--", "--host", "0.0.0.0"]

# Etapa de build para producción
FROM node:20-alpine as build

WORKDIR /app

COPY frontend/package*.json ./
RUN npm ci

COPY frontend .
RUN npm run build -- --configuration production

# Etapa de producción con Nginx
FROM nginx:alpine as production

# Copiar configuración de Nginx
COPY docker/nginx/frontend.conf /etc/nginx/conf.d/default.conf

# Copiar build de Angular
COPY --from=build /app/dist/frontend/browser /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
