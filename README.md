# EntryPass

**EntryPass** es un proyecto personal desarrollado como Trabajo de Fin de Grado (TFG) para el ciclo de Desarrollo de Aplicaciones Web (DAW). 

Consiste en una **plataforma web de compra de entradas para eventos** (similar a Eventbrite o Ticketmaster) que permite a los usuarios registrarse, explorar eventos, comprar entradas y recibir un código QR único para acceder al evento.

El objetivo académico de este proyecto es demostrar el uso y dominio de una **arquitectura moderna**, basada en contenedores, un frontend SPA, un backend con arquitectura hexagonal y procesamiento asíncrono utilizando colas de mensajes.

---

##  Stack Tecnológico

El proyecto está construido utilizando tecnologías modernas y estándares de la industria:

### Frontend
* **Angular 19** (SPA basado en features)
* **Diseño High-Fidelity**: Interfaz moderna con estética Premium Dark, paleta Cyan/Teal y componentes altamente interactivos.
* Comunicación fluida con API REST y gestión de estado reactiva.

### Backend
* **Symfony** (API REST)
* Arquitectura Hexagonal (Ports & Adapters)
* Symfony Messenger para la gestión de encolado de procesos

### Infraestructura y Servicios
* **Docker & Docker Compose**: Contenerización completa de la aplicación.
* **Nginx**: Servidor web principal y Reverse Proxy.
* **PostgreSQL**: Base de datos relacional para la persistencia de datos.
* **RabbitMQ**: Broker de mensajería para desacoplar tareas pesadas.

---

##  Arquitectura del Sistema

El sistema cuenta con una arquitectura robusta de **SPA + API REST + Procesamiento Asíncrono**. Actualmente, el proyecto ha consolidado un gran hito: implementar el **flujo asíncrono de compra de entradas** cumpliendo estrictamente con el patrón de **Puertos y Adaptadores (Arquitectura Hexagonal)**.

### Flujo Global de la Aplicación
1. **Flujo Principal:** `Usuario -> Angular SPA -> Nginx (Proxy) -> Symfony API -> PostgreSQL`.
2. **Procesamiento Asíncrono:** Las tareas pesadas (generación automática de códigos QR, PDFs unificados para los tickets y envío de emails) se delegan a través de **RabbitMQ**. Un contenedor `worker` (consumidor de Symfony Messenger) las procesa en segundo plano para garantizar una respuesta inmediata al usuario.

### Arquitectura Backend (Hexagonal)
El backend aísla el dominio lógico del sistema de sus implementaciones tecnológicas. Hasta la fecha se han implementado de forma firme las siguientes capas:

* **Capa de Dominio (Domain)**: (Cero dependencias externas).
  * Entidades principales definidas: `User`, `Event`, `Purchase`, `Ticket`. La lógica diferencia claramente la transacción general de la compra frente a las entradas individuales.
  * Interfaces de repositorios (Ports) establecidas (`EventRepositoryInterface`, etc.).
* **Capa de Aplicación (Application)**:
  * Casos de uso centrales: `CreateEventHandler`, `RegisterUserHandler`, `ProcessPurchaseHandler`.
  * **Puertos de Aplicación**: Definición estricta de interfaces abstractas (`QrCodeGeneratorInterface`, `PdfGeneratorInterface`, `EmailSenderInterface`) para desacoplar la lógica de negocio de librerías concretas.
  * Procesamiento asíncrono: `SendPurchaseEmailHandler` (vía Symfony Messenger) orquesta la generación visual de los tickets y el envío de estos.
* **Capa de Infraestructura (Infrastructure)**:
  * **Adaptadores Hexagonales**: Implementaciones tecnológicas concretas inyectadas transparentemente (`DompdfAdapter`, `EndroidQrCodeAdapter`, `SymfonyEmailSenderAdapter`).
  * Controladores REST funcionales para cada dominio principal (`User`, `Event`, `Purchase`, `Ticket`).
  * Persistencia configurada usando Doctrine ORM y colas a través de RabbitMQ.

---

## Guía de Instalación y Puesta a Punto

El proyecto cuenta con un `Makefile` y está completamente dockerizado para facilitar su despliegue.

### Prerrequisitos
*   [Docker Desktop](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/).
*   Entorno habilitado para ejecutar `make` (en Windows puedes usar WSL2 o Git Bash).

### Pasos de Instalación (Desde Cero)

