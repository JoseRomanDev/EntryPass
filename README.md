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

El sistema sigue una arquitectura de **SPA + API REST + Procesamiento Asíncrono**.

1. **Flujo Principal:** `Usuario -> Angular SPA -> Nginx (Proxy) -> Symfony API -> PostgreSQL`.
2. **Procesamiento Asíncrono:** Tareas pesadas (como la generación de códigos QR y el envío de emails) son delegadas a través de **RabbitMQ**, donde un contenedor `worker` (consumidor de Symfony Messenger) las procesa en segundo plano, permitiendo que la respuesta al usuario sea inmediata durante la compra.

### Arquitectura Backend (Hexagonal)
El backend aísla el dominio lógico del sistema de sus implementaciones técnicas:
* **Domain**: Entidades (`User`, `Event`, `Ticket`), interfaces de repositorios y Value Objects. Cero dependencias externas.
* **Application**: Casos de uso (`CommandHandlers`), DTOs y servicios orquestadores.
* **Infrastructure**: Controladores REST, implementación de persistencia (Doctrine), mensajería (RabbitMQ/Messenger) y servicios externos.

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

3. **Acceder a la aplicación:**
   * Frontend / Web: `http://localhost:8080` (A través de Nginx) o `http://localhost:4200` (Dev Server Angular).
   * Base de datos y backend están expuestos internamente y gestionados por Nginx/Docker.

---

##  Estado Actual del Proyecto

> **📌 Hito actual alcanzado:** El desarrollo más reciente ha consolidado el **flujo asíncrono de compra** de entradas. Se ha completado la integración con **RabbitMQ**, la **generación automática de códigos QR y PDFs** unificados para los tickets, y el envío de documentos por correo electrónico. Además, el código se ha refactorizado cumpliendo estrictamente con el patrón de **Puertos y Adaptadores (Arquitectura Hexagonal)**.

Al momento de esta presentación, se han implementado las siguientes bases fundamentales de la arquitectura:

* **Infraestructura Dockerizada (`compose.yml`)**: Los contenedores esenciales (PostgreSQL, RabbitMQ, PHP-FPM, Nginx, Node) están configurados y orquestados listos para desarrollo y puesta en marcha.
* **Capa de Dominio (Backend)**:
  * Entidades principales definidas: `User`, `Event`, `Purchase`, `Ticket`. Se ha refactorizado la lógica para separar la transacción de compra de las entradas individuales.
  * Interfaces de repositorios (Ports) establecidas (`EventRepositoryInterface`, etc.).
* **Capa de Aplicación**:
  * Casos de uso centrales: `CreateEventHandler`, `RegisterUserHandler`, `ProcessPurchaseHandler`.
  * **Puertos de Aplicación**: Definición estricta de interfaces abstractas (`QrCodeGeneratorInterface`, `PdfGeneratorInterface`, `EmailSenderInterface`) para desacoplar la lógica de negocio de librerías concretas.
  * Procesamiento asíncrono: `SendPurchaseEmailHandler` (vía Symfony Messenger) orquesta en segundo plano la generación visual de tickets y envío final.
* **Capa de Infraestructura**:
  * **Adaptadores Hexagonales**: Implementaciones tecnológicas concretas inyectadas transparentemente (`DompdfAdapter`, `EndroidQrCodeAdapter`, `SymfonyEmailSenderAdapter`).
  * Controladores REST funcionales para cada dominio (`User`, `Event`, `Purchase`, `Ticket`).
  * Persistencia configurada utilizando Doctrine ORM y mensajería a través de RabbitMQ.

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
