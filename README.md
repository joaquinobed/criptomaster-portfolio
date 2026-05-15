# CriptoMaster - Gestor de Portafolio de Criptomonedas

CriptoMaster es una aplicación web ligera y eficiente diseñada para rastrear y gestionar inversiones en criptomonedas. Permite a los usuarios monitorear su balance total, calcular rentabilidades y visualizar la distribución de sus activos en diferentes exchanges.

## 🚀 Características

- **Sincronización de Precios:** Conexión en tiempo real con la API de Binance para obtener precios actualizados.
- **Control Manual:** Opción de establecer precios manualmente y bloquearlos para evitar que se sobrescriban durante la sincronización automática.
- **Panel de Administración:** Gestión completa (CRUD) de Criptomonedas y Exchanges.
- **Dashboard Interactivo:**
  - Resumen de balance total en USD.
  - Cálculo de utilidad y porcentaje de variación.
  - Gráficos de distribución por moneda (Donut Chart).
  - Listado detallado de inversiones con indicadores visuales de rendimiento.
- **Seguridad:** Implementación de sentencias preparadas (Prepared Statements) para prevenir inyecciones SQL.

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 8.x
- **Base de Datos:** SQLite (mediante PHP PDO)
- **Frontend:**
  - HTML5 / CSS3
  - Bootstrap 5 (Styling)
  - JavaScript (Vanilla & jQuery para manipulación del DOM)
  - Chart.js (Visualización de datos)
  - FontAwesome 6 (Iconografía)
- **API Externa:** Binance API (Precios de mercado)

## 📋 Estructura del Proyecto

- `/api`: Endpoints que manejan la lógica de negocio y comunicación con la base de datos.
- `/js`: Lógica del lado del cliente (app.js).
- `/css`: Estilos personalizados (index.css).
- `/img`: Almacenamiento de iconos de criptomonedas.
- `config.php`: Configuración de la base de datos e inicialización del esquema.
- `migrate.php`: Script de migración inicial para poblar datos base.
- `index.php`: Interfaz de usuario principal.

## 🔧 Instalación y Configuración

1. **Servidor:** Requiere un servidor con PHP 8 o superior (XAMPP, Laragon, Apache).
2. **Base de Datos:** El sistema utiliza SQLite, por lo que se creará automáticamente el archivo `database.sqlite` al iniciar.
3. **Migración Inicial:** Ejecute el archivo `migrate.php` una sola vez para cargar las monedas y configuraciones iniciales.
   - Acceda vía navegador: `http://localhost/tu-carpeta/migrate.php`
4. **Permisos:** Asegúrese de que el servidor tenga permisos de escritura en el directorio raíz para crear y modificar el archivo de base de datos.

## ⚠️ Advertencia de Seguridad

- **Entorno Local:** Este script está diseñado exclusivamente para ser ejecutado en **entornos locales** (localhost).
- **Sin Autenticación:** El sistema **no implementa** controles de acceso mediante usuario y contraseña. Cualquier persona con acceso a la red local donde se ejecute el servidor podrá ver y modificar los datos.
- **Responsabilidad:** Utilice este script bajo su propia responsabilidad. No se recomienda su despliegue en servidores públicos sin añadir capas de seguridad adicionales.

## 📝 Notas de Versión

- **v1.1**: Añadido panel de administración de monedas y exchanges con validaciones de integridad referencial.
- **v1.0**: Sistema base de portafolio con sincronización Binance.

---
Desarrollado con enfoque en simplicidad y funcionalidad tradicional.
