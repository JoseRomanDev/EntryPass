# EntryPass

**EntryPass** es un proyecto personal desarrollado como Trabajo de Fin de Grado (TFG) para el ciclo de Desarrollo de Aplicaciones Web (DAW). 

Consiste en una **plataforma web de compra de entradas para eventos** (similar a Eventbrite o Ticketmaster) que permite a los usuarios registrarse, explorar eventos, comprar entradas y recibir un código QR único para acceder al evento.

El objetivo académico de este proyecto es demostrar el uso y dominio de una **arquitectura moderna**, basada en contenedores, un frontend SPA, un backend con arquitectura hexagonal y procesamiento asíncrono utilizando colas de mensajes.

---

##  Stack Tecnológico

El proyecto está construido utilizando tecnologías modernas y estándares de la industria:

### Frontend
* **Angular 21** (SPA basado en features y Signals)
* **Diseño High-Fidelity**: Interfaz moderna con estética Premium Dark, paleta Cyan/Teal y componentes altamente interactivos.
* Comunicación fluida con API REST y gestión de estado reactiva.

### Backend
* **Symfony 7** (API REST)
* **PHP 8.4** con tipado estricto
* Arquitectura Hexagonal (Ports & Adapters)
* Symfony Messenger para la gestión de encolado de procesos
* **LexikJWT** para autenticación basada en tokens
* **PHPUnit** para testing unitario y funcional

### Infraestructura y Servicios
* **Docker & Docker Compose**: Contenerización completa de la aplicación (6 servicios en desarrollo, 5 en producción).
* **Nginx**: Servidor web principal y Reverse Proxy. En producción sirve directamente los estáticos compilados de Angular.
* **PostgreSQL 16**: Base de datos relacional para la persistencia de datos.
* **RabbitMQ**: Broker de mensajería para desacoplar tareas pesadas (QRs, PDFs, emails).

---

##  Arquitectura del Sistema

El sistema cuenta con una arquitectura robusta de **SPA + API REST + Procesamiento Asíncrono**. El proyecto implementa el **flujo asíncrono de compra de entradas** cumpliendo estrictamente con el patrón de **Puertos y Adaptadores (Arquitectura Hexagonal)**.

### Flujo Global de la Aplicación
1. **Flujo Principal:** `Usuario -> Angular SPA -> Nginx (Proxy) -> Symfony API -> PostgreSQL`.
2. **Procesamiento Asíncrono:** Las tareas pesadas (generación automática de códigos QR, PDFs unificados para los tickets y envío de emails) se delegan a través de **RabbitMQ**. Un contenedor `worker` (consumidor de Symfony Messenger) las procesa en segundo plano para garantizar una respuesta inmediata al usuario.

### Arquitectura Backend (Hexagonal)
El backend aísla el dominio lógico del sistema de sus implementaciones tecnológicas:

* **Capa de Dominio (Domain)**: (Cero dependencias externas).
  * Entidades principales definidas: `User`, `Event`, `Purchase`, `Ticket`. La lógica diferencia claramente la transacción general de la compra frente a las entradas individuales.
  * Interfaces de repositorios (Ports) establecidas (`EventRepositoryInterface`, etc.).
* **Capa de Aplicación (Application)**:
  * Casos de uso centrales: `CreateEventHandler`, `RegisterUserHandler`, `ProcessPurchaseHandler`.
  * **Puertos de Aplicación**: Definición estricta de interfaces abstractas (`QrCodeGeneratorInterface`, `PdfGeneratorInterface`, `EmailSenderInterface`, `PaymentGatewayInterface`) para desacoplar la lógica de negocio de librerías concretas.
  * Procesamiento asíncrono: `SendPurchaseEmailHandler` (vía Symfony Messenger) orquesta la generación visual de los tickets y el envío de estos.
