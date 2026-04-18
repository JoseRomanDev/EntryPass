# Informe de Mejoras - Proyecto EntryPass (Abril 2026)

Este informe detalla los avances logrados y las mejoras introducidas en la plataforma **EntryPass** durante la fase actual de desarrollo, contrastándola con hitos anteriores y atendiendo a las necesidades detectadas.

---

##  1. Mejoras en el Frontend (UI/UX y Lógica)

El frontend ha pasado de ser un MVP (Mínimo Producto Viable) a una interfaz **High-Fidelity** con una experiencia de usuario premium.

*   **Identidad Visual Premium**: Migración a una paleta de colores **Dark/Cyan/Teal**. Uso de fondos negros puros (`#000000`) y acentos en Cyan (`#26b1c4`), proporcionando una estética moderna y profesional.
*   **Gestión de Estado Reactiva con Signals**: Sustitución de flujos tradicionales por **Angular Signals** en componentes críticos como el modal de *Checkout*. Esto garantiza una reactividad instantánea al seleccionar cantidad de tickets o validar stock.
*   **Flujo de Checkout Optimizado**: Implementación de un modal inteligente que gestiona la compra de forma fluida, incluyendo validaciones en tiempo real y persistencia de intención de compra tras el login.
*   **Panel de Usuario Consolidado**: La sección "Mis Entradas" ahora permite visualizar historial de compras y renderizar códigos QR dinámicos individualizados para cada entrada.

##  2. Mejoras en el Backend (Arquitectura y Funcionalidad)

Se ha reforzado el Core del sistema siguiendo los principios de la **Arquitectura Hexagonal**.

*   **Validación de Tickets (Portería)**: Creación del endpoint `/api/tickets/validate` para el escaneo presencial. Implementa lógica de seguridad para evitar dobles usos (`valid` -> `used`).
*   **Pasarela de Pagos Simulada**: Integración de una capa de infraestructura para simular transacciones financieras de forma asíncrona, desacoplando la lógica de negocio del procesamiento de pagos mediante el patrón *Ports & Adapters*.
*   **Gestión de Borrado Lógico (Soft-Delete)**: Implementación de la baja lógica para eventos. Los eventos eliminados permanecen en la base de datos para integridad referencial pero dejan de ser visibles para el usuario final.
*   **Límites de Compra por Negocio**: Restricción técnica de **máximo 4 entradas por usuario y evento**, asegurando un reparto justo de aforo y evitando el acaparamiento.

##  3. Infraestructura y Estabilidad

*   **Seguridad JWT Reforzada**: Implementación de *Guards* de Angular y autenticación por roles (`ROLE_ADMIN`, `ROLE_USER`) más estricta en el backend.
*   **Optimización de RabbitMQ**: Afinamiento de los consumidores (`workers`) para garantizar que la generación de PDFs y QRs se ejecute sin latencia perceptible para el usuario.
*   **Dockerización Profesional**: Unificación de entornos con un `Makefile` robusto que simplifica las tareas habituales de mantenimiento y despliegue.

##  4. Calidad de Software (QA)

*   **Suite de Pruebas PHPUnit**: Introducción sistemática de tests unitarios y funcionales. Se ha alcanzado una cobertura significativa en los controladores de autenticación, gestión de eventos y validación de tickets.
*   **Refactorización de Código**: Limpieza de "code smells" y estandarización según PSR-12 en el backend y las guías de estilo de Angular en el frontend.

---

Este informe refleja el compromiso con la excelencia técnica y la evolución continua del proyecto hacia una solución de grado profesional.

**Autor:** José Manuel Román Navarro  
**Fecha:** Abril 2026
