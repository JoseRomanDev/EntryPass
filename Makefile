# ==========================================
# ENTRYPASS - Comandos Rápidos de Docker
# ==========================================

# Regla por defecto al escribir simplemente 'make'
.PHONY: help
help:
	@echo "Comandos disponibles:"
	@echo ""
	@echo "  === DESARROLLO (ng serve + hot-reload) ==="
	@echo "  make up      - Levanta todos los contenedores en segundo plano"
	@echo "  make down    - Detiene y elimina los contenedores"
	@echo "  make start   - Activa/reanuda los contenedores detenidos"
	@echo "  make stop    - Pausa/detiene los contenedores sin eliminarlos"
	@echo "  make build   - Reconstruye las imágenes de Docker"
	@echo "  make logs    - Muestra los logs de todos los contenedores"
	@echo ""
	@echo "  === PRODUCCION (ng build + Nginx estaticos) ==="
	@echo "  make prod-up    - Levanta el entorno de produccion"
	@echo "  make prod-down  - Detiene el entorno de produccion"
	@echo "  make prod-build - Reconstruye las imagenes de produccion"
	@echo "  make prod-logs  - Muestra los logs de produccion"
	@echo ""
	@echo "  === ACCESO A CONTENEDORES ==="
	@echo "  make php     - Accede a la terminal del Backend (Symfony)"
	@echo "  make node    - Accede a la terminal del Frontend (Angular)"
	@echo "  make psql    - Accede a la consola SQL de PostgreSQL"
	@echo "  make seed    - Inserta el usuario administrador inicial"
	@echo "  make seed-demo - Inserta datos de demostracion"

# --- Desarrollo (compose.yml) ---
.PHONY: up down start stop build logs

up:
	docker compose up -d

down:
	docker compose down

start:
	docker compose start

stop:
	docker compose stop

build:
	docker compose build

logs:
	docker compose logs -f

# --- Produccion (compose.prod.yml) ---
.PHONY: prod-up prod-down prod-build prod-logs

prod-up:
	docker compose -f compose.prod.yml up -d --build

prod-down:
	docker compose -f compose.prod.yml down

prod-build:
	docker compose -f compose.prod.yml build

prod-logs:
	docker compose -f compose.prod.yml logs -f

# --- Opciones de Acceso a Contenedores ---
.PHONY: php node psql seed seed-demo

php:
	docker exec -it php sh

node:
	docker exec -it node sh

psql:
	docker exec -it postgres psql -U postgres_user -d entrypass_db

seed:
	docker compose exec php php bin/console app:seed-admin

seed-demo:
	docker compose exec php php bin/console app:seed-demo
