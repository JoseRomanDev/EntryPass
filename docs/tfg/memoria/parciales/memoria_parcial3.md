---
# ENTRYPASS
## Plataforma Web de Gestión y Compra de Entradas para Eventos

---

**Trabajo de Fin de Grado**  
**Ciclo Formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW)**

---

| Campo | Información |
|-------|-------------|
| **Autor** | José Manuel Román Navarro |
| **Correo electrónico** | josemanuel.rnav@gmail.com |
| **Teléfono** | 695 386 759 |
| **Curso** | 2º DAW |
| **Modalidad** | Individual |
| **Tiempo estimado** | 250 horas |
| **Fecha de entrega** | Abril 2026 |

---

## ÍNDICE

1. [Introducción y Justificación](#1-introducción-y-justificación)
2. [Objetivos del Proyecto](#2-objetivos-del-proyecto)
3. [Público Objetivo (Target)](#3-público-objetivo-target)
   - 3.1. [Perfil del usuario final](#31-perfil-del-usuario-final)
   - 3.2. [Perfil del organizador de eventos](#32-perfil-del-organizador-de-eventos)
   - 3.3. [Caso de uso típico: Flujo de compra y acceso](#33-caso-de-uso-típico-flujo-de-compra-y-acceso)
4. [Tecnologías a Emplear y Justificación](#4-tecnologías-a-emplear-y-justificación)
   - 4.1. [Frontend](#41-frontend)
   - 4.2. [Backend](#42-backend)
   - 4.3. [Infraestructura y servicios](#43-infraestructura-y-servicios)
   - 4.4. [Herramientas de desarrollo](#44-herramientas-de-desarrollo)
5. [Estado del Arte](#5-estado-del-arte)
   - 5.1. [Descripción del problema o necesidad detectada](#51-descripción-del-problema-o-necesidad-detectada)
   - 5.2. [Análisis de soluciones existentes](#52-análisis-de-soluciones-existentes)
   - 5.3. [Limitaciones de las soluciones actuales](#53-limitaciones-de-las-soluciones-actuales)
   - 5.4. [Margen de mejora y propuesta de valor](#54-margen-de-mejora-y-propuesta-de-valor)
6. [Arquitectura del Sistema](#6-arquitectura-del-sistema)
7. [Bases de Datos y Modelo](#7-bases-de-datos-y-modelo)
8. [Lógica del Flujo de Navegación](#8-lógica-del-flujo-de-navegación)
9. [Manual de Uso (Aproximación UI)](#9-manual-de-uso-aproximación-ui)
10. [Metodología de Desarrollo](#10-metodología-de-desarrollo)
11. [Estado Actual del Proyecto](#11-estado-actual-del-proyecto)
12. [Planificación y Roadmap](#12-planificación-y-roadmap)
13. [Implementaciones Futuras](#13-implementaciones-futuras)

---

## 1. Introducción y Justificación

El sector de la organización de eventos ha experimentado un crecimiento considerable en los últimos años, siendo cada vez más habitual la venta de entradas online para conciertos, conferencias, festivales y actividades culturales. No obstante, muchos organizadores de pequeño y mediano tamaño carecen de herramientas tecnológicas adecuadas para gestionar de forma eficiente el proceso de venta, el control de asistentes y la validación de acceso.

**EntryPass** surge como respuesta a esta necesidad. Se trata de una plataforma web completa para la gestión y compra de entradas de eventos, con un enfoque en la accesibilidad para organizadores pequeños y la fluidez de experiencia para el usuario final.

Desde el punto de vista académico, este proyecto constituye el **Trabajo de Fin de Grado (TFG)** del ciclo formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW). Su propósito es demostrar el dominio de una arquitectura moderna y profesional, aplicando los conocimientos adquiridos a lo largo del ciclo en áreas como el desarrollo frontend y backend, el diseño de APIs, la contenerización de aplicaciones y el procesamiento asíncrono mediante colas de mensajes.

---

## 2. Objetivos del Proyecto

Los principales objetivos de EntryPass se han definido buscando no solo la funcionalidad, sino también la operatividad medible en un entorno real:

1. **Diseñar y desarrollar una plataforma web completa** de compra de entradas, accesible tanto para usuarios finales como para organizadores.
   * *Métrica de éxito:* Despliegue funcional de ambos perfiles (organizador y cliente) operando sobre la misma plataforma unificada.
2. **Implementar un frontend SPA moderno con Angular.**
   * *Métrica de éxito:* Lograr una navegación ágil con tiempos de carga de vistas menores a 1 segundo, gestionando el enrutamiento sin recargas completas de la página.
3. **Desarrollar una API REST robusta con Symfony.**
   * *Métrica de éxito:* Procesar concurrencia simulada comprobando tiempos de respuesta de la API estables (inferiores a 500ms en condiciones normales).
4. **Implementar procesamiento asíncrono para tareas costosas** (generación de QR, envío de correos) mediante RabbitMQ y Symfony Messenger.
   * *Métrica de éxito:* La respuesta al usuario al darle al botón de "Comprar" debe completarse en menos de 1 segundo (solo grabando en BD), delegando todo el trabajo pesado a procesos de fondo que no penalicen la experiencia de usuario.
5. **Generar entradas digitales y habilitar su validación infalible.**
   * *Métrica de éxito:* Escanear el código QR y cambiar instantáneamente su estado en tiempo real (`valid` → `used`), impidiendo matemáticamente la reutilización o fraude por duplicidad.

---

## 3. Público Objetivo (Target)

EntryPass está orientado a dos perfiles de usuario diferenciados, aunque interdependientes:

### 3.1. Perfil del usuario final
- **Características:** Acostumbrados a ecosistemas web/móvil, exigen agilidad y confirmaciones inmediatas al pagar.
- **Necesidades detectadas:** Proceso de compra sin fricciones, confirmación instántanea en pantalla y acceso fácil a su entrada digital en el móvil.

### 3.2. Perfil del organizador de eventos
- **Características:** Promotores independientes, asociaciones o salas pequeñas que necesitan gestionar aforo y controlar los accesos el día del evento de forma fácil.
- **Necesidades detectadas:** Autonomía tecnológica, reducción de grandes comisiones y una herramienta de escaneo rápida (portería).

### 3.3. Caso de uso típico: Flujo de compra y acceso
Para ilustrar claramente el uso realista de la aplicación, el "escenario ideal" cerrado y controlado es el siguiente:
1. **Exploración:** Un usuario sin cuenta accede a la landing y ve un festival anunciado. Al hacer clic en "Comprar entrada", la app detecta falta de sesión y redirige al flujo de login/registro de forma transparente guardando su intención de compra mediante variables de sesión o query params.
2. **Compra (Flujo de alta disponibilidad):** Una vez autenticado, selecciona la cantidad de tickets (1 a 4). Al confirmar a través del nuevo modal de *Checkout*, el sistema registra la compra en milisegundos y confirma visualmente en pantalla en un panel de éxito.
3. **Asincronía:** El sistema de fondo (RabbitMQ) se entera de la compra, genera los UUID únicos, dibuja los QRs, empaqueta un PDF y se lo envía al correo del usuario mientras este ya puede seguir navegando en su perfil.
4. **Respuesta en Perfil:** El usuario puede acceder a su pantalla de perfil y visualizar instantáneamente sus compras, desplegando los tickets y viendo sus códigos QR listos para escanear a través de una API de renderizado al vuelo.
5. **Control de Puerta:** El día del evento, el organizador utiliza la vista móvil de la plataforma para escanear el QR del usuario en la puerta. Si está válido, la pantalla parpadea en verde y descuenta aforo. Si vuelve a pasar el mismo QR, indica en rojo "Entrada ya utilizada".

---

## 4. Tecnologías a Emplear y Justificación

La selección tecnológica responde a criterios de arquitectura empresarial, buscando separar muy bien las responsabilidades y asegurar escalabilidad.

### 4.1. Frontend
* **Angular (SPA):** Se elige frente a enfoques tradicionales debido a su estructuración obligatoria y clara (módulos/features). Permite desarrollar una experiencia de usuario extremadamente interactiva y limpia donde el DOM solo se repinta cuando es estrictamente necesario, usando para su gestión interna la inyección de Signals en el control de estado complejo (como el flujo del *Checkout* de venta y manejo del modal).

### 4.2. Backend
* **Symfony (PHP 8.4) y Arquitectura Hexagonal:** Se descartan opciones más monolíticas porque Symfony aporta un control rígido y formal del código. La arquitectura Hexagonal (Puertos y Adaptadores) es una decisión arquitectónica clave: aísla las reglas de nuestro negocio de la infraestructura. Si mañana la base de datos cambia o la librería de PDF queda obsoleta, el Core de nuestro sistema no se toca, solo se cambia el adaptador.
* **Symfony Messenger:** Es nativo en Symfony, lo que facilita enormemente el dispatching o encolado de mensajes frente a tener que crear scripts demonio manuales.

### 4.3. Infraestructura y servicios
* **Docker & Docker Compose:** Utilizado para eliminar el eterno problema de "en mi máquina sí funciona". Garantiza que cualquier ordenador que clone el repo pueda levantar exactamente las mismas versiones de BD y PHP al instante.
* **RabbitMQ:** Retiene el mensaje de la compra en la memoria, avisa automáticamente al nodo suscriptor y maneja "reintentos automáticos" si la generación del PDF falla, garantizando cero pérdidas en la entrega de tickets, descargando a la DB transaccional de todo ese esfuerzo.
* **PostgreSQL:** Optimizada para consistencia relacional severa y operaciones ACID, fundamental a la hora de manipular transacciones limitadas al aforo.

---

## 5. Estado del Arte

### 5.1. Análisis de soluciones existentes y limitaciones
En la actualidad, plataformas como Eventbrite o Ticketmaster lideran el mercado. Su mayor inconveniente reside en su modelo de ingresos y lo abrumadoras que resultan sus interfaces de gestión para eventos pequeños. En estos estratos, el control de puerta todavía se ejerce demasiadas veces usando listas de papel.

### 5.2. Comparativa Visual y Margen de Mejora
| Característica | EntryPass | Eventbrite | Entradium |
|-----------------|------------|------------|------------|
| Coste por uso | **Gratuito / Privado** | Altas comisiones | Media/Baja |
| Independencia de datos e infra | **Alta (Self-Hosted local/cloud)** | Nula | Nula |
| Control ágil asíncrono en tickets | **Sí (RabbitMQ)** | Sí | Limitado |
| Orientación visual para organizador | **Simple y directa** | Muy compleja | Simple |

**Propuesta de valor:** La plataforma prioriza la "no-fricción". El pequeño promotor domina cien por cien el flujo y el dato; no paga comisiones abusivas por ticket y dispone de un control de acceso automatizado.

---

## 6. Arquitectura del Sistema

El sistema sigue una arquitectura de **SPA + API REST + Procesamiento Asíncrono**. Se ha consolidado el flujo asíncrono para la compra y emisión de entradas con un cumplimiento estricto de la **Arquitectura Hexagonal**.

1. **Flujo Principal:** `Usuario -> Angular SPA -> Nginx -> Symfony API -> PostgreSQL`.
2. **Procesamiento Asíncrono:** Tareas pesadas (crear códigos QR, ensamblar PDFs) delegan en **RabbitMQ** y son procesadas por un `worker` (Symfony Messenger).

---

## 7. Bases de Datos y Modelo

La plataforma descansa sobre PostgreSQL dividiendo la carga de la siguiente manera:

* **Users:** Almacena la información de autenticación y perfilado (Organizador o Cliente estándar).
* **Events:** Guarda el metadato del evento (título, fechas, total de aforo reservable, stock actual).
* **Purchases (Compras):** Un apunte contable global. Un usuario realiza *una única* compra que incluye múltiples asientos.
* **Tickets (Entradas):** Relación de "1 a N" con la compra. Cada ticket es único e independiente (`UUID`), pudiendo tener un estatus `valid` o `used`.

---

## 8. Lógica del Flujo de Navegación

* **Gestión de Sesiones Inteligente (JWT):** Además de interceptar errores 401 por sesión caducada para redirigir al Login de manera silenciosa, la aplicación memoriza la ruta pre-login de la que vino el cliente a través de parámetros de ruta (`returnUrl`), garantizando que tras autenticarse regrese automáticamente al punto del flujo en el que fue interrumpido, como una pasarela de entrada concreta.
* **Flujos dinámicos de Compra (Checkout):** La integración del *PurchaseService* en el front permite la carga limpia en modales *overlay* de las compras, detectando sin recargas si el componente ha de bloquear el pago si el front detectó caída de inventario o aforo agotado.

---

## 9. Manual de Uso (Aproximación UI)

La interfaz de **EntryPass** se ha rediseñado siguiendo una estética de alta fidelidad (High-Fidelity):

*   **Esquema de Color**: Fondo negro absoluto (`#000000`) con acentos en **Cyan Teatral** (`#26b1c4`).
*   **Interactividad y Checkout Premium**: Uso de gradientes radiales oscuros, *glassmorphism* (blur effect) y un novedoso componente de **Checkout Simulado** (Stripe Mock UI) que replica el comportamiento de las mejores pasarelas de pago, con validaciones estrictas y latencias intencionadas para crear una sensación de máxima profesionalidad.
*   **Códigos QR Dinámicos (Perfil):** El perfil del usuario expone su compra descompuesta en *Tickets* individuales renderizando un código QR 100% real de alto contraste desde el `ticket_hash` almacenado de forma segura en Base de Datos.
*   **Gestión Administrativa Fluida**: El Dashboard para organizadores erradica el uso de controles genéricos del navegador a favor de modales customizados y SVG puro. Se introduce la **Baja y Alta Lógica** asíncrona, gestionando la visibilidad del evento con _Signals_ en tiempo real sin recargar pantallas.

---

## 10. Metodología de Desarrollo

Para gestionar de forma coherente y continua el tamaño, el proyecto se aborda bajo iteraciones:

1. **Gestión de Tareas:** Sprints funcionales, que actualmente nos permiten dar por cerrados los flujos básicos de Frontend.
2. **Atomicidad e Integración Continua:** Código gestionado a través de Git, y automatización vía Docker-compose.

---

## 11. Estado Actual del Proyecto

A fecha de la presente memoria, **todas las fases planificadas del proyecto han sido completadas satisfactoriamente**. El sistema está operativo en su totalidad, tanto en modo desarrollo como en modo producción:

- ✅ **Infraestructura Docker completa:** 6 servicios en desarrollo (PostgreSQL, RabbitMQ, PHP-FPM, Nginx, Node, Worker) y 5 en producción (sin Node, con estáticos compilados).
- ✅ **Despliegue de producción con `ng build`:** Dockerfile multi-stage que compila Angular en modo producción y sirve los estáticos optimizados directamente desde Nginx, eliminando la dependencia del servidor de desarrollo Node.
- ✅ **Capa de Dominio Hexagonal completa:** Entidades principales, 4 puertos de aplicación (`EmailSender`, `PaymentGateway`, `PdfGenerator`, `QrCodeGenerator`) y sus 4 adaptadores concretos (`Symfony`, `Simulated`, `Dompdf`, `Endroid`).
- ✅ **Flujos de Seguridad:** JWT (LexikJWT), endpoints validados por roles (`ROLE_ADMIN`/`ROLE_USER`) en Backend y Guards en Frontend.
- ✅ **Frontend Angular 21 SPA Plenamente Funcional:**
  - Desarrollo SPA implementado bajo arquitectura **Zoneless** optimizada con _Angular Signals_ puros.
  - Flujo de **compra de alta fidelidad** con pasarela transaccional simulada (Stripe Mock UI).
  - **Perfil de Usuario y Dashboard Administrativo** con modales propios de Alta/Baja lógica y renderizado de códigos QR.
- ✅ **Suite de Testing con PHPUnit:** Tests unitarios y funcionales completos para los handlers de la capa de Aplicación, con ejecución 100% limpia (Exit Code 0) sin deprecaciones.
- ✅ **Auditoría de Accesibilidad y SEO:** Atributos `aria-label`, vinculación de formularios, meta-etiquetas y jerarquía semántica HTML5.

---

## 12. Planificación y Roadmap

| Fase | Descripción | Estado |
|------|-------------|--------|
| **Fase 1 – Infraestructura** | Dockerización completa del entorno y configuración de todos los servicios. | ✅ Completada |
| **Fase 2 – Dominio y Aplicación** | Definición de entidades, interfaces y primeros casos de uso (Arquitectura Hexagonal). | ✅ Completada |
| **Fase 3 – API REST** | Autenticación JWT, roles y lógica core robustecida en infraestructura. | ✅ Completada |
| **Fase 4 – Gestión de Tickets y QR** | Generación asíncrona de QR, PDFs y envío de email mediante RabbitMQ. | ✅ Completada |
| **Fase 5 – Simulación de Pagos** | Pasarela de pagos simulada (Stripe Mock) con Patrón Hexagonal (Ports & Adapters). | ✅ Completada |
| **Fase 6 – Frontend SPA** | Pantallas responsivas de Angular: eventos, compra interactiva, perfil y administración. | ✅ Completada |
| **Fase 7 – Testing y QA** | Suite PHPUnit con tests unitarios y funcionales. Accesibilidad y SEO. | ✅ Completada |
| **Fase 8 – Despliegue Producción** | Dockerfile multi-stage, `compose.prod.yml`, Nginx sirviendo estáticos de `ng build`. | ✅ Completada |

---

## 13. Implementaciones Futuras

Aunque el proyecto cubre la totalidad de los requisitos funcionales planificados, existen múltiples vías de evolución que podrían abordarse en un entorno profesional real:

### Funcionalidades de negocio
* **Integración con pasarela de pagos real (Stripe/PayPal):** Gracias a la Arquitectura Hexagonal, bastaría con implementar un nuevo adaptador para `PaymentGatewayInterface` que conecte con la API real de Stripe, sin modificar ninguna lógica de negocio existente.
* **Sistema de categorías y filtros avanzados:** Clasificación de eventos por tipo (concierto, teatro, deporte, conferencia), ubicación geográfica y rango de fechas, con búsqueda full-text.
* **Sistema de reembolsos y cancelaciones:** Permitir al usuario solicitar la devolución de una entrada antes del evento, con flujo de aprobación por parte del organizador.
* **Notificaciones en tiempo real (WebSockets):** Alertas push para avisar al usuario de cambios en eventos, confirmaciones de compra y recordatorios previos al evento.
* **Estadísticas y analíticas para organizadores:** Dashboard con métricas de ventas, ocupación, ingresos y gráficas de tendencia para cada evento.

### Mejoras técnicas
* **Despliegue en VPS/Cloud (CI/CD):** Pipeline de integración y despliegue continuo con GitHub Actions, desplegando automáticamente en un servidor cloud (AWS, DigitalOcean).
* **Testing E2E con Cypress/Playwright:** Automatización de pruebas de extremo a extremo simulando la interacción real del usuario en el navegador.
* **Internacionalización (i18n):** Soporte multiidioma para expandir la plataforma a mercados internacionales, aprovechando el módulo `@angular/localize`.
* **Aplicación móvil nativa (PWA / Capacitor):** Conversión de la SPA Angular en una Progressive Web App instalable o empaquetado nativo para iOS/Android mediante Ionic Capacitor.
* **Caché distribuida (Redis):** Implementación de una capa de caché para optimizar las consultas frecuentes (listados de eventos, validación de tokens) y reducir la carga sobre PostgreSQL.
* **Rate Limiting y protección anti-bots:** Middleware de control de tasa de peticiones para prevenir abuso en endpoints críticos como compra y registro.

---

*Documento actualizado — Memoria Técnica TFG (Versión Final) – Abril 2026*
