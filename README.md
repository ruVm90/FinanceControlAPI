# 💰 FinanceControlAPI

API RESTful desarrollada con **Laravel** para la gestión y control de gastos personales.  
Permite registrar gastos, clasificarlos por categorías, y obtener reportes o resúmenes financieros.

---

## Características principales

- Registro y autenticación de usuarios (JWT o Sanctum).  
- Creación y gestión de **categorías** de gastos.  
- CRUD completo de **gastos**.  
- Filtros por fechas y categoría.  
- Posibilidad de generar estadísticas o reportes.  
- API documentada con Postman o Swagger.

---

## Tecnologías utilizadas

- **PHP** >= 8.x  
- **Laravel** 12  
- **MySQL** o **SQLite**  
- **Composer**  
- **Laravel Sanctum** para autenticación  
- **Postman** para pruebas de endpoints  

---

## Instalación

### 1. Clona el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/FinanceControlAPI.git
   cd FinanceControlAPI
   ```
### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar base de datos (SQLite)

```bash
touch database/database.sqlite
php artisan migrate
```

### 5. Iniciar servidor de desarrollo

```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000`

## Uso

### Autenticación

Todos los endpoints excepto `/register` y `/login` requieren autenticación mediante token Bearer.

#### Registrarse

```bash
POST /api/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Respuesta exitosa (201):**
```json
{
  "message": "user created successfully",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com"
  },
  "token": "your-bearer-token-here"
}
```

#### Login

```bash
POST /api/login
Content-Type: application/json

{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Respuesta exitosa (200):**
```json
{
  "message": "login successful",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com"
  },
  "token": "your-bearer-token-here"
}
```

#### Logout

```bash
POST /api/logout
Authorization: Bearer {token}
```
## Estructura del proyecto

financeControlAPI/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── .env.example
└── README.md

## Endpoints

| Método | Endpoint                 | Descripción                    |
| ------ | ------------------------ | ------------------------------ |
| GET    | `/api/expenses`          | Listar todas las transacciones |
| POST   | `/api/expense`           | Crear una nueva transacción    |
| GET    | `/api/expense/{id}`      | Ver detalle de una transacción |
| PUT    | `/api/expense/{id}`      | Actualizar una transacción     |
| DELETE | `/api/expense/{id}`      | Eliminar una transacción       |


