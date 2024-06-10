# 🍽️ Reto Técnico PHP - Jornada de Almuerzo ¡Gratis! 🍽️

¡Bienvenido al repositorio del reto técnico para Alegra! En este proyecto, desarrollaremos una aplicación para un restaurante que dona comidas aleatorias a los residentes de la región. La aplicación debe gestionar la preparación de platos, el inventario de ingredientes y permitir pedidos masivos, todo bajo una arquitectura de microservicios utilizando Docker.

## 🛠️ Tecnologías Utilizadas

- **PHP** (Laravel)
- **Docker**
- **MySQL**
- **Angular** (Frontend)

## 🎯 Objetivo

El objetivo de este proyecto es desarrollar un sistema funcional y eficiente para manejar la donación de comidas de manera aleatoria, asegurando la disponibilidad de ingredientes y la correcta preparación de los platos.

## 🚀 Descripción del Proyecto

### Funcionalidades Principales

1. **Pedido de Platos**:
    - El gerente del restaurante puede presionar un botón para enviar una orden a la cocina para preparar un nuevo plato.
    - La cocina selecciona aleatoriamente una receta de una lista predefinida de 6 recetas.

2. **Gestión de Inventario**:
    - La bodega de alimentos inicia con una cantidad de 5 unidades por ingrediente.
    - La cocina solicita los ingredientes necesarios a la bodega.
    - Si la bodega no tiene los ingredientes, los compra en la plaza de mercado.

3. **Interacción con la Plaza de Mercado**:
    - La plaza de mercado está disponible en `https://recruitment.alegra.com/api/farmers-market/buy`.
    - La compra es exitosa si la plaza devuelve una cantidad mayor a cero de los ingredientes solicitados.

4. **Tarea Programada**:
    - Una tarea programada se ejecuta cada minuto para verificar el estado de las órdenes y actualizar los ingredientes en la bodega si han sido comprados en la plaza de mercado.

### Componentes de la Interfaz

La interfaz de usuario permite la creación y seguimiento de órdenes de manera eficiente:

- **Formulario de Creación de Orden**: Permite especificar la cantidad de platos a preparar y enviar la orden a la cocina.
- **Listado de Órdenes**: Muestra las órdenes en preparación y las despachadas, incluyendo la cantidad, el estado y la fecha de cada orden.

![Captura de Pantalla](./ruta/a/la/captura.png)

### Estructura de la Interfaz

1. **Crear Orden**:
    - Campo para especificar la cantidad de platos a preparar.
    - Botón para enviar la orden a la cocina.

2. **Listado de Órdenes**:
    - Muestra las órdenes con la siguiente información: cantidad, estado (en proceso, despachada), fecha y un indicador visual del estado.

## 🧩 Estructura del Proyecto

El proyecto está organizado en varios microservicios, y se comunica de la siguiente manera:

1. **Frontend**:
    - Accesible en: `http://localhost:8081/orders`
    - Se comunica con los microservicios de cocina y bodega.

2. **Microservicio de Cocina**:
    - Recibe las órdenes desde el frontend.
    - Selecciona aleatoriamente un menú.
    - Solicita los ingredientes necesarios al microservicio de bodega.

3. **Microservicio de Bodega**:
    - Administra el inventario de ingredientes.
    - Proporciona los ingredientes a la cocina.
    - Si no tiene los ingredientes necesarios, los compra en la plaza de mercado.

4. **Plaza de Mercado**:
    - Endpoint externo en: `https://recruitment.alegra.com/api/farmers-market/buy`
    - Provee ingredientes a la bodega cuando se solicitan.

### Tarea Programada

Una tarea programada se ejecuta cada minuto para:

- Verificar el estado de las órdenes pendientes.
- Actualizar los ingredientes en la bodega si han sido comprados en la plaza de mercado.
- Despachar las órdenes una vez que todos los ingredientes están disponibles.

## ⚙️ Cómo Ejecutar el Proyecto

### Prerrequisitos

- Docker instalado
- Docker Compose

### Pasos para Levantar los Microservicios

1. **Clonar el Repositorio**:
    ```bash
    git clone https://github.com/usuario/repo.git
    cd repo
    ```

2. **Levantar los Contenedores**:
    ```bash
    docker-compose up --build
    ```

### URLs de Acceso

- **Frontend**: `http://localhost:8081/orders`

## 📚 Recursos Adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Docker](https://docs.docker.com/)
- [Documentación de Angular](https://angular.io/docs)

## 📝 Configuración de Docker Compose

El archivo `docker-compose.yml` incluye la configuración necesaria para levantar los microservicios y las bases de datos. Aquí está el contenido del archivo:

```yaml
version: '3.8'
services:
  gerente-app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: gerente-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - .:/var/www
    networks:
      - app-network

  gerente-web:
    build:
      context: .
      dockerfile: Dockerfile.nginx
    container_name: gerente-web
    restart: unless-stopped
    ports:
      - "8081:80"
    networks:
      - app-network
    depends_on:
      - gerente-app

  gerente-db:
    image: mysql:5.7
    container_name: gerente-db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: gerente
    ports:
      - "33007:3306"
    volumes:
      - gerente-dbdata:/var/lib/mysql
    networks:
      - app-network

  redis-gerente:
    image: redis:alpine
    container_name: redis-gerente
    restart: unless-stopped
    ports:
      - "6378:6379"
    networks:
      - app-network

networks:
  app-network:
    driver: bridge

volumes:
  gerente-dbdata:
    driver: local
