# Hummingbed Property API

Hummingbed is a REST API for a property marketplace. Customers can discover and save listings, contact brokers, and request viewings. Brokers can manage their profiles, listings, images, amenities, enquiries, and appointments. Administrators can manage shared marketplace data.

The API is built with Laravel 10, uses Laravel Sanctum bearer tokens, stores data in SQLite, and includes an interactive OpenAPI/Swagger interface.

## Features

- Versioned REST endpoints under `/api/v1`
- Customer, broker, and administrator roles
- Token authentication with Laravel Sanctum
- Broker-owned property management
- Searchable and paginated property listings
- Prices, property characteristics, publication metadata, and featured listings
- Ordered property images with a primary-image flag
- Reusable amenities and amenity-based filtering
- Saved properties and favourites
- Public property enquiries with broker status management
- Viewing requests with customer and broker workflows
- Consistent JSON errors for authentication, authorization, validation, and missing resources
- Complete Swagger documentation
- SQLite foreign-key constraints and transactional property writes

## Technology

- PHP 8.1+
- Laravel 10
- Laravel Sanctum
- SQLite through `pdo_sqlite`
- L5 Swagger and OpenAPI 3
- PHPUnit 10

## Getting started

### Requirements

- PHP 8.1 or later
- Composer
- PHP extensions required by Laravel, including `pdo_sqlite`

### Installation

```bash
git clone <repository-url>
cd hummingbed-property-api-service
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The API will be available at `http://127.0.0.1:8000/api/v1` and Swagger UI at `http://127.0.0.1:8000/api/documentation`.

Verify that the deployed API router is reachable with `GET /api/v1/health`. This endpoint does not query the database.

SQLite is the only configured database. Local data is stored in `database/database.sqlite`, while automated tests use an isolated in-memory SQLite database.

### Vercel deployment

Vercel sends all requests through `api/index.php`. The entry point normalizes PHP's script path so Laravel receives `/api/*` URLs unchanged. After deploying, verify routing at:

```text
https://your-domain.vercel.app/api/v1/health
```

Vercel's serverless filesystem is read-only except for `/tmp`, so the deployment uses `/tmp/database.sqlite` and initializes its schema when a function instance starts. This makes the API runnable for previews and demonstrations, but `/tmp` is ephemeral and is not shared between function instances. Data can disappear after a cold start or deployment. A Vercel deployment therefore cannot provide durable SQLite storage; production persistence requires hosting the application on a service with a persistent writable volume.

## Authentication

Register a customer or broker account:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/register \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "name": "Ada Okafor",
    "email": "ada@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "broker"
  }'
