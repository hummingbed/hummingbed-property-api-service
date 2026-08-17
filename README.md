# Hummingbed Property API

A Laravel API for managing property brokers and searchable real-estate listings. A property belongs to a broker and has one set of characteristics containing its price, size, type, and availability status.

## Requirements

- PHP 8.1 or later
- Composer
- PHP SQLite extension (`pdo_sqlite`)

## Local setup

```bash
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

SQLite is the default and only configured database. The application uses `database/database.sqlite`; no database server or credentials are required. Interactive Swagger UI is available at `/api/documentation` while the application is running.

Regenerate the OpenAPI document after changing endpoints:

```bash
php artisan l5-swagger:generate
```

API errors are always returned as JSON, including requests sent from Swagger UI:

```json
{
  "status": "failed",
  "message": "Unauthenticated.",
  "data": null
}
```

Validation failures use HTTP `422` and include an `errors` object keyed by input field. In Swagger UI, use the **Authorize** button and enter the token returned by the login or registration endpoint.

## API

The preferred endpoints use the `/api/v1` prefix. Older endpoints remain available temporarily for backwards compatibility.

Bearer-token authentication is provided by Laravel Sanctum. Register through `POST /api/v1/auth/register`, log in through `POST /api/v1/auth/login`, and send the returned token as `Authorization: Bearer <token>`. Public listing, broker, amenity, and enquiry endpoints do not require a token.

### Properties

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/v1/properties` | List and filter properties |
| `POST` | `/api/v1/properties` | Create a property and its characteristics |
| `GET` | `/api/v1/properties/{id}` | Retrieve a property |
| `PATCH` | `/api/v1/properties/{id}` | Partially update a property |
| `DELETE` | `/api/v1/properties/{id}` | Delete a property |

The listing endpoint accepts `city`, `listing_type`, `property_type`, `status`, `min_price`, `max_price`, `amenity`, `featured`, and `per_page`. Prices are returned as integer naira amounts so API consumers can sort and calculate with them safely.

### Brokers

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/v1/brokers` | List brokers |
| `POST` | `/api/v1/brokers` | Create a broker |
| `GET` | `/api/v1/brokers/{id}` | Retrieve a broker |
| `PATCH` | `/api/v1/brokers/{id}` | Partially update a broker |
| `DELETE` | `/api/v1/brokers/{id}` | Delete a broker and its properties |

### Marketplace features

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/v1/amenities` | List available amenities |
| `PUT` | `/api/v1/properties/{id}/amenities` | Set listing amenities |
| `POST` | `/api/v1/properties/{id}/images` | Add an ordered listing image |
| `DELETE` | `/api/v1/properties/{id}/images/{image}` | Remove a listing image |
| `GET` | `/api/v1/favorites` | List the current user's saved listings |
| `POST` | `/api/v1/properties/{id}/favorite` | Save a listing |
| `DELETE` | `/api/v1/properties/{id}/favorite` | Remove a saved listing |
| `POST` | `/api/v1/properties/{id}/inquiries` | Send an enquiry |
| `GET` | `/api/v1/inquiries` | List enquiries received by a broker |
| `PATCH` | `/api/v1/inquiries/{id}` | Progress an enquiry |
| `POST` | `/api/v1/properties/{id}/appointments` | Request a viewing |
| `GET` | `/api/v1/appointments` | List the customer's viewings |
| `PATCH` | `/api/v1/appointments/{id}/cancel` | Cancel a viewing |
| `GET` | `/api/v1/broker/appointments` | List viewing requests received by a broker |
| `PATCH` | `/api/v1/broker/appointments/{id}` | Confirm or complete a viewing |

## Tests

Tests use an in-memory SQLite database:

```bash
php artisan test
```

The property feature suite covers atomic creation, persisted characteristics, filtering, pagination, updates, deletion cascades, and 404 responses.

## Next milestones

- Draft, review, publication, and archival workflow
- Image uploads backed by object storage (current image endpoints accept hosted URLs)
- API rate limits, queues, monitoring, and CI
