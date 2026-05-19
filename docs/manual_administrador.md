# Manual del Administrador - EntryPass

¡Bienvenido al **Manual de Administración de EntryPass**! Este documento está dirigido al personal organizador y al equipo de soporte de la plataforma. Detalla el funcionamiento de los paneles de control de eventos, el flujo de validación en portería y las guías de soporte técnico para el correcto funcionamiento de la infraestructura.

---

## 📌 Índice de Contenidos

1. [Acceso de Gestión (ROL_ADMIN)](#1-acceso-de-gestión-rol_admin)
2. [Gestión Integral de Eventos](#2-gestión-integral-de-eventos)
3. [Control de Accesos en Puerta (Portería QR)](#3-control-de-accesos-en-puerta-portería-qr)
4. [Soporte Técnico Avanzado y DevOps](#4-soporte-técnico-avanzado-y-devops)
   * [4.1. Activación y Diagnóstico de Colas (RabbitMQ)](#41-activación-y-diagnóstico-de-colas-rabbitmq)
   * [4.2. Expiración de Sesión y Claves JWT](#42-expiración-de-sesión-y-claves-jwt)
   * [4.3. Conflictos de Puertos Docker (8080, 5432, 15672)](#43-conflictos-de-puertos-docker-8080-5432-15672)
   * [4.4. Reseteo Completo de la Base de Datos y Seeds](#44-reseteo-completo-de-la-base-de-datos-y-seeds)

---

## 🔑 1. Acceso de Gestión (ROL_ADMIN)

Para administrar la plataforma de EntryPass, es obligatorio disponer de un usuario con privilegios de rol administrador (`ROLE_ADMIN`):

*   **Credenciales de Demostración:** Se inyecta una semilla de forma predeterminada para el testeo del tribunal:
    *   **Email:** `admin@entrypass.com`
    *   **Contraseña:** `Admin123!`
*   **Barra de Navegación Dinámica:** Al iniciar sesión con esta cuenta, la cabecera del sitio detectará tu rol de forma segura y habilitará un botón exclusivo llamado **Administración** (este botón está totalmente oculto para usuarios compradores ordinarios).

---

## 🛠️ 2. Gestión Integral de Eventos

Desde el panel administrativo, el organizador controla la oferta comercial en tiempo real:

*   **Crear Espectáculos:** Haz clic en **Nuevo Evento**. Rellena el formulario con el título, descripción larga del evento, categoría (p. ej., Música, Teatro, Deportes), fecha y hora de la celebración, aforo máximo inicial y la URL para la imagen de portada.
*   **Editar Datos:** Puedes actualizar la fecha, la descripción o expandir el aforo de eventos activos en cualquier momento.
*   **Baja Lógica (Soft-Delete) para Integridad Referencial:**
    > [!WARNING]
    > **Importante:** Si decides cancelar o eliminar un evento que ya tiene entradas vendidas a clientes, el backend **no borrará el registro físicamente de la base de datos**.
    > En su lugar, aplica una *Baja Lógica* (Soft-Delete) rellenando la columna `deleted_at`. Esto retira el evento de la Home y los catálogos públicos inmediatamente para impedir nuevas compras, pero conserva los registros de ventas de cara a la facturación histórica, consulta de perfiles y devoluciones.

---

## 🎫 3. Control de Accesos en Puerta (Portería QR)

El día del espectáculo, el personal de portería utilizará la herramienta integrada de escaneo móvil para registrar las entradas y mitigar el fraude:

*   **Acceso a la Herramienta:** Entra al Panel Administrativo y pulsa en **Validación QR / Portería**.
*   **Escáner QR Integrado:** El sistema puede utilizar la cámara del teléfono móvil para escanear el código QR del cliente, o bien introducir de forma manual el Hash UUID en la consola de entrada.
*   **Evaluación Visual de Estados:** La pantalla de portería está diseñada en alta visibilidad con colores de fondo planos ideales para entornos de concierto oscuros:
    *   ✅ **Verde (Acceso Autorizado):** Entrada válida, auténtica y que no ha sido usada. El backend cambia inmediatamente el estado de la entrada de `valid` a `used` y registra el *timestamp* de acceso.
    *   ❌ **Rojo (Acceso Denegado / Fraude):** El código es inexistente o, de forma crítica, **ya fue utilizado con anterioridad**. La pantalla expondrá la hora exacta en la que se realizó el primer escaneo de ese ticket para justificar el bloqueo al portador.

---

## 💻 4. Soporte Técnico Avanzado y DevOps

Esta sección detalla cómo supervisar la infraestructura dockerizada de la plataforma y resolver eventualidades típicas de administración en servidores de desarrollo o despliegues productivos.

### 4.1. Activación y Diagnóstico de Colas (RabbitMQ)

Si la base de datos registra las compras correctamente pero los compradores no reciben sus correos electrónicos o no se visualiza el QR, lo más probable es que el servicio de mensajería (Worker) de Symfony Messenger esté detenido.

*   **Diagnóstico Visual (Dashboard):**
    Abre en tu navegador la consola de RabbitMQ en [http://localhost:15672](http://localhost:15672) (Credenciales por defecto: `guest` / `guest`). Inspecciona la sección **Queues** para verificar si hay mensajes retenidos en la cola `async`.
*   **Arranque del Consumidor en Segundo Plano:**
    Si estás en el entorno de desarrollo, inicia el worker de Symfony con el siguiente comando:
    ```bash
    docker compose exec php php bin/console messenger:consume async -vv
    ```
    *(El indicador `-vv` activa el modo verboso, mostrando en la terminal la lectura en tiempo real de cada mensaje de correo y ticket PDF que procesa).*

---

### 4.2. Expiración de Sesión y Claves JWT

El token de seguridad JWT tiene un ciclo de vida configurado por defecto en 1 hora.

*   **Interceptores Angular:** El frontend cuenta con interceptores reactivos HTTP que capturan cualquier respuesta `401 Unauthorized` desde Symfony. Si el token caduca, la sesión se limpia y el usuario es redirigido inmediatamente al login de forma elegante.
*   **Regenerar Claves JWT:** Si por alguna razón de despliegue limpio las llaves del backend fallan o no se encuentran, se pueden generar instantáneamente corriendo:
    ```bash
    docker compose exec php php bin/console lexik:jwt:generate-keypair
    ```

---

### 4.3. Conflictos de Puertos Docker (8080, 5432, 15672)

Si al arrancar el contenedor con `make up` u `opción producción` el sistema indica que un puerto local ya se encuentra ocupado:

*   **Puerto 8080 (Servidor Web):** Asegúrate de detener servicios como IIS en Windows, Apache locales o Nginx locales que estén preinstalados en tu máquina.
*   **Puerto 5432 (PostgreSQL):** Si dispones de un servicio nativo de base de datos Postgres corriendo en segundo plano en el sistema anfitrión, detenlo temporalmente:
    *   *Windows (PowerShell Administrador):* `Stop-Service postgresql*`
    *   *Linux / macOS:* `sudo service postgresql stop`

---

### 4.4. Reseteo Completo de la Base de Datos y Seeds

Si por motivos de pruebas masivas la base de datos contiene registros incoherentes y deseas reestablecerla de forma limpia para una demostración ante tribunal, sigue esta secuencia técnica:

```bash
# Acceder a la consola de comandos de PHP
make php

# Ejecutar el reestablecimiento completo de base de datos
php bin/console doctrine:database:drop --force --if-exists
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n

# Cargar los datos maestros semilla
php bin/console app:seed-admin
php bin/console app:seed-demo
```
