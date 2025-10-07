# 🍽️ Reserva Restaurante

Aplicación web desarrollada en **PHP**, **MySQL** y **Bootstrap 5**, que
permite gestionar **reservas de un restaurante**.  
Incluye funcionalidades completas de **listado, alta, modificación,
eliminación y ordenación** de reservas.

------------------------------------------------------------------------

## 📂 Estructura del proyecto

    /reserva-restaurante/
    │
    ├── index.php              → Página principal (listado, modificar, eliminar, edición en línea)
    ├── nuevo_cliente.php      → Formulario para añadir nuevas reservas
    ├── /css/
    │   └── style.css          → Estilos personalizados con fondo, transparencia y glassmorphism
    ├── /img/
    │   └── restaurante.png    → Imagen de fondo (restaurante)
    └── README.md              → Este archivo

------------------------------------------------------------------------

## ⚙️ Requisitos

-   **Servidor PHP 8.1 o superior**  
-   **MySQL 5.7 o superior**  
-   Extensión `mysqli` habilitada  
-   Navegador moderno compatible con `backdrop-filter` (para el efecto
    vidrio)

------------------------------------------------------------------------

## 🖥️ Uso de la aplicación

### 🔹 Página principal: index.php

- Muestra el listado de reservas existentes.

- Permite **ordenar** por nombre, teléfono, email, fecha, hora o número de comensales.

- Incluye botones:

  -  🗑️ Eliminar reserva

  -  ✏️ Modificar (edición en línea)

  -  ➕ Nueva reserva → lleva a nuevo_cliente.php

### 🔹 Añadir nueva reserva: nuevo_cliente.php

- Formulario con los campos:

  - Nombre

  - Teléfono

  - Email

  - Fecha

  - Hora

  - Comensales

- Valida que los campos sean obligatorios y que no haya reservas duplicadas
para el mismo email + fecha + hora.

------------------------------------------------------------------------

## 🎨 Diseño (CSS)

El archivo `style.css` aplica: 

- Fondo con imagen (`/img/banca-turing.png`)
- Efecto **glassmorphism** con:
`css   background-color: rgba(255, 255, 255, 0.18);   backdrop-filter: blur(6px);   border-radius: 12px;` 
- Tablas transparentes y con texto claro.
- Botones `Bootstrap` personalizados (`.btn-success`, `.btn-primary`,
`.btn-danger`).

------------------------------------------------------------------------

## ⚡ Características técnicas

- CRUD completo (Create, Read, Update, Delete) con control de errores.
- Uso de mysqli preparado (para evitar inyecciones SQL).
- Ordenación de columnas mediante parámetros GET seguros.
- Interfaz responsive y moderna con Bootstrap 5.3.8.
- Totalmente compatible con AlwaysData.

------------------------------------------------------------------------

## 📸 Captura de ejemplo

![alt text](./src/img/Captura%20de%20Reserva.png)
![alt text](./src/img/Captura%20de%20Nueva%20reserva.png)

------------------------------------------------------------------------

## 👨‍💻 Autor

**Alejandro García Gómez**

Proyecto educativo — Ejercicio Reserva Restaurante Mejorado (2025)
Desarrollado para prácticas de bases de datos y PHP.