```

Registration and login return a Sanctum token. Send it with protected requests:

```text
Authorization: Bearer <token>
Accept: application/json
```

In Swagger UI, select **Authorize** and enter the token. The `Bearer` prefix is handled by Swagger's authorization scheme.

### Roles

| Role | Capabilities |
| --- | --- |
| Customer | Save properties, send enquiries, and manage viewing requests |
| Broker | Create a broker profile, manage owned listings, and handle received enquiries and viewings |
| Administrator | Manage any owned resource and create shared amenities |

## API reference

Endpoints marked **Auth** require a Sanctum bearer token.

### Authentication

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `POST` | `/api/v1/auth/register` | Public | Register a customer or broker |
| `POST` | `/api/v1/auth/login` | Public | Log in and receive a token |
| `GET` | `/api/v1/auth/me` | Auth | Retrieve the current account and broker profile |
| `POST` | `/api/v1/auth/logout` | Auth | Revoke the current token |

### Properties

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `GET` | `/api/v1/properties` | Public | Search and paginate properties |
| `POST` | `/api/v1/properties` | Broker | Create a property with its characteristics |
| `GET` | `/api/v1/properties/{id}` | Public | Retrieve a property |
| `PUT/PATCH` | `/api/v1/properties/{id}` | Owner/Admin | Update a property or its characteristics |
| `DELETE` | `/api/v1/properties/{id}` | Owner/Admin | Delete a property and dependent records |

Property searches support the following query parameters:

| Parameter | Description |
| --- | --- |
| `city` | Partial city match |
| `listing_type` | Listing agreement type |
| `property_type` | Building type, such as `duplex` |
| `status` | Availability, such as `on sale` |
| `min_price` / `max_price` | Inclusive price range in naira |
| `amenity` | Amenity slug, such as `24-hour-power` |
| `featured` | Boolean featured flag |
| `per_page` | Page size from 1 to 100; defaults to 10 |

Prices are represented as integer naira amounts to preserve numeric sorting and calculations.

### Brokers

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `GET` | `/api/v1/brokers` | Public | List brokers |
| `POST` | `/api/v1/brokers` | Broker/Admin | Create one broker profile for the current account |
| `GET` | `/api/v1/brokers/{id}` | Public | Retrieve a broker |
| `PUT/PATCH` | `/api/v1/brokers/{id}` | Owner/Admin | Update a broker profile |
| `DELETE` | `/api/v1/brokers/{id}` | Owner/Admin | Delete a broker and its properties |

### Images and amenities

Image endpoints currently accept hosted URLs; they do not upload binary files.

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `GET` | `/api/v1/amenities` | Public | List available amenities |
| `POST` | `/api/v1/amenities` | Admin | Create an amenity |
| `PUT` | `/api/v1/properties/{property}/amenities` | Owner/Admin | Replace a property's amenity selection |
| `POST` | `/api/v1/properties/{property}/images` | Owner/Admin | Add an ordered property image |
| `DELETE` | `/api/v1/properties/{property}/images/{image}` | Owner/Admin | Delete a property image |

### Favourites

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `GET` | `/api/v1/favorites` | Auth | List the current user's saved properties |
| `POST` | `/api/v1/properties/{property}/favorite` | Auth | Save a property; repeated calls are safe |
| `DELETE` | `/api/v1/properties/{property}/favorite` | Auth | Remove a saved property |

### Enquiries

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `POST` | `/api/v1/properties/{property}/inquiries` | Public | Submit an enquiry about a property |
| `GET` | `/api/v1/inquiries` | Broker/Admin | List enquiries received for owned properties |
| `PATCH` | `/api/v1/inquiries/{inquiry}` | Owner/Admin | Set status to `new`, `contacted`, or `closed` |

### Viewing appointments

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `GET` | `/api/v1/appointments` | Auth | List the current customer's viewing requests |
| `POST` | `/api/v1/properties/{property}/appointments` | Auth | Request a future viewing time |
| `PATCH` | `/api/v1/appointments/{appointment}/cancel` | Requester | Cancel a viewing request |
| `GET` | `/api/v1/broker/appointments` | Broker/Admin | List viewing requests for owned properties |
| `PATCH` | `/api/v1/broker/appointments/{appointment}` | Owner/Admin | Confirm, complete, or cancel a viewing |

## Response format

A typical successful response is:

```json
{
  "status": "success",
  "message": "Property retrieved successfully",
  "data": {}
}
```

API errors always return JSON, including when the caller omits an `Accept` header:

```json
{
  "status": "failed",
  "message": "Unauthenticated.",
  "data": null
}
```

Validation errors use HTTP `422` and provide errors keyed by field:

```json
{
  "status": "failed",
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

Common status codes are `200`, `201`, `401`, `403`, `404`, `405`, `422`, `429`, and `500`.

## Swagger and OpenAPI

Start the application and open:

```text
http://127.0.0.1:8000/api/documentation
```

Regenerate the checked-in OpenAPI document after changing routes or schemas:

```bash
php artisan l5-swagger:generate
```

The generated JSON is stored at `storage/api-docs/api-docs.json`. A regression test verifies that every registered `/api/v1` operation appears in Swagger.

## Development commands

```bash
# Run the test suite
php artisan test

# Check code formatting
vendor/bin/pint --test

# Apply code formatting
vendor/bin/pint

# Rebuild the local SQLite database and seed amenities
php artisan migrate:fresh --seed

# Show registered API routes
php artisan route:list --path=api/v1
```

`migrate:fresh` deletes all data in the local SQLite database. Use it only when resetting the development environment is intentional.

## Project structure

```text
app/
├── Http/Controllers/   HTTP endpoints and marketplace workflows
├── Http/Requests/      Request validation
├── Http/Resources/     Public response transformations
├── Models/             Eloquent entities and relationships
├── OpenApi/            Swagger operations and reusable schemas
├── Repositories/       Database query abstraction
└── Services/           Property and broker business operations

database/
├── factories/          Test-data factories
├── migrations/         SQLite schema history
└── seeders/            Default amenity data

tests/Feature/          API, authorization, error, and Swagger coverage
```

## Testing

```bash
php artisan test
```

Tests run against an in-memory SQLite database and cover authentication, ownership, property CRUD, transactions, filters, pagination, images, amenities, favourites, enquiries, viewing appointments, JSON errors, deletion cascades, and Swagger completeness.

## Roadmap

- Draft, moderation, publication, expiry, and archival workflows
- Direct image uploads backed by object storage
- Email and in-app notifications
- Queued image processing and notification delivery
- Audit logs, observability, and continuous integration

## License

This project is available under the MIT License.
