# 🎓 Guía de Preparación para la Defensa del TFG — EntryPass

Este documento resume los puntos clave, la narrativa técnica y el flujo de demostración ideal para defender el proyecto ante el tribunal.

---

## 🚀 1. Preparación del Entorno (Checklist)

Antes de que entre el tribunal, asegúrate de que todo está listo para evitar imprevistos:

1.  **Modo Producción**: Asegúrate de estar en `make prod-up`. El tribunal valorará que la aplicación se sirva mediante **Nginx** con estáticos compilados y no mediante un servidor de desarrollo.
2.  **Datos Limpios**: Ejecuta `make seed-demo` para tener eventos con imágenes y descripciones reales.
3.  **RabbitMQ**: Ten abierta la pestaña de `http://localhost:15672` (guest/guest) por si te preguntan sobre la cola de mensajes.
4.  **Terminal Lista**: Ten una terminal abierta en la raíz del proyecto por si te piden ejecutar los tests (`make php` -> `vendor/bin/phpunit`).

---

## 🏛️ 2. Puntos Técnicos "Estrella" (Qué resaltar)

Si el jurado te pregunta "¿Por qué es este proyecto nivel TFG?", destaca estos tres pilares:

### A. Arquitectura Hexagonal (Ports & Adapters)
*   **Justificación**: "He separado las reglas de negocio (Dominio) de la tecnología (Infraestructura). Si mañana queremos cambiar la base de datos o el sistema de correos, el núcleo de mi aplicación no cambia."
*   **Donde mostrarlo**: En el backend, enseña la carpeta `src/Domain` (sin dependencias) vs `src/Infrastructure`.

### B. Procesamiento Asíncrono (RabbitMQ)
*   **Justificación**: "La generación de PDFs y códigos QR es pesada. Para no hacer esperar al usuario, el backend registra la compra y delega el trabajo pesado a una cola de mensajes en segundo plano."
*   **Narrativa**: Habla de "User Experience" y "Escalabilidad".

### C. Frontend de Alta Fidelidad y Diseño (UI/UX)
*   **Justificación**: "La rúbrica exige una interfaz usable. He utilizado Angular 21 (Zoneless + Signals) y he maquetado todo usando CSS nativo, basándome en un **prototipado previo en Figma**. El diseño es responsivo y aplica *Glassmorphism*."
*   **Diseño**: Resalta la paleta de colores (Modo Oscuro + Cyan) y la ausencia de frameworks pesados de diseño para maximizar el rendimiento.

### D. Testing y Calidad de Código
*   **Justificación**: "Para garantizar que el backend no tenga errores críticos, he implementado **Tests Unitarios y Funcionales con PHPUnit**, testeando especialmente la pasarela simulada y los Handlers de compra."
*   **Donde mostrarlo**: Menciona que los tests pasan en verde (Exit Code 0) asegurando la fiabilidad transaccional.

---

## 🎭 3. Flujo de Demostración Sugerido (15-20 min máximo)

Sigue este orden para "contar una historia" con la app:

0.  **Presentación (5 min)**: Usa las diapositivas (`presentacion_tfg.pdf`) como hilo conductor inicial. Presenta el problema, los objetivos y la comparativa técnica antes de abrir el navegador.
1.  **La Landing (Home)**: Muestra el **carrusel dinámico**. Explica que no es estático, sino que extrae los últimos eventos de la API.
2.  **El Problema**: Intenta comprar una entrada sin estar logueado. Muestra cómo el `AuthGuard` te redirige al Login y recuerda que querías comprar esa entrada específica (`returnUrl`).
3.  **El Registro/Login**: Destaca la estética cuidada y la validación de formularios en tiempo real.
4.  **La Compra (El momento WOW)**:
    *   Entra en un evento.
    *   Abre el modal de compra.
    *   Usa la **pasarela simulada (Stripe Mock)**. Habla de cómo has replicado la experiencia de una pasarela real con latencias intencionadas.
    *   **Importante**: Menciona que al darle a "Pagar", el sistema ya ha enviado el mensaje a RabbitMQ.
5.  **Mis Entradas**: Ve al perfil. Muestra los códigos QR. Explica que se renderizan a partir de un Hash único para evitar fraude.
6.  **Panel Admin**: Entra como `admin@entrypass.com`. Crea o edita un evento y muestra cómo la Home se actualiza al instante.

---

## ❓ 4. Posibles Preguntas "Trampa" y Respuestas

| Pregunta | Respuesta Recomendada |
|----------|-----------------------|
| **¿Por qué PHP/Symfony y no Node/Express?** | "Symfony me aporta una estructura empresarial muy sólida y herramientas nativas como Messenger para RabbitMQ que Express no tiene por defecto." |
| **¿Cómo gestionas la seguridad?** | "Uso **JWT** (JSON Web Tokens) para las sesiones y he configurado cabeceras de seguridad en Nginx. Además, los QRs están basados en Hashes UUID, previniendo inyecciones o fraudes." |
| **¿Por qué no está desplegado en un Cloud público con HTTPS? (CRÍTICO para la Rúbrica)** | "El proyecto está preparado (*Production-ready*) con Docker Multi-stage y Nginx. He priorizado cerrar de forma sobresaliente la Arquitectura Hexagonal y el asincronismo. El despliegue Cloud real (ej. DigitalOcean) es el siguiente paso lógico ('Líneas futuras'), ya que localmente simula el entorno productivo al 100%." |
| **¿Qué harías si el servidor de correos falla?** | "Gracias a RabbitMQ, los mensajes se quedan en la cola. Puedo configurar reintentos automáticos para que el ticket se envíe en cuanto el servicio de correo vuelva a estar activo." |
| **¿Por qué no usaste una base de datos NoSQL?** | "Para entradas y aforos, la integridad relacional y las transacciones ACID son críticas. PostgreSQL garantiza que no se venda la misma entrada dos veces (sobreventa)." |

---

## 💎 5. Detalles de "Excelencia" (Menciónalos si tienes tiempo)

*   **Rendimiento**: "He configurado compresión Gzip en el servidor para que la web cargue más rápido."
*   **Accesibilidad**: "La web cumple con estándares de accesibilidad (etiquetas ARIA, contraste alto, navegación por teclado)."
*   **Docker**: "El proyecto es 100% portable; se despliega con un solo comando independientemente del Sistema Operativo."

---

*¡Mucha suerte en la defensa, José Manuel! El proyecto está a un nivel excelente.*
