---
# ENTRYPASS
## Plataforma Web de Gestión y Compra de Entradas para Eventos

---

**Memoria Final – Trabajo de Fin de Grado**  
**Ciclo Formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW)**

---

| Campo | Información |
|-------|-------------|
| **Autor** | José Manuel Román Navarro |
| **Correo electrónico** | josemanuel.rnav@gmail.com |
| **Teléfono** | 695 386 759 |
| **Curso** | 2º DAW (Curso 25/26) |
| **Centro** | IES PORTADA ALTA |
| **Modalidad** | Individual |
| **Tiempo estimado** | 250 horas |
| **Fecha de entrega** | Abril/Mayo 2026 |

---

## ÍNDICE

1. [Introducción y Justificación](#1-introducción-y-justificación)
2. [Objetivos del Proyecto](#2-objetivos-del-proyecto)
3. [Público Objetivo (Target)](#3-público-objetivo-target)
4. [Tecnologías a Emplear y Justificación](#4-tecnologías-a-emplear-y-justificación)
5. [Estado del Arte](#5-estado-del-arte)
6. [Arquitectura del Sistema](#6-arquitectura-del-sistema)
7. [Bases de Datos y Modelo](#7-bases-de-datos-y-modelo)
8. [Diseño de Interfaces y Prototipado](#8-diseño-de-interfaces-y-prototipado)
9. [Seguridad y Control de Calidad (Testing)](#9-seguridad-y-control-de-calidad-testing)
10. [Lógica del Flujo de Navegación](#10-lógica-del-flujo-de-navegación)
11. [Metodología de Desarrollo](#11-metodología-de-desarrollo)
12. [Despliegue y Estado Final del Proyecto](#12-despliegue-y-estado-final-del-proyecto)
13. [Implementaciones Futuras](#13-implementaciones-futuras)
14. [Conclusiones](#14-conclusiones)

---

## 1. Introducción y Justificación

El sector de la organización de eventos ha experimentado un crecimiento considerable en los últimos años, siendo cada vez más habitual la venta de entradas online para conciertos, conferencias, festivales y actividades culturales. No obstante, muchos organizadores de pequeño y mediano tamaño carecen de herramientas tecnológicas adecuadas para gestionar de forma eficiente el proceso de venta, el control de asistentes y la validación de acceso.

**EntryPass** surge como respuesta a esta necesidad. Se trata de una plataforma web completa para la gestión y compra de entradas de eventos, con un enfoque en la accesibilidad para organizadores pequeños y la fluidez de experiencia para el usuario final.

Desde el punto de vista académico, este proyecto constituye el **Trabajo de Fin de Grado (TFG)** del ciclo formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW). Su propósito es demostrar el dominio de una arquitectura moderna y profesional, aplicando los conocimientos adquiridos a lo largo del ciclo en áreas como el desarrollo frontend y backend, el diseño de APIs, la contenerización de aplicaciones, pruebas automatizadas y el procesamiento asíncrono mediante colas de mensajes.

---

## 2. Objetivos del Proyecto

Los principales objetivos de EntryPass se han definido buscando no solo la funcionalidad, sino también la operatividad medible en un entorno real:

1. **Diseñar y desarrollar una plataforma web completa** de compra de entradas, accesible tanto para usuarios finales como para organizadores.
   * *Métrica de éxito:* Despliegue funcional de ambos perfiles operando sobre la misma plataforma unificada.
2. **Implementar un frontend SPA moderno con Angular.**
   * *Métrica de éxito:* Lograr una navegación ágil gestionando el enrutamiento sin recargas completas de la página y utilizando control de estado avanzado mediante Signals.
3. **Desarrollar una API REST robusta con Symfony.**
   * *Métrica de éxito:* Procesar concurrencia simulada comprobando tiempos de respuesta estables, utilizando el patrón de Arquitectura Hexagonal.
4. **Implementar procesamiento asíncrono para tareas costosas** (generación de QR, envío de correos) mediante RabbitMQ y Symfony Messenger.
   * *Métrica de éxito:* La respuesta al usuario en compras pesadas no debe penalizar la experiencia, delegando el trabajo a *workers* en segundo plano.
5. **Generar entradas digitales y habilitar su validación infalible.**
   * *Métrica de éxito:* Escanear el código QR y cambiar instantáneamente su estado en tiempo real (`valid` → `used`), impidiendo reutilización o fraude.

---

## 3. Público Objetivo (Target)

EntryPass está orientado a dos perfiles de usuario diferenciados, aunque interdependientes:

### 3.1. Perfil del usuario final
- **Características:** Acostumbrados a ecosistemas web/móvil, exigen agilidad y confirmaciones inmediatas al pagar.
- **Necesidades detectadas:** Proceso de compra sin fricciones, confirmación instántanea en pantalla y acceso fácil a su entrada digital en el móvil.

### 3.2. Perfil del organizador de eventos
- **Características:** Promotores independientes, asociaciones o salas pequeñas que necesitan gestionar aforo y controlar los accesos el día del evento de forma fácil.
- **Necesidades detectadas:** Autonomía tecnológica, reducción de grandes comisiones y una herramienta de escaneo rápida y visual (portería).

---

## 4. Tecnologías a Emplear y Justificación

La selección tecnológica responde a criterios de arquitectura empresarial, buscando separar responsabilidades y asegurar escalabilidad.

### 4.1. Frontend
* **Angular (SPA):** Framework SPA bajo arquitectura *Zoneless* y estado mediante *Signals*. Permite desarrollar una experiencia de usuario interactiva donde el DOM solo se repinta cuando es estrictamente necesario, vital en los "carritos de compra".
* **HTML5 / CSS Nativo:** Para asegurar la accesibilidad, diseño semántico y optimización máxima evitando librerías de estilos pesadas.

### 4.2. Backend
* **Symfony (PHP 8.4):** Se descartan enfoques monolíticos básicos en favor de un marco riguroso.
* **Arquitectura Hexagonal (Ports & Adapters):** Aísla las reglas de negocio de la infraestructura. Facilita el intercambio de herramientas (ej. cambiar pasarela simulada por Stripe real).
* **Doctrine ORM:** Abstracción segura orientada a objetos para la base de datos relacional.

### 4.3. Infraestructura y servicios
* **Docker & Docker Compose:** Utilizado para garantizar la portabilidad e idéntica ejecución en desarrollo y producción.
* **RabbitMQ:** Broker dedicado para encolado asíncrono, garantizando cero pérdidas en la entrega de tickets mediante políticas de auto-reintento.
* **PostgreSQL:** Optimizada para consistencia relacional severa y operaciones ACID (vital para transacciones comerciales y aforo).
* **Nginx:** Servidor optimizado para el proxy inverso de la API y para servir los archivos estáticos generados del frontend.

---

## 5. Estado del Arte

### 5.1. Análisis de soluciones existentes y limitaciones
En la actualidad, plataformas como Eventbrite o Ticketmaster lideran el mercado. Su mayor inconveniente reside en su modelo de ingresos (altas comisiones) y en sus interfaces complejas. En sectores más pequeños, el control de acceso suele hacerse mediante listas de papel o hojas de Excel, provocando colas lentas.

**Propuesta de valor:** EntryPass prioriza la "no-fricción" y ofrece un control de puerta automatizado (vía QR) igual de potente que los líderes del mercado, sin infraestructuras inalcanzables.

---

## 6. Arquitectura del Sistema

El sistema obedece a un esquema **SPA + API REST + Procesamiento Asíncrono**.

1. **Flujo Principal:** `Usuario -> Angular SPA -> Nginx -> Symfony API -> PostgreSQL`.
2. **Procesamiento Asíncrono:** Tareas pesadas (QR, PDFs, Emails) se delegan en **RabbitMQ** y son procesadas por un `worker`.

### Arquitectura Hexagonal
* **Domain**: Reglas puras (`User`, `Event`, `Purchase`, `Ticket`).
* **Application**: Casos de uso (`PurchaseTicketHandler`). Definición de Interfaces (Puertos) como `PaymentGatewayInterface` o `QrCodeGeneratorInterface`.
* **Infrastructure**: Implementaciones concretas o adaptadores (`EndroidQrCodeAdapter`, `SimulatedPaymentGateway`), controladores HTTP y repositorios.

---

## 7. Bases de Datos y Modelo

Basado en PostgreSQL con diseño normalizado de alta integridad.
* **Users:** Perfiles y roles de seguridad (`ROLE_USER`, `ROLE_ADMIN`).
* **Events:** Atributos del evento (título, fechas, total de aforo reservable, stock disponible con prevención de concurrencia).
* **Purchases:** Apunte contable global.
* **Tickets:** Entradas concretas, en relación 1:N con Purchases. Identificador único inviolable (`UUID`) y estado lógico de validación (`status`).

---

## 8. Diseño de Interfaces y Prototipado

### 8.1. Guía de Estilos y Prototipado (Figma)
Antes de la programación de componentes visuales, el desarrollo de la interfaz partió de un **prototipado y una guía de estilos conceptual** enfocados en la experiencia nocturna/cultural:
* **Esquema de Color**: Fondo negro puro (`#000000`) para lograr profundidad y contraste (Modo oscuro nativo).
* **Acentos (Primary)**: **Cyan Teatral** (`#26b1c4`), proporcionando un aspecto moderno y vibrante.
* **Glassmorphism**: Efectos de difuminado (`backdrop-filter: blur`) en componentes flotantes como cabeceras y modales.

### 8.2. Frontend Responsivo (Mobile-First)
El maquetado hace uso de HTML5 semántico (`<header>`, `<main>`, `<article>`) y variables CSS globales para lograr un diseño responsivo 100% adaptable mediante CSS Grid y Flexbox. 

---

## 9. Seguridad y Control de Calidad (Testing)

### 9.1. Seguridad y Autenticación
* **Autenticación mediante JWT:** Tokens seguros sin estado firmados criptográficamente (`LexikJWTAuthenticationBundle`).
* **Validación de Formularios:** Doble capa de validación (Angular Reactive Forms + Constraints de Symfony Validator) para mitigar vulnerabilidades y errores de inyección.
* **Control de Accesos (RBAC):** Restricciones de rutas mediante Guards en Angular y Atributos de Seguridad `#[IsGranted('ROLE_ADMIN')]` en los endpoints.

### 9.2. Testing Automatizado (PHPUnit)
Para asegurar que la capa de Dominio y Aplicación no se corrompe, se implementó una **suite de pruebas automatizadas con PHPUnit**:
* **Tests Unitarios:** Verifican los adaptadores y simuladores (como la validación correcta de pagos falsos y rechazados en `SimulatedPaymentGatewayTest`).
* **Tests Funcionales:** Prueban los casos de uso completos (`PurchaseTicketHandlerTest`, etc.) sin dependencias colaterales.
El resultado de la ejecución continua de los tests arroja una limpieza absoluta sin errores críticos.

---

## 10. Lógica del Flujo de Navegación

* **Gestión Inteligente de Caídas y Redirecciones:** Si un token JWT expira, el interceptor de Angular atrapa el error 401, limpia la sesión y redirige discretamente al usuario. Además, se conserva el historial de la última vista (`returnUrl`) para devolverlo automáticamente donde estaba tras iniciar sesión.
* **Validación de Puerta Rápida:** La interfaz móvil de lectura QR omite florituras para mostrar exclusivamente rojo (Error) o verde (Aprobado), maximizando la usabilidad del personal de puerta en ambientes de escasa luz.

---

## 11. Metodología de Desarrollo

El proyecto se gestionó bajo principios ágiles adaptados para el trabajo individual:
* **Flujos Iterativos (Sprints Funcionales):** Planificación escalonada (Fase API, Fase Asíncrona, Fase Frontend).
* **Gestión de Versiones (Git):** Uso exhaustivo de repositorios locales y confirmaciones (commits) atómicas.
* **Integración y Estandarización Continua:** El uso de Docker ha permitido mantener un entorno limpio y libre de conflictos en todas las fases del ciclo de vida del desarrollo.

---

## 12. Despliegue y Estado Final del Proyecto

**Estado:** Proyecto 100% Finalizado cumpliendo los requisitos funcionales.

### Arquitectura de Despliegue
La aplicación ha sido encapsulada en **contenedores Docker** altamente optimizados para Producción:
* **Backend:** Contenedor optimizado de PHP-FPM con las extensiones mínimas necesarias.
* **Frontend:** Se emplea un **Dockerfile Multi-Stage** que compila la aplicación Angular en un *bundle* estático (AOT) en la primera etapa, el cual es posteriormente copiado a una imagen pura de **Nginx** en la segunda etapa, prescindiendo totalmente de entornos de desarrollo de Node.js en producción.
* **Orquestación:** Se maneja a través de un archivo final `compose.prod.yml`.

---

## 13. Implementaciones Futuras

Como continuidad comercial e industrial, el proyecto se encuentra preparado para integrar de manera natural las siguientes mejoras:
* **Despliegue Cloud & SSL Real:** Despliegue del archivo Compose de producción a infraestructuras Cloud (AWS EC2 / DigitalOcean) con un proxy reverso manejando certificados SSL gratuitos automatizados (Let's Encrypt).
* **Integración con pasarela real (Stripe):** Bastaría con añadir una única clase (`StripeAdapter`) que implemente la `PaymentGatewayInterface` para comenzar a cobrar dinero real.
* **App Móvil Nativa (Capacitor):** Empaquetar la SPA de Angular para distribución en App Store y Google Play.

---

## 14. Conclusiones

La elaboración de **EntryPass** demuestra que las tecnologías abordadas en el ciclo DAW permiten, con la correcta estructura y arquitectura (Hexagonal, Mensajería Asíncrona), desarrollar productos a nivel empresarial de principio a fin. El diseño e implementación asíncrona no solo dota al sistema de agilidad en los cuellos de botella clásicos, sino que garantiza fiabilidad e integridad relacional.

Se ha obtenido un producto escalable, con una experiencia de usuario de gran valor estético y técnico, dejando una base arquitectónica completamente limpia para escalar a pasarelas de pago y despliegues reales en la nube en muy poco tiempo.

---
*Documento Final - Memoria Técnica TFG – Abril 2026*
