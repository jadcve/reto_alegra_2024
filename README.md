# 🍽️ Reto Técnico PHP - Jornada de Almuerzo ¡Gratis! 🍽️

¡Bienvenido al repositorio del reto técnico para Alegra! En este proyecto, desarrollaremos una aplicación para un restaurante que dona comidas aleatorias a los residentes de la región. La aplicación debe gestionar la preparación de platos, el inventario de ingredientes y permitir pedidos masivos, todo bajo una arquitectura de microservicios utilizando Docker.

## 🛠️ Tecnologías Utilizadas

- **PHP** (Laravel)
- **Docker**
- **MySQL**
- **PostgreSQL**
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

### Componentes de la Interfaz

- **Botón para Pedir un Plato**: Permite realizar pedidos masivos de platos.
- **Órdenes en Preparación**: Muestra las órdenes actuales en la cocina.
- **Inventario de Ingredientes**: Visualiza las cantidades disponibles en la bodega.
- **Historial de Compras**: Registra las compras realizadas en la plaza de mercado.
- **Historial de Pedidos**: Lista los pedidos realizados a la cocina.
- **Recetas Disponibles**: Muestra las recetas con sus ingredientes y cantidades.

## 🧩 Estructura del Proyecto

El proyecto está organizado en varios microservicios, cada uno con su propio `Dockerfile` y `POM.xml`:

1. **Microservicio `customer`**:
    - Puerto: `8080`
    - Base de datos: MySQL

2. **Microservicio `account`**:
    - Puerto: `8081`
    - Base de datos: PostgreSQL

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

- **Frontend**: `http://localhost:4200`
- **Microservicio Customer**: `http://localhost:8080`
- **Microservicio Account**: `http://localhost:8081`

## 📚 Recursos Adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Docker](https://docs.docker.com/)
- [Documentación de Angular](https://angular.io/docs)


> ¡Diviértete! “Disfrutar con el trabajo es hallar la fuente de la juventud.” - Pearl S. Buck