* **Capa de Infraestructura (Infrastructure)**:
  * **Adaptadores Hexagonales**: Implementaciones tecnológicas concretas inyectadas transparentemente (`DompdfAdapter`, `EndroidQrCodeAdapter`, `SymfonyEmailSenderAdapter`, `SimulatedPaymentGateway`).
  * Controladores REST funcionales para cada dominio principal (`User`, `Event`, `Purchase`, `Ticket`).
  * Persistencia configurada usando Doctrine ORM y colas a través de RabbitMQ.

### Despliegue: Desarrollo vs Producción

El proyecto dispone de **dos modos de despliegue** con Docker Compose:

| Aspecto | Desarrollo (`compose.yml`) | Producción (`compose.prod.yml`) |
|---|---|---|
| **Frontend** | `ng serve` con hot-reload | `ng build` → estáticos optimizados |
| **Nginx** | Proxy a `node:4200` | Sirve estáticos directamente |
| **Contenedores** | 6 (incluye `node`) | 5 (sin `node`) |
| **Comando** | `make up` | `make prod-up` |

**Arquitectura en producción:**
```
ng build → genera estáticos optimizados (≈80 kB transferidos)
Usuario → Nginx (:8080) → sirve estáticos Angular directamente
                        → proxy /api → PHP-FPM (:9000)
```

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

4.  **Generar la Base de Datos (Migraciones):**
    Ejecuta las migraciones de Doctrine para que se creen las tablas en PostgreSQL:
    ```bash
    docker compose exec php php bin/console doctrine:migrations:migrate -n
    ```

5.  **Generar claves JWT:**
    Obligatorio para que el sistema de login funcione:
    ```bash
    docker compose exec php php bin/console lexik:jwt:generate-keypair
    ```

6.  **Insertar usuario administrador (Seed):**
    Para poder probar el panel de administración, inserta el usuario por defecto en la BD:
    ```bash
    make seed
    ```

    > [!NOTE]
    > Esto creará el usuario `admin@entrypass.com` con la contraseña `Admin123!`.

7.  **(Opcional) Insertar eventos de demostración:**
    ```bash
    make seed-demo
    ```

