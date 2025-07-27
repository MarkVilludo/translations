# Translation Management Service

This project is an API built using Laravel for managing application translations. It provides a robust and scalable API for creating, updating, searching, and exporting translations, designed to support multiple locales and a variety of front-end applications.

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Local Setup](#local-setup)
- [Technical Decisions and Design Choices](#technical-decisions-and-design-choices)
- [API Endpoints](#api-endpoints)
- [Usage](#usage)
- [Testing](#testing)
- [Performance](#performance)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Multi-Locale Support**: Store translations for multiple locales (e.g., `en`, `fr`, `es`), with the ability to easily add new languages.
- **Tagging System**: Tag translations for context (e.g., mobile, desktop, web) to allow for better organization and targeted retrieval.
- **Comprehensive API**: A set of RESTful endpoints to create, update, view, and search translations by tags, keys, or content.
- **Efficient JSON Export**: A dedicated endpoint to export translations in a JSON format optimized for consumption by front-end frameworks like Vue.js.
- **Scalability**: Architecture is designed to handle 100k+ translation records efficiently.
- **Secure API**: All endpoints are secured using Sanctum token-based authentication.
- **Performance**: All endpoints optimized for quick response times under heavy load.

## Prerequisites

- PHP 8.2
- Composer
- Database (MySQL or MariaDB)
- Docker and Docker Compose

## Local Setup

### Option 1: Docker (recommended)

1. Ensure Docker is installed on your machine.
2. Clone the repository:
   ```bash
   git clone https://{user}@bitbucket.org/org/project-name.git
   cd project-name.git
   ```
3. Copy `docker\local\.env.local` to `.env` and configure it as needed.
4. Add to your local `hosts` file:
   ```text
   127.0.0.1 translation-api.local.com
   ```
5. Generate a local certificate using mkcert:
   ```bash
   cd docker/local/nginx/mkcert
   mkcert translation-api.local.com
   ```
6. Build and run the containers:
   ```bash
   docker compose -f docker-compose.local.yml up --build -d
   ```
7. Generate a new app key:
   ```bash
   docker exec translation-api php artisan key:generate
   ```
8. Run migrations and seed database: 
   ```bash
   docker exec translation-api php artisan migrate:fresh --seed
   ```

9.  Check the users table and use for login (if needed), the password is `password` only for now.
10. Import Postman collection from root folder to test the API.

## Technical Decisions and Design Choices

- **Laravel Actions**: Business logic is encapsulated in action classes for maintainability and testability.
- **Scalable Database Schema**: Unique composite index on `key` and `locale` for fast lookups and data integrity.
- **Eloquent Optimization**: Efficient querying with eager loading to avoid N+1 issues.
- **Export Optimization**: Optimized joins and minimal memory usage for large dataset exports.
- **Secure Auth**: Laravel Sanctum provides stateless, secure token-based auth.
- **Coding Standards**: Code adheres to PSR-12 for consistency.

## API Endpoints

### Authentication

- `POST /login`: Authenticate user and return Sanctum token.

### Translations (requires authentication)

All routes are prefixed with `/api/translations`:

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `/`      | Create a new translation. |
| PUT    | `/key/{key}` | Update an existing translation by key. |
| GET    | `/key/{key}` | Get a specific translation by key. |
| GET    | `/`      | Search translations. Supports filters like `key`, `tag`, and `content`. |

**Example Request Body** (POST `/api/translations`):
```json
{
  "key": "greeting.hello",
  "translations": {
    "en": "Hello",
    "fr": "Bonjour",
    "es": "Hola"
  },
  "tags": [1, 2]
}
```

**Example Request Body** (PUT `/api/translations/key/:key`):
```json
{
  "translations": {
    "en": "Hi there",
    "fr": "Salut"
  },
  "tags": [1,2]
}
```

**Example Request Body** (GET `/api/translations/key/:key`):
```json
{
    "key": "greeting.hello",
    "translations": {
        "en": "Hello",
        "fr": "Bonjour",
        "es": "Hola"
    },
    "tags": [
        1,
        2
    ]
}
```

**Example Request Body** (GET `/api/translations?key=greeting.hello&tags=web&content=`):
```json
{
    "data": [
        {
            "id": 1,
            "locale": "en",
            "key": "greeting.hello",
            "tags": [
                "web",
                "mobile"
            ],
            "content": "Hi there"
        },
        {
            "id": 2,
            "locale": "fr",
            "key": "greeting.hello",
            "tags": [
                "web",
                "mobile"
            ],
            "content": "Salut"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 2
    }
}
```

### Translation Export API

- **Endpoint**: `GET /api/translations/exports?locale=en`
- **Query Param**: `locale`
- **Response**:
```json
{
  "greeting.hello": "Hello",
  "home.title": "Home",
  "login.button": "Sign In"
}
```

**Design Notes**:

- Action-driven implementation for separation of concerns.
- Optimized queries and output formatting.

## Usage

### Getting a Sanctum Token

```bash
curl -X POST https://translation-api.local.com/api/login   -H 'Content-Type: application/json'   -d '{
    "email": "user@example.com",
    "password": "password"
}'
```

Use the returned token in the Authorization header and Accept Application Json:
```
Authorization: Bearer <token>
Accept application/json
```

## Testing

Run the full test suite:

```bash
docker exec translation-api php artisan test
```

Run performance tests only:

```bash
docker exec translation-api php artisan test --filter PerformanceTest::test_json_export_endpoint_response_time_is_under_500ms
```

## Performance

- General Endpoints: < 200ms response time.

## Contributing

Pull requests welcome! Open an issue to discuss major changes.

## License

This project is open-sourced under the MIT License.