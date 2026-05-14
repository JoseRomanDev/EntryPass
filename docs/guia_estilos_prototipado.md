# Guía de Estilos y Prototipado (UI/UX)
## Proyecto EntryPass

La presente guía documenta las decisiones de diseño, la paleta cromática, la tipografía y el prototipado previo realizado para el desarrollo de la interfaz de **EntryPass**, asegurando la coherencia visual y el cumplimiento de los estándares modernos de desarrollo de interfaces web.

---

## 1. Metodología de Prototipado

Antes de la fase de desarrollo frontend en Angular, se realizó un proceso de diseño enfocado en la Experiencia de Usuario (UX) y la Interfaz de Usuario (UI) utilizando la herramienta de prototipado **Figma**.

### Objetivos del Prototipado:
- **Flujo de Usuario (User Flow):** Mapear el recorrido completo desde que el usuario entra sin registro, busca un evento, pasa por el login y finaliza la compra.
- **Identidad Visual:** Crear una marca atractiva ligada al sector del ocio nocturno y los eventos culturales (conciertos, festivales).
- **Prototipo de Alta Fidelidad (High-Fidelity):** Validar componentes complejos como el modal de *Checkout* simulado y el escáner de códigos QR antes de escribirlos en código.

---

## 2. Paleta de Colores

Se ha optado por un diseño en **Modo Oscuro Nativo** para evocar una sensación "premium" y de evento nocturno/teatral. El contraste se logra mediante colores vibrantes sobre fondos profundos.

| Rol | Color HEX | Uso principal |
|-----|-----------|---------------|
| **Fondo Principal** | `#000000` | Background absoluto de la aplicación. |
| **Fondo Secundario** | `#0d0d12` | Fondos de tarjetas, modales y secciones elevadas. |
| **Acento (Primary)** | `#26b1c4` | *Cyan Teatral*. Botones de llamada a la acción, enlaces, bordes iluminados y títulos principales. |
| **Texto Principal** | `#ffffff` | Títulos y datos relevantes. |
| **Texto Secundario** | `#e2e8f0` | Párrafos y descripciones largas. |
| **Texto Muted** | `#94a3b8` | Textos de apoyo, placeholders y fechas. |
| **Éxito (Success)** | `#10b981` | Notificaciones de validación correcta de QR (pantalla verde en puerta) y confirmaciones de pago. |
| **Error (Danger)** | `#ef4444` | Alertas de aforo agotado, QR inválido o ya utilizado. |

---

## 3. Tipografía

Se ha empleado una única familia tipográfica de la colección de Google Fonts para mantener la coherencia y mejorar los tiempos de carga, garantizando la legibilidad en pantallas móviles y de escritorio.

- **Familia principal:** `Inter`, sans-serif.
- **Pesos (Font-Weights):** 
  - `300` (Light): Subtítulos y metadatos.
  - `400` (Regular): Cuerpo de texto.
  - `600` (SemiBold): Botones y títulos de secciones.
  - `700` (Bold): Encabezados principales (`h1`, `h2`).

---

## 4. Sistema de Componentes y Estilos CSS Nativo

Para optimizar el rendimiento y evitar dependencias pesadas, la interfaz se construyó mediante **CSS Nativo con Variables Globales**, definiendo los siguientes patrones visuales:

### 4.1. Glassmorphism (Efecto Cristal)
Se implementa en cabeceras de navegación (`nav`) y ventanas superpuestas (modales).
- **CSS:** `background: rgba(13, 13, 18, 0.7); backdrop-filter: blur(12px);`
- **Justificación:** Otorga profundidad a la aplicación sin romper la lectura del texto inferior.

### 4.2. Bordes y Radios (Border-Radius)
Se huye de los diseños "caja" agresivos.
- **Tarjetas de eventos:** `border-radius: 28px;`
- **Botones y Badges:** `border-radius: 20px;` u `8px` dependiendo de su impacto.
- **Contornos (Outlines):** En estado `focus` y `hover`, los elementos interactivos se envuelven en un borde `rgba(38, 177, 196, 0.4)` proporcionando accesibilidad visual.

### 4.3. Modales en lugar de redirecciones
Para procesos críticos (como el Checkout o la Baja Lógica de eventos), se diseñaron ventanas superpuestas (*overlays*). Esto impide que el usuario pierda su contexto espacial, manteniendo el control visual de lo que estaba haciendo antes de la interrupción.

---

## 5. Diseño Responsivo (Mobile-First)

El maquetado de todos los componentes siguió la filosofía *Mobile-First*, dada la naturaleza del sector (los usuarios finales compran y enseñan el QR mayoritariamente desde su smartphone).

- **Grid System:** Uso extensivo de `display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));` para adaptar la parrilla de eventos a cualquier tamaño de pantalla sin necesidad de Media Queries excesivas.
- **Navegación Móvil:** Adaptación del Header a menús tipo "hamburguesa" e integración táctil intuitiva.
- **Escáner de Portería:** Interfaz diseñada a pantalla completa, eliminando distracciones y dejando la respuesta de validación limitada a un color de fondo plano (Verde/Rojo) de gran visibilidad para entornos poco iluminados.

---
*Documento Anexo - Guía de Estilos UI/UX - TFG EntryPass*