8.  **Acceder a la Plataforma:**
    Todo listo. Abre tu navegador y dirígete a:
    **[http://localhost:8080](http://localhost:8080)**

### Levantar en Modo Producción

Para desplegar con `ng build` (estáticos optimizados servidos por Nginx):

```bash
make prod-up
```

Esto ejecuta un **Dockerfile multi-stage** que:
1. Compila Angular en modo producción (`ng build --configuration=production`)
2. Copia los estáticos resultantes a una imagen Nginx
3. Nginx sirve directamente los archivos sin necesidad de un servidor Node

> [!NOTE]
> Para volver al modo desarrollo con hot-reload: `make prod-down` y luego `make up`.

### 🎓 Guía Rápida — Demostración ante el Jurado

Secuencia completa de comandos para poner en marcha el proyecto **desde cero** y demostrar su funcionamiento con datos de prueba:

```bash
# 1. Clonar y entrar al proyecto
git clone <url-del-repositorio>
cd EntryPass

# 2. Construir y levantar todos los contenedores en modo producción
make prod-up

# 3. Instalar dependencias del backend (Symfony)
docker compose -f compose.prod.yml exec php composer install

# 4. Crear las tablas en la base de datos (migraciones)
docker compose -f compose.prod.yml exec php php bin/console doctrine:migrations:migrate -n

# 5. Generar las claves JWT (necesario para el login)
docker compose -f compose.prod.yml exec php php bin/console lexik:jwt:generate-keypair

# 6. Insertar el usuario administrador
docker compose -f compose.prod.yml exec php php bin/console app:seed-admin

# 7. Insertar eventos de demostración (datos de prueba)
docker compose -f compose.prod.yml exec php php bin/console app:seed-demo
```

> [!IMPORTANT]
> Una vez ejecutados todos los comandos, la plataforma estará disponible en:
> **[http://localhost:8080](http://localhost:8080)**

#### Credenciales de prueba

| Rol | Email | Contraseña |
|---|---|---|
| **Administrador** | `admin@entrypass.com` | `Admin123!` |
| **Usuario normal** | _(registrarse desde la app)_ | _(libre)_ |

#### Flujo de demostración sugerido

1. **Página principal** → Mostrar el catálogo de eventos con los datos de demo cargados
2. **Registro de usuario** → Crear una cuenta nueva desde `/auth/register`
3. **Explorar un evento** → Clic en un evento para ver su detalle, aforo y precio
4. **Comprar entradas** → Flujo de checkout con simulación de pago tipo Stripe
5. **Mi Perfil** → Ver las entradas compradas con sus códigos QR individuales
6. **Panel de Administración** → Iniciar sesión como `admin@entrypass.com` y acceder a `/admin/events` para crear, editar y eliminar eventos
7. **Tests del backend** → Ejecutar la suite de PHPUnit:
   ```bash
   docker compose -f compose.prod.yml exec php php bin/phpunit
   ```

#### Para apagar el proyecto al finalizar

```bash
make prod-down
```

### Comandos del Makefile

| Comando | Descripción |
|---|---|
| **Desarrollo** | |
| `make up` | Levanta todos los contenedores en segundo plano |
| `make down` | Detiene y elimina los contenedores |
| `make start` / `make stop` | Reanuda / pausa contenedores sin eliminarlos |
| `make build` | Reconstruye las imágenes de Docker |
| `make logs` | Muestra los logs de todos los contenedores |
| **Producción** | |
| `make prod-up` | Levanta el entorno de producción (con build) |
| `make prod-down` | Detiene el entorno de producción |
| `make prod-build` | Reconstruye las imágenes de producción |
| `make prod-logs` | Muestra los logs de producción |
| **Acceso** | |
| `make php` | Accede a la terminal del Backend (Symfony) |
| `make node` | Accede a la terminal del Frontend (Angular) |
| `make psql` | Accede a la consola SQL de PostgreSQL |
| `make seed` | Inserta el usuario administrador inicial |
| `make seed-demo` | Inserta los eventos de demostración |

### Notas y Solución de Problemas

> [!NOTE]
> **Comandos Útiles:** Usa `make build` si realizas cambios en los Dockerfiles y `make down` para detener los servicios.

*   **Error "compose not found"**: Asegúrate de usar `docker compose` (con espacio) en lugar de `docker-compose` (con guion) si estás en versiones modernas de Docker. El comando `make up` gestiona esto automáticamente por ti.
*   **Permisos de Claves JWT**: Si recibes errores de autenticación, verifica que las claves en `backend/config/jwt/` tengan los permisos adecuados o vuelve a generarlas.
*   **Acceso Web**: 
    *   Aplicación: [http://localhost:8080](http://localhost:8080) (Nginx)
    *   Angular Dev Server: [http://localhost:4200](http://localhost:4200) (solo en modo desarrollo)
    *   RabbitMQ Panel: [http://localhost:15672](http://localhost:15672) (guest/guest)

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

##  Testing (PHPUnit)

El proyecto incluye una suite de pruebas automatizadas para el backend:

```bash
# Ejecutar todos los tests
docker compose exec php php bin/phpunit

# Ejecutar solo tests unitarios
docker compose exec php php bin/phpunit tests/Unit
```

Los tests cubren los casos de uso principales de la Arquitectura Hexagonal:
* **Unitarios**: Handlers de Application (`PurchaseTicketHandler`, `DeleteEventHandler`, `ValidateTicketHandler`, etc.) con Mocks y Stubs.
* **Funcionales**: Validación end-to-end de los flujos de negocio.

---

## Hitos y Evolución del Proyecto

Puedes consultar el detalle del proyecto y las guías de uso en los siguientes enlaces:

**[Manual de Usuario (Clientes y Administradores)](docs/manual_usuario.md)**  
**[Informe de Mejoras (Fase Técnica)](docs/informe_mejoras.md)**

### Hitos Alcanzados

* **Despliegue de Producción con Docker (ng build):**
  * Implementación de un Dockerfile **multi-stage** para el frontend que compila Angular con `ng build --configuration=production` y sirve los estáticos optimizados directamente desde Nginx.
  * Creación de `compose.prod.yml` separado del entorno de desarrollo, eliminando la dependencia del servidor Node en producción.
  * Configuración de cache agresiva para assets con hash (`expires 1y`, `Cache-Control: public, immutable`).
* **Frontend SPA Completado Plenamente (Lógica de Compra y Perfil):**
  * Se implementó exitosamente el flujo de Checkout end-to-end conectando Angular con la API Symfony.
  * Interfaz de Alta Fidelidad en el Modal de compra con gestión de estado a través de _Signals_ de Angular (selectores de cantidad y validación de stock visual).
  * Panel de "Mis Entradas" finalizado. Ahora expone y renderiza al vuelo los Códigos QR de cada entrada de forma individualizada a partir del Hash securitizado en base de datos.
  * Se incorporó el redireccionamiento fluído de autenticación (`Guards`) en la pasarela, para recordar qué intentaba comprar un visitante y redirigirlo inmediatamente tras el Login sin perder recorrido.
* **Endpoint de Validación de QRs (Portería):** 
  * Se ha desarrollado e implementado un endpoint protegido (`/api/tickets/validate`) para verificar presencialmente la autenticidad de una entrada escaneada y marcar su estado como consumido (`valid` -> `used`), asegurando el ecosistema contra fraude.
* **Simulación de Pasarela de Pagos (Stripe Mock):**
  * Incorporación de una simulación de cobro realista en el backend antes de autorizar y encolar la creación definitiva de los tickets. Implementado mediante Patrón de Diseño Hexagonal (Ports & Adapters) garantizando desacoplamiento total. Todo fuertemente respaldado por testing en **PHPUnit** y tests funcionales para asegurar la inviolabilidad de los datos en fallos transaccionales.
  * Construcción de una interfaz de Checkout hiperrealista encapsulando _Stripe Elements_ directamente en Angular, dotando a la simulación de confirmación y procesamiento con _fake delays_ que consolidan un aspecto profesional.
* **Rediseño Estético de Alta Fidelidad (UI/UX):**
  * Transformación integral de la identidad visual de la aplicación. Migración a un sistema de diseño basado en fondo negro absoluto y acentos Cyan (`#26b1c4`), con una arquitectura de componentes optimizada para una experiencia fluida y premium.
* **Autenticación Resiliente e Interceptores:**
  * Implementación de Interceptores HTTP reactivos para gestionar el cierre de sesión transparente frente a tokens caducados, reparando errores `401 Unauthorized` en rutas públicas y eliminando pantallas de carga infinitas.
  * Mejoras drásticas de UX en componentes: visualización de contraseña mediante SVG en login y redirecciones automáticas (`Router Guards`).
* **Panel de Administración Refinado:**
  * Refactorización del **Dashboard** administrativo eliminando por completo alertas nativas del navegador a favor de modales customizados de Alta y Baja Lógica. Sustitución total de emojis OS por iconografía SVG elegante adaptada al *Dark Mode*.
* **Optimización y Estabilización de Tests (PHPUnit):**
  * Saneamiento integral de la suite de pruebas del backend. Se eliminaron todas las deprecaciones de PHP 8.4 y avisos de PHPUnit 10+, logrando una ejecución 100% limpia (Exit Code 0).
  * Blindaje de la lógica de negocio en la Arquitectura Hexagonal mediante la correcta implementación de Stubs y Mocks con atributos nativos de PHPUnit.
* **Auditoría de Accesibilidad y SEO (Lighthouse Ready):**
  * Optimización del Frontend SPA para cumplir con estándares de accesibilidad (A11y).
  * Implementación de etiquetas `aria-label` en elementos interactivos, vinculación explícita de formularios (`label for`), y marcado de elementos decorativos para lectores de pantalla.
  * Configuración completa de Meta-etiquetas SEO y jerarquía semántica HTML5 en todas las vistas principales.