1.  **Clonar el repositorio:**
    ```bash
    git clone <url-del-repositorio>
    cd EntryPass
    ```

    > [!NOTE]
    > Asegúrate de estar en una terminal con permisos de administrador o dentro de un entorno que soporte `make` (WSL, Git Bash o PowerShell con Make instalado).

2.  **Levantar e inicializar los contenedores:**
    Si es la primera vez que ejecutas el proyecto o no tienes las imágenes construidas:
    ```bash
    make build
    make up
    ```

    > [!IMPORTANT]
    > El primer `make build` puede tardar varios minutos dependiendo de tu conexión, ya que descarga imágenes de PHP, PostgreSQL, Node y RabbitMQ.

3.  **Instalar dependencias de las librerías:**
    Una vez los contenedores estén corriendo, descarga los paquetes de PHP y Node:
    ```bash
    # Backend (Symfony + LexikJWT + Dompdf + EndroidQR)
    docker compose exec php composer install

    # Frontend (Angular + Core UI)
    docker compose exec node npm install
    ```

4.  **Generar claves JWT:**
    Obligatorio para que el sistema de login funcione:
    ```bash
    docker compose exec php php bin/console lexik:jwt:generate-keypair
    ```

    > [!WARNING]
    > Si el comando anterior falla, verifica que el contenedor `php` esté en estado *Running*. Puedes comprobarlo con `docker ps`.

5.  **Insertar usuario administrador (Seed):**
    Para poder probar el panel de administración en cualquier dispositivo, inserta el usuario por defecto:
    ```bash
    make seed
    ```

    > [!NOTE]
    > Esto creará el usuario `admin@entrypass.com` con la contraseña `Admin123!`. Si el usuario ya existe, el comando simplemente te informará de ello.

### Notas y Solución de Problemas

> [!NOTE]
> **Comandos Útiles:** Usa `make build` si realizas cambios en los Dockerfiles y `make down` para detener los servicios.

