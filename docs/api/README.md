# Edison Tech API Documentation

## Overview

This directory contains comprehensive API documentation for the Edison Tech Project Management System.

## Documentation Format

The API is documented using the **OpenAPI 3.0 Specification** (formerly Swagger).

## Files

- `openapi.yaml` - Complete OpenAPI 3.0 specification
- `README.md` - This file

## Viewing the Documentation

### Option 1: Swagger UI (Recommended)

1. Install Swagger UI or use the online editor:
   ```bash
   npx @stoplight/spectral-cli lint openapi.yaml
   ```

2. Visit: https://editor.swagger.io/
3. Import the `openapi.yaml` file

### Option 2: Redoc

Generate beautiful static documentation:

```bash
npx @redocly/cli build-docs openapi.yaml -o api-docs.html
```

### Option 3: Local Development

Install L5-Swagger package:

```bash
composer require darkaonline/l5-swagger
php artisan l5-swagger:generate
```

Then visit: `http://localhost/api/documentation`

## API Versioning

Current API version: **v1.0.0**

All endpoints are prefixed with `/api`:
```
http://localhost/api/health
http://localhost/api/users
http://localhost/api/projects
```

## Authentication

### Bearer Token Authentication

All API requests (except health checks and login) require authentication using Bearer tokens.

**Example request:**
```bash
curl -X GET "http://localhost/api/users" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

### Obtaining a Token

**Login endpoint:**
```bash
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "admin"
  }
}
```

## Rate Limiting

| Endpoint Type | Rate Limit |
|--------------|------------|
| API endpoints | 60 requests/minute |
| Authentication | 5 requests/minute |
| Password reset | 3 requests/minute |
| Uploads | 10 requests/minute |
| 2FA | 5 requests/minute |
| Newsletter | 5 requests/minute |

**Rate limit headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1642348800
```

## Error Responses

All errors follow a consistent format:

**Validation Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

**Unauthorized (401):**
```json
{
  "message": "Unauthenticated."
}
```

**Forbidden (403):**
```json
{
  "message": "This action is unauthorized."
}
```

**Not Found (404):**
```json
{
  "message": "Resource not found."
}
```

**Server Error (500):**
```json
{
  "message": "Server Error"
}
```

## Common HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request succeeded |
| 201 | Created - Resource created successfully |
| 204 | No Content - Request succeeded, no content returned |
| 400 | Bad Request - Invalid request format |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource doesn't exist |
| 422 | Unprocessable Entity - Validation failed |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |
| 503 | Service Unavailable - Service temporarily down |

## Pagination

List endpoints return paginated results:

**Request:**
```bash
GET /api/users?page=2&per_page=15
```

**Response:**
```json
{
  "data": [
    { "id": 1, "name": "User 1" },
    { "id": 2, "name": "User 2" }
  ],
  "links": {
    "first": "http://localhost/api/users?page=1",
    "last": "http://localhost/api/users?page=5",
    "prev": "http://localhost/api/users?page=1",
    "next": "http://localhost/api/users?page=3"
  },
  "meta": {
    "current_page": 2,
    "from": 16,
    "last_page": 5,
    "per_page": 15,
    "to": 30,
    "total": 73
  }
}
```

## Filtering

Most list endpoints support filtering via query parameters:

**Projects:**
```bash
GET /api/projects?status=active&company_id=5&search=website
```

**Tasks:**
```bash
GET /api/tasks?status=in_progress&priority=high&assignee_id=10
```

**Invoices:**
```bash
GET /api/invoices?status=paid&from_date=2025-01-01&to_date=2025-12-31
```

## Sorting

Use the `sort` parameter:

```bash
GET /api/projects?sort=-created_at  # Descending
GET /api/tasks?sort=due_date        # Ascending
```

## Including Related Data

Use the `include` parameter to load relationships:

```bash
GET /api/projects/1?include=company,tasks,invoices
GET /api/invoices/5?include=items,payments,company
```

## Example Requests

### Create Project

```bash
curl -X POST "http://localhost/api/projects" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 5,
    "name": "Website Redesign",
    "description": "Complete redesign of company website",
    "status": "planning",
    "priority": "high",
    "budget": 50000.00,
    "start_date": "2025-02-01",
    "end_date": "2025-05-01"
  }'
```

### Update Task Status

```bash
curl -X PATCH "http://localhost/api/tasks/123/status" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed"
  }'
```

### Process Payment

```bash
curl -X POST "http://localhost/api/payments" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_id": 456,
    "amount": 1000.00,
    "payment_method": "credit_card",
    "payment_date": "2025-01-15",
    "reference_number": "TXN-123456"
  }'
```

## Testing the API

### Using cURL

```bash
# Health check (no auth required)
curl http://localhost/health

# Login
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Get users (with auth)
curl http://localhost/api/users \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Using Postman

1. Import the `openapi.yaml` file into Postman
2. Create an environment with:
   - `base_url`: http://localhost/api
   - `token`: (obtained from login)
3. Use `{{base_url}}` and `{{token}}` in requests

### Using HTTPie

```bash
# Health check
http GET localhost/health

# Login
http POST localhost/api/login email=admin@example.com password=password

# Get users
http GET localhost/api/users Authorization:"Bearer YOUR_TOKEN"
```

## Webhook Events

The system can send webhook notifications for the following events:

- `invoice.created`
- `invoice.sent`
- `invoice.paid`
- `payment.completed`
- `payment.failed`
- `project.created`
- `project.completed`
- `task.completed`

Configure webhooks in the admin panel or via API.

## Best Practices

1. **Always use HTTPS in production**
2. **Store tokens securely** (never in source code)
3. **Implement token refresh** for long-running applications
4. **Handle rate limits** with exponential backoff
5. **Validate all input** on the client side
6. **Cache GET requests** when appropriate
7. **Use ETags** for conditional requests
8. **Implement request timeouts**
9. **Log all API errors**
10. **Monitor API usage**

## Support

For API support, contact:
- Email: support@edisontech.example.com
- Documentation: https://docs.edisontech.example.com
- Status page: https://status.edisontech.example.com

## Changelog

### Version 1.0.0 (2025-01-15)
- Initial API release
- Complete CRUD operations for all resources
- Health check endpoints
- Payment processing
- Document management
- Authentication and authorization
