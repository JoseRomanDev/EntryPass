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
| **Fecha de entrega** | Marzo 2026 |

---

## ÍNDICE

1. [Introducción y Justificación](#1-introducción-y-justificación)
2. [Objetivos del Proyecto](#2-objetivos-del-proyecto)
3. [Público Objetivo (Target)](#3-público-objetivo-target)
   - 3.1. [Perfil del usuario final](#31-perfil-del-usuario-final)
   - 3.2. [Perfil del organizador de eventos](#32-perfil-del-organizador-de-eventos)
4. [Tecnologías a Emplear](#4-tecnologías-a-emplear)
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
7. [Estado Actual del Proyecto](#7-estado-actual-del-proyecto)
8. [Planificación y Roadmap](#8-planificación-y-roadmap)

---

## 1. Introducción y Justificación

El sector de la organización de eventos ha experimentado un crecimiento considerable en los últimos años, siendo cada vez más habitual la venta de entradas online para conciertos, conferencias, festivales y actividades culturales. No obstante, muchos organizadores de pequeño y mediano tamaño carecen de herramientas tecnológicas adecuadas para gestionar de forma eficiente el proceso de venta, el control de asistentes y la validación de acceso.

**EntryPass** surge como respuesta a esta necesidad. Se trata de una plataforma web completa para la gestión y compra de entradas de eventos, con un enfoque en la accesibilidad para organizadores pequeños y la fluidez de experiencia para el usuario final.

Desde el punto de vista académico, este proyecto constituye el **Trabajo de Fin de Grado (TFG)** del ciclo formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW). Su propósito es demostrar el dominio de una arquitectura moderna y profesional, aplicando los conocimientos adquiridos a lo largo del ciclo en áreas como el desarrollo frontend y backend, el diseño de APIs, la contenerización de aplicaciones y el procesamiento asíncrono mediante colas de mensajes.

---

## 2. Objetivos del Proyecto

Los principales objetivos de EntryPass son los siguientes:

1. **Diseñar y desarrollar una plataforma web completa** de compra de entradas para eventos, accesible tanto para usuarios finales como para organizadores.

2. **Implementar un frontend SPA moderno** con Angular que ofrezca una navegación fluida, intuitiva y responsiva al usuario.

3. **Desarrollar una API REST robusta** con Symfony para gestionar de forma segura usuarios, eventos y tickets.

4. **Implementar un sistema de generación de entradas digitales** con códigos QR únicos, asociados a un identificador UUID por compra.

5. **Permitir la validación de entradas** mediante el escaneo del código QR en el acceso al evento, con actualización del estado del ticket (`valid` → `used`).

6. **Implementar procesamiento asíncrono** para tareas costosas como la generación de códigos QR y el envío de correos electrónicos de confirmación, utilizando RabbitMQ y Symfony Messenger.

7. **Desplegar la aplicación en contenedores Docker** para simular un entorno de producción profesional y facilitar la replicabilidad del proyecto.

---

## 3. Público Objetivo (Target)

EntryPass está orientado a dos perfiles de usuario diferenciados, aunque interdependientes:

### 3.1. Perfil del usuario final

- **Descripción:** Personas que desean comprar entradas para eventos de forma online.
- **Características:**
  - Perfil técnico variable: desde usuarios digitales avanzados hasta usuarios con conocimientos básicos.
  - Acostumbrados al uso de aplicaciones web y móviles.
  - Necesidad de inmediatez en el proceso de compra.
  - Expectativa de recibir confirmación digital (correo electrónico con entrada en formato QR).
- **Necesidades detectadas:**
  - Proceso de compra sencillo y rápido.
  - Confirmación inmediata de la transacción.
  - Entrada digital fácilmente accesible desde el dispositivo móvil.
  - Seguridad en el proceso de pago y en la custodia de datos personales.

### 3.2. Perfil del organizador de eventos

- **Descripción:** Pequeñas y medianas asociaciones, entidades culturales o promotores independientes que organizan eventos y necesitan gestión de aforo y acceso.
- **Características:**
  - Organizadores de eventos culturales o musicales de pequeño y mediano tamaño.
  - Asociaciones o entidades que organizan actividades con inscripción previa.
  - Personal encargado del control de acceso en el evento.
- **Necesidades detectadas:**
  - Centralización de la gestión de eventos en una única plataforma.
  - Validación ágil de entradas en el acceso (escaneo de QR).
  - Reducción de comisiones frente a grandes plataformas.
  - Autonomía tecnológica sin depender de soluciones de terceros.

---

## 4. Tecnologías a Emplear

La selección tecnológica de EntryPass responde a criterios de modernidad, escalabilidad y alineación con los estándares de la industria del desarrollo web actual.

### 4.1. Frontend

| Tecnología | Propósito |
|------------|-----------|
| **Angular** | Framework SPA para el desarrollo de la interfaz de usuario. Organización del proyecto basada en features (`auth`, `events`, `tickets`, `profile`). |
| **TypeScript** | Lenguaje tipado que mejora la mantenibilidad y la detección de errores en tiempo de desarrollo. |
| **HTML5 / CSS / SCSS** | Maquetación semántica y estilado modular de componentes. |

### 4.2. Backend

| Tecnología | Propósito |
|------------|-----------|
| **Symfony (PHP)** | Framework para el desarrollo de la API REST. Proporciona robustez, convenciones claras y un ecosistema maduro. |
| **Arquitectura Hexagonal (Ports & Adapters)** | Patrón arquitectónico que desacopla el dominio lógico de las implementaciones técnicas (persistencia, mensajería, controladores). |
| **Doctrine ORM** | Capa de abstracción para la persistencia de datos, mapeando las entidades del dominio (`User`, `Event`, `Ticket`) a la base de datos relacional. |
| **Symfony Messenger** | Componente para la gestión de mensajes y colas de procesamiento asíncrono. |

### 4.3. Infraestructura y servicios

| Tecnología | Propósito |
|------------|-----------|
| **Docker & Docker Compose** | Contenerización completa de la aplicación. Garantiza reproducibilidad del entorno y facilidad de despliegue. |
| **Nginx** | Servidor web principal y reverse proxy, enrutando peticiones entre el frontend y la API. |
| **PostgreSQL** | Base de datos relacional para la persistencia de datos de usuarios, eventos y tickets. |
| **RabbitMQ** | Broker de mensajería para la gestión de tareas asíncronas (generación de QR, envío de emails). |

### 4.4. Herramientas de desarrollo

| Herramienta | Propósito |
|-------------|-----------|
| **Git** | Control de versiones del código fuente. |
| **PHPUnit** | Framework de testing para el backend Symfony. |

---

## 5. Estado del Arte

### 5.1. Descripción del problema o necesidad detectada

La organización de eventos requiere un sistema eficaz para gestionar el registro de asistentes, la venta de entradas y el control de acceso. En la actualidad, muchos pequeños organizadores o asociaciones carecen de herramientas tecnológicas adecuadas, recurriendo a métodos manuales o sistemas poco integrados que generan problemas como:

- **Errores humanos** en la gestión de listas de asistentes.
- **Duplicidades** en la validación de entradas.
- **Colas excesivas** en los accesos por procesos de verificación lentos.
- **Ausencia de datos** sobre el comportamiento de los usuarios o la ocupación de los eventos.

### 5.2. Análisis de soluciones existentes

En el mercado actual existen varias plataformas consolidadas para la gestión y venta de entradas. A continuación se presenta un análisis comparativo de las más relevantes:

| Solución | Descripción | Puntos Fuertes | Puntos Débiles |
|----------|-------------|----------------|----------------|
| **Eventbrite** | Plataforma internacional de referencia para la gestión de eventos de todo tipo y tamaño. | Gran alcance global, sistema consolidado, variedad de tipologías de evento, herramientas de marketing integradas. | Comisiones elevadas por venta de entrada (entre el 3,5% y el 6,5% + tarifa fija), personalización limitada para eventos pequeños. |
| **Ticketmaster** | Plataforma líder enfocada a grandes eventos y promotores de alto volumen. | Integración con grandes recintos y promotores, tecnología de prevención de reventa. | Interfaz compleja, diseñada para grandes actores; inaccesible para pequeños organizadores; comisiones altas. |
| **Entradium** | Plataforma española orientada a eventos de tamaño mediano. | Interfaz más sencilla que las anteriores, adaptada al mercado español, sin comisiones en eventos gratuitos. | Poca flexibilidad para integración o personalización, análisis de datos limitado. |
| **Sympla / Kinetix** | Otras soluciones emergentes en el sector. | Modelos freemium, facilidad de uso. | Ecosistema reducido, menor fiabilidad e integración. |

### 5.3. Limitaciones de las soluciones actuales

Del análisis anterior se extraen las siguientes limitaciones comunes en las soluciones existentes:

1. **Comisiones elevadas:** Las grandes plataformas (Eventbrite, Ticketmaster) aplican comisiones significativas que reducen el margen para eventos pequeños o culturales sin ánimo de lucro.

2. **Complejidad para eventos pequeños:** Las interfaces y flujos de gestión están diseñados para eventos de gran formato, resultando complejos e innecesariamente extensos para asociaciones o pequeños organizadores.

3. **Persistencia del control de acceso manual:** En muchos eventos de pequeño formato, la validación de entradas se sigue realizando con listas en papel o validación manual, con el consiguiente riesgo de errores.

4. **Falta de autonomía tecnológica:** Ninguna solución existente del mercado permite al organizador tener control total sobre sus datos, su infraestructura o su flujo de negocio sin depender completamente del proveedor.

5. **Escasa personalización:** La mayoría de plataformas ofrecen una experiencia estándar, con poca capacidad de adaptación al contexto específico de cada organizador o evento.

### 5.4. Margen de mejora y propuesta de valor

EntryPass se posiciona como una alternativa moderna y personalizable que aborda las carencias identificadas, aportando:

- **Control total del proceso:** Al ser una plataforma desarrollada a medida, el organizador no depende de terceros para la gestión de sus datos ni de sus flujos de negocio.
- **Digitalización total del acceso:** Entradas digitales con código QR único por entrada, generado de forma automática y enviado por correo electrónico tras la compra.
- **Validación ágil y fiable:** Endpoint protegido para la verificación del QR en tiempo real, transformando el control de acceso presencial en un proceso rápido y sin errores.
- **Arquitectura moderna y escalable:** El sistema está diseñado con patrones de arquitectura profesionales (Hexagonal, SPA, procesamiento asíncrono) que permiten escalar y mantener la plataforma de forma sostenible.
- **Procesamiento asíncrono:** El desacoplamiento de tareas pesadas (generación de QR, envíos de email) mediante RabbitMQ garantiza que la experiencia del usuario durante la compra sea siempre inmediata, independientemente de la carga del sistema.

---

## 6. Arquitectura del Sistema

El sistema EntryPass sigue una arquitectura de tres capas principales: **SPA Frontend** + **API REST Backend** + **Procesamiento Asíncrono**. Toda la infraestructura está contenerizada con Docker.

### Flujo principal de datos

```
Usuario → Angular SPA → Nginx (Reverse Proxy) → Symfony API → PostgreSQL
```

### Flujo asíncrono (compra de entrada)

```
Compra confirmada → Symfony API → RabbitMQ (cola) → Worker (Symfony Messenger)
                                                         ├── Genera código QR
                                                         └── Envía email con entrada
```

### Arquitectura Hexagonal del Backend

El backend aplica el patrón de **Arquitectura Hexagonal (Ports & Adapters)**, que aísla el dominio lógico del sistema de sus implementaciones técnicas:

| Capa | Responsabilidad | Ejemplos |
|------|-----------------|---------|
| **Domain** | Entidades, interfaces de repositorios y Value Objects. Sin dependencias externas. | `User`, `Event`, `Ticket`, `EventRepositoryInterface` |
| **Application** | Casos de uso, DTOs y servicios orquestadores. | `CreateEventHandler`, `RegisterUserHandler`, `PurchaseTicketHandler` |
| **Infrastructure** | Controladores REST, persistencia (Doctrine), mensajería (RabbitMQ) y servicios externos. | `DoctrineEventRepository`, `RegisterUserController`, `QrCodeGenerator` |

---

## 7. Estado Actual del Proyecto

A fecha de la presente entrega, se han implementado las siguientes bases fundamentales:

- ✅ **Infraestructura Docker completa:** Todos los contenedores (PostgreSQL, RabbitMQ, PHP-FPM, Nginx, Node, Worker) configurados y orquestados mediante `compose.yml`.
- ✅ **Capa de Dominio:** Entidades principales definidas (`User`, `Event`) con sus interfaces de repositorio correspondientes.
- ✅ **Capa de Aplicación:** Casos de uso centrales implementados (`CreateEventHandler`, `RegisterUserHandler`).
- ✅ **Capa de Infraestructura:** Persistencia configurada con Doctrine ORM (`DoctrineEventRepository`, `DoctrineUserRepository`). Controladores REST iniciales en desarrollo (`RegisterUserController`, `GetUserProfileController`).
- 🔄 **Frontend Angular:** Proyecto inicializado con la estructura de features planeada.

---

## 8. Planificación y Roadmap

El desarrollo de EntryPass se articula en las siguientes fases iterativas:

| Fase | Descripción | Estado |
|------|-------------|--------|
| **Fase 1 – Infraestructura** | Dockerización completa del entorno y configuración de todos los servicios. | ✅ Completada |
| **Fase 2 – Dominio y Aplicación** | Definición de entidades, interfaces y primeros casos de uso. | ✅ Completada |
| **Fase 3 – API REST** | Implementación de endpoints para usuario, eventos y tickets. Autenticación (JWT). | 🔄 En progreso |
| **Fase 4 – Gestión de Tickets y QR** | Entidad `Ticket` con UUID, generación asíncrona de QR y envío de email. | ⏳ Pendiente |
| **Fase 5 – Validación de Acceso** | Endpoint de verificación de QR con cambio de estado del ticket. | ⏳ Pendiente |
| **Fase 6 – Frontend SPA** | Pantallas responsivas de Angular: auth, explorador de eventos, compra y perfil. | ⏳ Pendiente |
| **Fase 7 – Testing y QA** | Tests unitarios con PHPUnit, pruebas de integración y validación del flujo completo. | ⏳ Pendiente |

---

*Documento generado para la entrega de revisión parcial de la memoria del TFG – Marzo 2026.*