*   **Error "compose not found"**: Asegúrate de usar `docker compose` (con espacio) en lugar de `docker-compose` (con guion) si estás en versiones modernas de Docker. El comando `make up` gestiona esto automáticamente por ti.
*   **Permisos de Claves JWT**: Si recibes errores de autenticación, verifica que las claves en `backend/config/jwt/` tengan los permisos adecuados o vuelve a generarlas.
*   **Acceso Web**: 
    *   Frontend: [http://localhost:8080](http://localhost:8080) (Nginx)
    *   Angular Dev: [http://localhost:4200](http://localhost:4200)

---

##  Pruebas de la API (Postman)

En la carpeta `/postman` del proyecto se incluyen las colecciones exportadas con todos los endpoints disponibles de la aplicación (Eventos, Autenticación, Compras, etc.). 

Para probar la API localmente usando estos archivos:
1. Abre tu aplicación de **Postman**.
2. En tu Workspace, haz clic en el botón **Import** (generalmente arriba a la izquierda) o arrastra y suelta los archivos.
3. Selecciona los archivos `.json` que se encuentran dentro de la carpeta `postman/` de este proyecto.
4. Esto añadirá automáticamente las colecciones preconfiguradas para interactuar con la API en `http://localhost:8080`.

###  Instrucciones de Uso y Consideraciones
Para probar correctamente los endpoints en Postman, ten en cuenta las siguientes reglas de negocio del backend:

* **Autenticación (JWT Bearer):** Gran parte de la API está protegida. Para acceder a rutas privadas u operaciones que requieran un usuario, primero debes realizar una petición al endpoint de **Login**. Copia el token devuelto y en tu petición coloca ese valor en la pestaña **Authorization** seleccionando el tipo **Bearer Token**.
* **Gestión de Roles (`ROLE_ADMIN` vs `ROLE_USER`):** El sistema verifica el rol del usuario que realiza la petición.
  * Solo los administradores (`ROLE_ADMIN`) están autorizados a realizar acciones de gestión globales, como **crear, editar o eliminar Eventos**.
  * Los usuarios normales (`ROLE_USER`) tienen acceso a explorar eventos e iniciar flujos de **Compras de Entradas** de cara al público general.
* **Procesamiento de Compras:** Cuando envíes un `POST` de prueba para comprar una entrada en la colección de Purchases, recuerda que la respuesta del servidor es rápida e indica que ha aceptado la petición (Cód. 202). El procesamiento de PDFs, códigos QR y del envío del e-mail de compra lo realiza RabbitMQ en segundo plano como parte de la Arquitectura Hexagonal.

---

##  Acceso Administrativo (Testeo)

Para realizar pruebas en el panel de gestión de eventos, se ha habilitado un usuario con rol de administrador (`ROLE_ADMIN`):

*   **Email:** `admin@entrypass.com`
*   **Contraseña:** `Admin123!`

> [!NOTE]
> Este usuario administrador es una semilla (seed) pre-configurada para facilitar el testeo inicial del panel de gestión sin necesidad de crear uno manualmente en la base de datos.

---


---

## Hitos y Evolución del Proyecto

Puedes consultar el detalle del proyecto y las guías de uso en los siguientes enlaces:

**[Manual de Usuario (Clientes y Administradores)](docs/manual_usuario.md)**  
**[Informe de Mejoras (Fase Técnica)](docs/informe_mejoras.md)**

### Hitos Alcanzados Recientemente

* **Frontend SPA Completado Plenamente (Lógica de Compra y Perfil):**
  * Se implementó exitosamente el flujo de Checkout end-to-end conectando Angular con la API Symfony.
  * Interfaz de Alta Fidelidad en el Modal de compra con gestión de estado a través de _Signals_ de Angular (selectores de cantidad y validación de stock visual).
  * Panel de "Mis Entradas" finalizado. Ahora expone y renderiza al vuelo los Códigos QR de cada entrada de forma individualizada a partir del Hash securitizado en base de datos.
  * Se incorporó el redireccionamiento fluído de autenticación (`Guards`) en la pasarela, para recordar qué intentaba comprar un visitante y redirigirlo inmediatamente tras el Login sin perder recorrido.
* **Endpoint de Validación de QRs (Portería):** 
  * Se ha desarrollado e implementado un endpoint protegido (`/api/tickets/validate`) para verificar presencialmente la autenticidad de una entrada escaneada y marcar su estado como consumido (`valid` -> `used`), asegurando el ecosistema contra fraude.
* **Simulación de Pasarela de Pagos:**
  * Incorporación de una simulación de cobro realista en el backend antes de autorizar y encolar la creación definitiva de los tickets. Implementado mediante Patrón de Diseño Hexagonal (Ports & Adapters) garantizando desacoplamiento total. Todo fuertemente respaldado por testing en **PHPUnit** y tests funcionales para asegurar la inviolabilidad de los datos en fallos transaccionales.
* **Rediseño Estético de Alta Fidelidad (UI/UX):**
  * Transformación integral de la identidad visual de la aplicación. Migración a un sistema de diseño basado en fondo negro absoluto y acentos Cyan (`#26b1c4`), con una arquitectura de componentes optimizada para una experiencia fluida y premium.
* **Autenticación Resiliente e Interceptores:**
  * Implementación de Interceptores HTTP reactivos para gestionar el cierre de sesión transparente frente a tokens caducados, reparando errores `401 Unauthorized` en rutas públicas y eliminando pantallas de carga infinitas.
  * Mejoras drásticas de UX en componentes: visualización de contraseña mediante SVG en login y redirecciones automáticas (`Router Guards`).
* **Simulación Avanzada de Pasarela de Pagos (Stripe Mock):**
  * Construcción de una interfaz de Checkout hiperrealista encapsulando _Stripe Elements_ directamente en Angular, dotando a la simulación de confirmación y procesamiento con _fake delays_ que consolidan un aspecto profesional.
* **Panel de Administración Refinado:**
  * Refactorización del **Dashboard** administrativo eliminando por completo alertas nativas del navegador a favor de modales customizados de Alta y Baja Lógica. Sustitución total de emojis OS por iconografía SVG elegante adaptada al *Dark Mode*.

##  Implementaciones Futuras (Roadmap)

El desarrollo ha alcanzado la madurez funcional de todos los flujos base y se acerca al final del TFG. Las próximas fases clave y últimos pasos para cerrar el proyecto se centran en el aseguramiento de calidad:

1. **Optimización y Testeo Global (Fase 7):**
   * Ampliación del Testing funcional automatizado global de todos los componentes Hexagonales del Backend.
   * Testing funcional del flujo de componentes expuestos en el Frontend SPA.
   * Auditoría de accesibilidad y optimización de performance para los motores de Chrome (Lighthouse).
2. **Despliegue de Producción (Opcional):**
   * Documentación del pasaje hacia arquitecturas Cloud o servicios VPS para demostrar el empaquetado del ecosistema Docker generado.
