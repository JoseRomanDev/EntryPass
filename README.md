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

Al momento de esta presentación, se han implementado las siguientes bases fundamentales de la arquitectura:

* **Infraestructura Dockerizada (`compose.yml`)**: Los contenedores esenciales (PostgreSQL, RabbitMQ, PHP-FPM, Nginx, Node) están configurados y orquestados listos para desarrollo puestas en marchas.
* **Capa de Dominio (Backend)**:
  * Entidades principales definidas: `User`, `Event`.
  * Interfaces de repositorios (Ports) establecidas (`EventRepositoryInterface`, `UserRepositoryInterface`).
* **Capa de Aplicación**:
  * Implementación de los Handlers de Casos de Uso centrales: `CreateEventHandler`, `RegisterUserHandler`.
* **Capa de Infraestructura**:
  * Persistencia configurada utilizando Doctrine ORM con sus respectivos repositorios (`DoctrineEventRepository`, `DoctrineUserRepository`).
  * Controladores REST iniciales (`RegisterUserController`, `GetUserProfileController`) en fase de final del desarrollo.

---

##  Implementaciones Futuras (Roadmap)

El desarrollo continúa de forma iterativa hacia el cumplimiento del flujo completo y la finalización del TFG. Las próximas fases incluyen:

1. **Gestión Completa de Entradas (Tickets):** Creación de la entidad `Ticket`, su UUID asociado, su ciclo de vida y relacionarlos con `User` y `Event`.
2. **Workers y Asincronía (Compra):**
   * Integración de un Worker para la generación automática de **código QR único** (basado en UUID) mediante Symfony Messenger tras la compra concurrente.
   * Envío asíncrono de **correos electrónicos** confirmando la entrada con el código QR adjunto.
3. **Flujo API REST y Autenticación:** Completar el controlador de usuario (login, gestión de sesión/tokens) y consolidar los endpoints del listado de eventos y simulación de compra final.
4. **Validación de Código QR:** Endpoint protegido para verificar la validez de un ticket escaneado presencialmente en el evento (cambio de status: `valid` -> `used`).
5. **Frontend SPA (Angular):**
   * Estructuración del proyecto basado en Features (`auth`, `events`, `tickets`, `profile`).
   * Pantallas responsive, consumo final de la API e implementación en UI de las entradas compradas por el usuario.
