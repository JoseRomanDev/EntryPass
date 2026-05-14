---
marp: true
theme: gaia
_class: lead
backgroundColor: #000
color: #fff
style: |
  section {
    font-family: 'Inter', sans-serif;
    background: radial-gradient(circle at 0% 0%, #0d0d12 0%, #000000 100%);
    font-size: 26px; /* Base font size reduced slightly to fit content */
    padding: 60px 80px; /* Better padding */
  }
  h1 { color: #26b1c4; font-size: 2.2em; margin-bottom: 0.2em; }
  h2 { 
    color: #26b1c4; 
    border-bottom: 2px solid rgba(38, 177, 196, 0.3); 
    padding-bottom: 10px; 
    font-size: 1.5em; 
    margin-bottom: 0.8em; 
  }
  h3 { color: #fff; font-size: 1.1em; margin-top: 1em; margin-bottom: 0.3em; }
  p, li { font-size: 0.95em; line-height: 1.4; color: #e2e8f0; }
  li { margin-bottom: 0.3em; }
  footer { color: #6b6b7b; font-size: 0.5em; }
  
  .badge {
    display: inline-block;
    background: rgba(38, 177, 196, 0.15);
    color: #26b1c4;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.65em;
    font-weight: 600;
    border: 1px solid rgba(38, 177, 196, 0.4);
    margin: 15px 0;
  }
  .highlight { color: #26b1c4; font-weight: 600; }
  
  img[alt="logo"] { width: 140px; border-radius: 16px; margin-bottom: 10px; }
  
  /* Tables */
  table { width: 100%; font-size: 0.85em; margin-top: 1em; border-collapse: collapse; }
  th { background: rgba(38, 177, 196, 0.1); color: #26b1c4; border-bottom: 2px solid #26b1c4; padding: 12px; }
  td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); }
  
  /* Cover Slide Adjustments */
  section.lead { text-align: center; padding-top: 40px; }
  section.lead h1 { margin-bottom: 0; font-size: 2.8em; }
  section.lead h3 { margin-top: 0; font-weight: 300; color: #94a3b8; font-size: 1.2em; }
  section.lead p { font-size: 0.85em; margin: 5px 0; color: #cbd5e1; }
  section.lead strong { color: #fff; font-size: 1.1em; }
---

![logo](frontend/public/logo.png)
# EntryPass
### Gestión y Compra de Entradas para Eventos

<div class="badge">Trabajo de Fin de Grado — 2º DAW</div>

**José Manuel Román Navarro**
Curso Académico: 2025-2026
IES PORTADA ALTA

---

# Índice

1. **Introducción y Objetivos**
2. **Comparativa de Soluciones**
3. **Stack Tecnológico**
4. **Arquitectura Hexagonal y Asincronía**
5. **Casos de Uso Principales**
6. **Líneas Futuras y Conclusiones**
7. **Bibliografía**
8. **Demostración en Vivo**

---

# 1. Introducción

- **Problema**: Dificultad para pequeños organizadores de gestionar ventas y accesos de forma autónoma.
- **Solución**: EntryPass. Una plataforma <span class="highlight">integral, escalable y sin comisiones abusivas</span>.
- **Enfoque**: Experiencia de usuario (UX) fluida y robustez técnica empresarial.

---

# 2. Objetivos

- **Plataforma Unificada**: Cliente y Organizador en un solo lugar.
- **Frontend SPA**: Navegación instantánea con <span class="highlight">Angular 21 + Signals</span>.
- **Backend Robusto**: API REST con <span class="highlight">Symfony 7 + PHP 8.4</span>.
- **Procesamiento Asíncrono**: Desacoplo de tareas pesadas (QR/PDF) mediante <span class="highlight">RabbitMQ</span>.
- **Seguridad**: Validación de tickets infalible mediante hashes UUID.

---

# 3. Comparativa de Soluciones

| Característica | EntryPass | Eventbrite | Entradium |
| :--- | :---: | :---: | :---: |
| **Comisiones** | <span class="highlight">0%</span> | 3.5% - 6.5% | Media |
| **Control de Datos** | **Total** | Nulo | Nulo |
| **Asincronía** | **Sí (RabbitMQ)** | Sí | Limitada |
| **Arquitectura** | **Hexagonal** | Desconocida | Monolítica |

---

# 4. Tecnologías Empleadas

### Frontend
- **Angular 21**: Arquitectura Zoneless y Signals.
- **CSS Nativo**: Estilos modulares con variables CSS para máximo rendimiento.

### Backend
- **Symfony 7**: Messenger, Mailer y Security JWT.
- **PostgreSQL 16**: Integridad referencial y tipos UUID.

### Infraestructura
- **Docker / Compose**: Portabilidad total.
- **RabbitMQ**: Gestión de colas de mensajes.
- **Nginx**: Servidor de producción optimizado (Gzip).

### Calidad y Diseño
- **PHPUnit**: Tests Unitarios y Funcionales (validación de pasarela).
- **Figma**: Prototipado y definición de guía de estilos.

---

# 5. Arquitectura del Sistema

### Arquitectura Hexagonal (Ports & Adapters)
- **Dominio**: Reglas de negocio puras (independiente).
- **Aplicación**: Casos de uso (Checkout, Registro).
- **Infraestructura**: Adaptadores (Doctrine, StripeMock, Mailer).

### Procesamiento Asíncrono
1. El usuario paga.
2. El backend emite un mensaje a la cola.
3. El **Worker** genera el QR y envía el email en segundo plano.

---

# 6. Casos de Uso: Compra y Validación

### Flujo de Compra
- Checkout con **Stripe Mock UI** (latencia simulada para confianza).
- Control estricto de aforo (Race conditions controladas en BD).

### Validación en Puerta
- Escáner QR móvil integrado.
- Respuesta visual inmediata: **Válido (Verde)** vs **Error (Rojo)**.
- Prevención de fraude (Double-spend) en milisegundos.

---

# 7. Líneas Futuras

- **Pasarelas Reales**: Integración con Stripe/PayPal real (solo un adaptador nuevo).
- **WebSockets**: Notificaciones de aforo en tiempo real.
- **Dashboard Analítico**: Gráficas de ventas e ingresos para organizadores.
- **App Móvil**: PWA / Capacitor para instalación nativa.
- **CI/CD**: Despliegue continuo en AWS/DigitalOcean.

---

# 8. Conclusiones

- El proyecto demuestra que es posible crear una herramienta <span class="highlight">profesional y escalable</span> con un presupuesto de infraestructura mínimo.
- La **Arquitectura Hexagonal** ha permitido un desarrollo limpio y preparado para el cambio.
- Se han cumplido el 100% de los objetivos técnicos y funcionales planteados.

---

# 9. Bibliografía

- **Angular Documentation**: signals & zoneless architecture (angular.dev).
- **Symfony Framework**: Messenger and Hexagonal patterns (symfony.com).
- **RabbitMQ in Practice**: Distributed systems and message brokering.
- **Clean Architecture** (Robert C. Martin): Principles of dependency inversion.
- **MDN Web Docs**: CSS Grid, Flexbox and Accessibility standards.

---

# 10. Demostración en Vivo

1. **Exploración**: Home y carrusel dinámico.
2. **Auth**: Flujo de registro y login.
3. **Proceso de Compra**: Selección de entradas y Checkout.
4. **Perfil**: Visualización de tickets y QRs.
5. **Panel Admin**: Gestión de eventos y validación QR.

---

# Preguntas

## ¡Muchas gracias por su atención!

*"Y con esto doy por concluida la presentación de mi proyecto y quedo a disposición del tribunal para las preguntas que consideren necesarias."*

---
