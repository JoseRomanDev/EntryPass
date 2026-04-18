# ==========================================
# ENTRYPASS - Comandos Rápidos de Docker
# ==========================================

# Regla por defecto al escribir simplemente 'make'
.PHONY: help
help:
	@echo "Comandos disponibles:"
	@echo "  make up      - Levanta todos los contenedores en segundo plano"
	@echo "  make down    - Detiene y elimina los contenedores"
	@echo "  make start   - Activa/reanuda los contenedores detenidos"
	@echo "  make stop    - Pausa/detiene los contenedores sin eliminarlos"
	@echo "  make build   - Reconstruye las imágenes de Docker"
	@echo "  make php     - Accede a la terminal del Backend (Symfony)"
	@echo "  make node    - Accede a la terminal del Frontend (Angular)"
	@echo "  make psql    - Accede a la consola SQL de PostgreSQL"
	@echo "  make logs    - Muestra los logs de todos los contenedores"
	@echo "  make seed    - Inserta el usuario administrador inicial"

# --- Opciones de Infraestructura ---
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

# --- Opciones de Acceso a Contenedores ---
.PHONY: php node psql

php:
	docker exec -it php sh

node:
	docker exec -it node sh

psql:
	docker exec -it postgres psql -U postgres_user -d entrypass_db

seed:
	docker compose exec php php bin/console app:seed-admin
