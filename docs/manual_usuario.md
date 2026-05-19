# Manual del Cliente - EntryPass

¡Bienvenido al **Manual del Cliente de EntryPass**! Este documento detalla de manera sencilla y clara cómo registrarse, buscar tus eventos favoritos, comprar entradas de forma segura y acceder a tus tickets digitales con códigos QR para presentarlos el día del espectáculo.

---

## 📌 Índice de Contenidos

1. [Registro e Inicio de Sesión](#1-registro-e-inicio-de-sesión)
2. [Exploración y Búsqueda de Eventos](#2-exploración-y-búsqueda-de-evetos)
3. [Proceso de Compra (Checkout)](#3-proceso-de-compra-checkout)
4. [Mis Entradas y Códigos QR](#4-mis-entradas-y-códigos-qr)
5. [Preguntas Frecuentes y Soporte al Comprador](#5-preguntas-frecuentes-y-soporte-al-comprador)

---

## 👤 1. Registro e Inicio de Sesión

Para poder adquirir entradas en la plataforma y realizar un seguimiento de tus compras, necesitas disponer de una cuenta activa:

*   **Creación de Cuenta:** Haz clic en **Registrarse** en la barra de navegación superior. Completa los campos básicos del formulario (nombre, correo electrónico y contraseña). El sistema validará en tiempo real que el correo sea correcto y que la contraseña sea segura.
*   **Verificar Contraseña:** Puedes hacer clic en el icono del ojo SVG en el campo de contraseña para mostrar/ocultar los caracteres escritos y asegurarte de que no haya erratas.
*   **Acceso y Protección (AuthGuard):** Si intentas realizar una compra sin estar autenticado, no te preocupes: el sistema recordará automáticamente qué entrada querías comprar, te redirigirá a la pantalla de Login y, tras introducir tus credenciales con éxito, te devolverá de forma fluida directamente al proceso de checkout en el que estabas.

---

## 🔍 2. Exploración y Búsqueda de Eventos

*   **Catálogo Principal (Home):** En la página de inicio dispones de un carrusel dinámico interactivo con espectáculos destacados y una cuadrícula (grid) donde se muestran todos los eventos programados organizados en tarjetas cromáticas.
*   **Filtro en Tiempo Real:** Escribe cualquier palabra clave en la barra de búsqueda de la Home. El listado de eventos se filtrará instantáneamente en pantalla a medida que escribes, sin recargas de página complejas.
*   **Ficha del Evento:** Haz clic en cualquier tarjeta de evento para inspeccionar todos los detalles específicos del espectáculo: fecha, descripción amplia, precio de la entrada y el aforo disponible restante en tiempo real.

---

## 💳 3. Proceso de Compra (Checkout)

> [!TIP]
> **Política de Límites:** Por motivos de seguridad y para evitar la reventa masiva, el sistema impone un **límite máximo de 4 entradas** por compra y evento para cada usuario.

1.  Haz clic en **Comprar Entrada** desde la página del evento de tu interés.
2.  Se desplegará una ventana emergente interactiva de alta fidelidad inspirada en la experiencia de *Stripe Elements*.
3.  Utiliza los botones `+` y `-` para elegir la cantidad de entradas. El modal recalculará el coste total al instante.
4.  Introduce los datos de tu tarjeta bancaria simulada (puedes utilizar la clásica tarjeta de pruebas `4242 4242 4242 4242`).
5.  Haz clic en el botón **Pagar**:
    *   La plataforma simulará el procesamiento financiero en tiempo real mediante un loader visual interactivo de 1.5 segundos.
    *   **Procesamiento en Segundo Plano:** Una vez autorizado el pago por el backend, se te confirmará la compra de inmediato en pantalla. El sistema de colas (RabbitMQ) se encargará en segundo plano de generar tu ticket PDF y enviártelo al correo electrónico registrado para evitar tiempos de espera innecesarios.

---

## 🎫 4. Mis Entradas y Códigos QR

Una vez que tu compra se complete correctamente, tendrás tus entradas siempre accesibles desde cualquier dispositivo móvil u ordenador:

*   Accede al menú superior y pulsa en la sección **Mis Entradas**.
*   Allí aparecerá tu historial completo de compras organizadas por fechas e importes.
*   Haz clic sobre cualquier compra para desplegarla. Verás el **Código QR individualizado** por cada ticket adquirido.
*   **Seguridad Digital:** El código QR contiene un Hash UUID encriptado seguro. El día del espectáculo, el personal de portería escaneará este código para validar que la entrada es auténtica y autorizar tu acceso en milisegundos.

---

## ❓ 5. Preguntas Frecuentes y Soporte al Comprador

*   **¿Cuánto tarda en llegar el email con mi entrada en PDF?**
    Generalmente se envía en pocos segundos tras completar el pago. Si no lo visualizas en tu bandeja de entrada principal, revisa la carpeta de Correo No Deseado (SPAM).
*   **¿Puedo llevar la entrada en el móvil o tengo que imprimirla?**
    No es necesario imprimirla. EntryPass está maquetada con diseño responsivo premium, por lo que puedes abrir la sección de **Mis Entradas** en tu smartphone y enseñar el código QR directamente en el control de accesos.
*   **Mi sesión se ha cerrado sola tras un tiempo, ¿es normal?**
    Sí. Para proteger tus datos y transacciones, la sesión expira automáticamente tras un período prolongado de inactividad. El sistema cerrará la sesión de forma segura y solo tendrás que volver a iniciarla.
