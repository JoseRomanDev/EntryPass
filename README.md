# EntryPass

**EntryPass** es un proyecto personal desarrollado como Trabajo de Fin de Grado (TFG) para el ciclo de Desarrollo de Aplicaciones Web (DAW). 

Consiste en una **plataforma web de compra de entradas para eventos** (similar a Eventbrite o Ticketmaster) que permite a los usuarios registrarse, explorar eventos, comprar entradas y recibir un código QR único para acceder al evento.

El objetivo académico de este proyecto es demostrar el uso y dominio de una **arquitectura moderna**, basada en contenedores, un frontend SPA, un backend con arquitectura hexagonal y procesamiento asíncrono utilizando colas de mensajes.

---

##  Stack Tecnológico

El proyecto está construido utilizando tecnologías modernas y estándares de la industria:

### Frontend
* **Angular** (SPA basado en features)
* Comunicación fluida con API REST

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

##  Guía de Instalación y Puesta a Punto

El proyecto está completamente dockerizado para facilitar su despliegue y desarrollo local.

### Prerrequisitos
* [Docker](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/) instalados en el sistema local.

### Pasos
1. **Clonar el repositorio:**
   ```bash
   git clone <url-del-repositorio>
   cd EntryPass
   ```

2. **Levantar la infraestructura con Docker Compose:**
   En la raíz del proyecto, ejecuta el siguiente comando:
   ```bash
   docker compose up -d --build
   ```
   *Esto construirá las imágenes personalizadas y levantará los servicios: `postgres`, `rabbitmq`, `php` (backend), `nginx` (web), `node` (frontend server) y el `worker`.*

3. **Instalar dependencias y librerías externas:**
   Aunque herramientas base como Composer, Symfony CLI y Angular CLI ya están incluidas dentro de las imágenes de Docker (PHP y Node), es indispensable descargar las dependencias externas que utiliza el sistema. Esto instalará paquetes importantes como **LexikJWT** (autenticación), **Endroid QR Code** (generación de códigos QR) y **Dompdf** (creación de PDFs).
   ```bash
   # Instalar librerías externas del Backend
   docker compose exec php composer install

   # Instalar librerías externas del Frontend
   docker compose exec node npm install
   ```

4. **Configurar claves JWT:**
   Como hacemos uso de la librería externa de JWT, es necesario generar el par de claves SSL para la firma y verificación de los tokens:
   ```bash
   docker compose exec php php bin/console lexik:jwt:generate-keypair
   ```

5. **Acceder a la aplicación:**
   * Frontend / Web: `http://localhost:8080` (A través de Nginx) o `http://localhost:4200` (Dev Server Angular).
   * Base de datos y backend están expuestos internamente y gestionados por Nginx/Docker.



---

##  Implementaciones Futuras (Roadmap)

El desarrollo continúa de forma iterativa hacia el cumplimiento del flujo completo y la finalización del TFG. Las próximas fases incluyen:

1. **Endpoint de Validación de QRs (Portería):** 
   * Desarrollo de un endpoint protegido (ej. `/api/tickets/validate`) para verificar presencialmente la autenticidad de una entrada escaneada y marcar su estado como utilizado (`valid` -> `used`).
2. **Pasarela de Pagos (Stripe):**
   * Incorporar una simulación de cobro realista en el backend antes de autorizar y encolar la creación definitiva de los tickets.
3. **Frontend SPA (Angular):**
   * Consolidación de la comunicación con la API e integración real del Auth JWT.
   * Estructuración del proyecto en Features (`auth`, `events`, `tickets`, `profile`).
   * Pantallas responsive, listado público de eventos, flujo interactivo de compra en la UI y panel de "Mis Entradas" para los usuarios.
