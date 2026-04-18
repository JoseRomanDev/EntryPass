# Manual de Usuario - EntryPass

Este manual describe el funcionamiento de la plataforma **EntryPass** para los dos perfiles principales: el **Cliente** (Usuario Final) y el **Organizador** (Administrador).

---

##  1. Perfil del Cliente (Compra de Entradas)

El cliente es el usuario que navega por la plataforma para descubrir eventos y adquirir sus entradas.

### 1.1. Registro y Acceso
- Accede a la plataforma y haz clic en "Iniciar Sesión" o "Registrarse".
- Introduce tus datos. Una vez autenticado, tendrás acceso a tu **Perfil Personal**.

### 1.2. Exploración de Eventos
- En la página principal, puedes visualizar todos los eventos disponibles.
- Utiliza la barra de búsqueda para filtrar eventos por nombre en tiempo real.
- Haz clic en la tarjeta de un evento para ver sus detalles (fecha, aforo, descripción).

### 1.3. Proceso de Compra (Checkout)
1. Haz clic en el botón **"Comprar Entrada"**.
2. Si no has iniciado sesión, el sistema te redirigirá al login y te devolverá al proceso de compra automáticamente.
3. Se abrirá un **Modal de Checkout**. Selecciona la cantidad de entradas (máximo 4 por evento).
4. El sistema validará el stock en tiempo real.
5. Confirma la compra. Recibirás una confirmación visual instantánea.
6. El sistema procesará tu pedido de forma asíncrona y recibirás un email con tus entradas (PDF).

### 1.4. Mis Entradas y Códigos QR
- Accede a tu sección de **Perfil > Mis Entradas**.
- Verás un listado de tus compras.
- Al desplegar una compra, podrás ver cada **Código QR** individual. Este es el código que deberás presentar en la entrada del evento.

---

##  2. Perfil del Organizador (Administración)

El organizador gestiona la oferta de eventos y realiza el control de acceso.

### 2.1. Acceso Administrativo
- Utiliza las credenciales de administrador (ej: `admin@entrypass.com`).
- Una vez dentro, verás la opción **"Administración"** en la barra de navegación.

### 2.2. Gestión de Eventos (Panel de Control)
Desde el panel de gestión puedes:
- **Crear Eventos**: Define título, descripción, fecha, imagen y aforo total.
- **Editar**: Modifica la información de eventos existentes.
- **Baja Lógica (Eliminar)**: Puedes desactivar eventos. El sistema los marcará como eliminados pero mantendrá la integridad de las compras ya realizadas.

### 2.3. Control de Acceso (Portería)
- El día del evento, el organizador puede acceder a la herramienta de **Validación**.
- Al escanear el QR de un cliente (o validar su Hash manualmente), el sistema verificará:
    - ✅ **Verde**: Entrada válida. El sistema la marca como "Usada" (`used`).
    - ❌ **Rojo**: Entrada ya utilizada anteriormente o inexistente.
- Este proceso garantiza un control de aforo real y evita el fraude por duplicidad.

---

##  3. Soporte Técnico Técnico y Troubleshooting

- **QR no carga**: Asegúrate de tener conexión a internet. Los QRs se generan de forma securizada a partir de los datos en la base de datos.
- **Email no recibido**: Revisa tu bandeja de SPAM. El envío puede tardar unos segundos debido al procesamiento asíncrono en colas (RabbitMQ).
