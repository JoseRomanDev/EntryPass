# 🎬 Guía de Demo — Script para Vídeo/Capturas EntryPass

> **Propósito**: Esta guía es el guión paso a paso para grabar la demo del proyecto.  
> Sigue el orden exacto de las secciones para contar una historia coherente y profesional.

---

## ⚙️ 0. Preparación ANTES de grabar (5 min)

Ejecuta estos comandos **en este orden exacto** desde la raíz del proyecto:

```bash
# 1. Levantar el entorno de producción (Nginx + PHP-FPM)
make prod-up

# 2. ⚠️ OBLIGATORIO: Crear las tablas en la base de datos (SIEMPRE tras un deploy limpio)
make migrate

# 3. Cargar eventos de demo + usuario normal
make seed-demo

# 4. Crear el usuario administrador
make seed
```

> ⚠️ **IMPORTANTE**: Si saltas el `make migrate` y ejecutas directamente `make seed-demo`, obtendrás el error `relation "users" does not exist`. Las migraciones crean las tablas — sin ellas la BD está vacía.

> ✅ **Comprueba** que `http://localhost` carga la web con los eventos antes de empezar a grabar.

---

## 👤 Credenciales de Usuarios

| Rol | Email | Contraseña | Descripción |
|-----|-------|------------|-------------|
| 🔵 **Usuario normal** | `user@entrypass.com` | `User123!` | Cliente que compra entradas |
| 🔴 **Administrador** | `admin@entrypass.com` | `Admin123!` | Gestiona eventos desde el panel |

---

## 💳 Tarjetas de Crédito Simuladas (Mock Stripe)

> El sistema de pago es una **pasarela simulada** (hexagonal architecture → `SimulatedPaymentGatewayAdapter`).  
> El frontend valida el formato de la tarjeta; el backend decide si el pago es aprobado o rechazado.

### ✅ Tarjeta que FUNCIONA (pago aprobado)

Introduce **cualquier tarjeta con formato válido** — el backend aprueba el ~95% de los pagos:

| Campo | Valor a introducir |
|-------|--------------------|
| **Nombre en tarjeta** | `Jose Manuel Test` |
| **Número** | `4242 4242 4242 4242` |
| **Caducidad** | `12/28` |
| **CVC** | `123` |

> ✅ El pago se procesará con un delay de 1.5s (realismo simulado) y mostrará la confirmación.

---

### ❌ Tarjeta que FALLA (formato inválido — error en frontend)

Para demostrar la **validación del formulario** antes de enviar, introduce datos incompletos:

| Campo | Valor a introducir |
|-------|--------------------|
| **Nombre en tarjeta** | `Jose` *(menos de 4 caracteres → botón desactivado)* |
| **Número** | `1234 5678` *(menos de 16 dígitos → botón desactivado)* |
| **Caducidad** | `0` |
| **CVC** | `1` |

> ❌ El botón **"Pagar"** permanecerá desactivado (`disabled`). Así demuestras la **validación en tiempo real del frontend**.

---

### ⚠️ Pago rechazado por el BACKEND (fondos insuficientes simulados)

> El backend tiene un **5% de probabilidad aleatoria** de rechazar cualquier pago.  
> Para garantizar que se vea este escenario en la demo, puedes modificar temporalmente el adaptador:

**Archivo**: `backend/src/Infrastructure/Adapter/SimulatedPaymentGatewayAdapter.php`

```php
// ANTES (producción normal — falla el 5%)
if ($random <= 5) {
    throw new \Exception("Pago rechazado: Fondos insuficientes en la tarjeta simulada.");
}

// PARA LA DEMO — Cambia temporalmente a 100 para forzar el error siempre:
if ($random <= 100) {
    throw new \Exception("Pago rechazado: Fondos insuficientes en la tarjeta simulada.");
}
```

> Después de grabar ese clip, **revierte el cambio** a `<= 5`.

---

## 🎬 Script del Vídeo (Orden de grabación)

### 🎯 Escena 1 — La Landing Page (sin login)
**Objetivo**: Mostrar la home y el carrusel de eventos.

1. Abre `http://localhost` en el navegador.
2. Muestra el **carrusel dinámico** de eventos destacados.
3. Baja para ver la **grid de eventos** con categorías.
4. Usa la **barra de búsqueda** → escribe `Rock` → muestra el filtro en tiempo real.
5. Haz clic en la tarjeta de **"Rock Arena Festival 2026"** para ir al detalle.

---

### 🎯 Escena 2 — AuthGuard en acción
**Objetivo**: Demostrar la protección de rutas.

1. En la página de detalle del evento, haz clic en **"Comprar Entrada"**.
2. Muestra cómo el sistema te **redirige automáticamente al Login** (con `returnUrl` en la URL).
3. Esto demuestra el `AuthGuard` de Angular en funcionamiento.

---

### 🎯 Escena 3 — Registro de nuevo usuario (OPCIONAL)
**Objetivo**: Mostrar el formulario de registro con validaciones.

1. Haz clic en **"Registrarse"**.
2. Muestra la validación de campos en tiempo real (email inválido, contraseña corta, etc.).
3. Registra un nuevo usuario de prueba si lo deseas, o usa las credenciales del paso siguiente.

---

### 🎯 Escena 4 — Login como usuario normal
**Objetivo**: Autenticar con el usuario cliente.

1. Ve a `/auth/login`.
2. Introduce:
   - **Email**: `user@entrypass.com`
   - **Contraseña**: `User123!`
3. Haz clic en **"Iniciar Sesión"**.
4. Muestra cómo el sistema te redirige de vuelta al evento gracias al `returnUrl`.

---

### 🎯 Escena 5 — Compra exitosa (tarjeta válida)
**Objetivo**: El momento WOW de la demo.

1. Estás en la página del evento **"Rock Arena Festival 2026"**.
2. Haz clic en **"Comprar Entrada"**.
3. Se abre el **Modal de Checkout** estilo Stripe.
4. Selecciona **2 entradas** con el selector `+`.
5. Introduce los datos de la tarjeta que FUNCIONA:
   - Nombre: `Jose Manuel Test`
   - Número: `4242 4242 4242 4242`
   - Caducidad: `12/28`
   - CVC: `123`
6. Haz clic en **"Pagar"** — observa el **spinner de carga** (1.5s de delay simulado).
7. Aparece el mensaje de **✅ ¡Compra Confirmada!** con el número de tickets.
8. Menciona verbalmente: *"En este momento, el backend ya ha enviado el mensaje a RabbitMQ para procesar el email con el PDF"*.

---

### 🎯 Escena 6 — Validación de tarjeta fallida (frontend)
**Objetivo**: Mostrar la validación del formulario.

1. Abre el modal de checkout de otro evento.
2. Introduce los datos incompletos de la **tarjeta que falla en frontend**:
   - Nombre: `Jo`
   - Número: `1234`
3. Muestra que el **botón "Pagar" está desactivado** (gris, no clickable).
4. Explica: *"El formulario valida 16 dígitos, fecha y CVC antes de habilitar el botón"*.

---

### 🎯 Escena 7 — Error de pago del backend (fondos insuficientes)
**Objetivo**: Mostrar el error de rechazo por el backend.

> ⚠️ Si modificaste el adaptador para forzar el error (`<= 100`), graba esta escena ahora.

1. Introduce los datos de la **tarjeta válida** (formato correcto).
2. Haz clic en **"Pagar"**.
3. Tras el delay de 1.5s, aparece el mensaje de error:  
   *"Pago rechazado: Fondos insuficientes en la tarjeta simulada."*
4. Explica: *"Este error viene del backend — la pasarela simulada rechazó el pago"*.

---

### 🎯 Escena 8 — Mis Entradas y Códigos QR
**Objetivo**: Mostrar el perfil y los tickets generados.

1. Navega a **Perfil → Mis Entradas** (icono de usuario o menú).
2. Muestra el listado de compras realizadas.
3. Despliega una compra — aparecen los **Códigos QR individuales**.
4. Explica: *"Cada QR contiene un hash UUID único e irrepetible para prevenir fraude"*.

---

### 🎯 Escena 9 — Panel de Administración
**Objetivo**: Mostrar el rol de administrador.

1. Cierra sesión del usuario normal.
2. Inicia sesión como:
   - **Email**: `admin@entrypass.com`
   - **Contraseña**: `Admin123!`
3. Observa cómo aparece el menú **"Administración"** en la navbar (no visible para usuarios normales).
4. Accede al **Panel de Gestión de Eventos**.
5. Muestra la lista de eventos existentes.
6. Haz clic en **"Nuevo Evento"** — rellena el formulario con un evento de prueba.
7. Guarda y vuelve a la Home — muestra que el nuevo evento aparece en el catálogo.
8. Vuelve al panel y demuestra la **Baja Lógica** (eliminar sin borrar datos reales).

---

### 🎯 Escena 10 (BONUS) — RabbitMQ Management
**Objetivo**: Demostrar el sistema de mensajería asíncrona.

1. Abre en otra pestaña: `http://localhost:15672`
2. Login: `guest` / `guest`
3. Muestra la interfaz de gestión de colas.
4. Explica: *"Cuando se realiza una compra, el backend publica un mensaje en esta cola. El worker lo consume de forma asíncrona para generar el PDF y enviar el email sin bloquear la respuesta al usuario"*.

---

## ⏱️ Duración estimada por escena

| Escena | Descripción | Tiempo aprox. |
|--------|-------------|---------------|
| 0 | Preparación | (no grabar) |
| 1 | Landing Page | 1 min |
| 2 | AuthGuard | 30 seg |
| 3 | Registro | 1 min (opcional) |
| 4 | Login usuario | 30 seg |
| 5 | Compra exitosa | 2 min ⭐ |
| 6 | Validación frontend | 1 min |
| 7 | Error backend | 1 min |
| 8 | Mis Entradas / QR | 1 min |
| 9 | Panel Admin | 2 min |
| 10 | RabbitMQ | 1 min (bonus) |
| **Total** | | **~10-12 min** |

---

## 📝 Notas de producción

- **Resolución**: Graba a 1920×1080 mínimo.
- **Zoom**: Usa el zoom del navegador al 110-125% para que el texto sea legible en vídeo.
- **Limpieza**: Cierra pestañas innecesarias antes de grabar.
- **Datos**: Si la BD está sucia, ejecuta `make reset` + `make seed-demo` + `make seed-admin`.
- **Audio**: Si grabas con narración, habla despacio. Si no, añade subtítulos/anotaciones al vídeo.

---

*Guía generada para la defensa del TFG — EntryPass © 2026*
