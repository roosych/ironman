# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Important: Avoid Hallucinations

If you don't know the answer or are unsure about something:
- **Say "I don't know"** instead of guessing
- **Ask clarifying questions** before making assumptions
- **Check the codebase first** before suggesting solutions
- **Don't invent APIs, methods, or features** that may not exist
- **Verify Laravel/PHP versions** - this project uses Laravel 12 and PHP 8.2+
- When uncertain about implementation details, read the existing code or official docs

## Project Overview

Laravel 12.0 **REST API backend** for web (React) and mobile applications. PHP 8.2+, Blade views only for email verification pages.

## Tech Stack

- **Backend**: PHP 8.2+, Laravel 12.0
- **Database**: MySQL (Eloquent ORM)
- **API**: RESTful JSON API with Laravel Sanctum
- **Queue**: Database driver (for email notifications)
- **Frontend**: React (separate repo) + Mobile apps
- **Testing**: PHPUnit 11.5

## API Response Format (IMPORTANT!)

All API responses MUST follow this format:

```php
// Success
{ "success": true, "data": { ... } }

// Success with message
{ "success": true, "message": "...", "data": { ... } }

// Error (validation 422, auth 401, etc.)
{ "success": false, "errors": { "field": ["message"] } }
```

Use `App\Traits\ApiResponse` trait in controllers:
```php
return $this->successResponse(['data' => $resource]);
return $this->errorResponse(['field' => ['Error message']], 422);
```

## Architecture

### API Structure (Versioned)
```
app/Http/Controllers/
├── Api/
│   └── V1/                   # API version 1
│       ├── AuthController.php
│       └── [other controllers]
│   └── V2/                   # API version 2 (future)
└── Web/                      # Web controllers (return Blade views)

app/Http/
├── Requests/                 # Form Request validation classes
│   └── Auth/                 # Auth-specific requests
├── Resources/                # API Resources (JSON transformation)
└── Middleware/

app/
├── Models/                   # Eloquent models
├── Services/                 # Business logic services
├── Notifications/            # Email/SMS notifications
└── Exceptions/
```

### Routes (Versioned API)
```
routes/
├── api.php                   # Main API file (loads versions)
├── api/
│   └── v1.php               # API v1 routes
└── web.php                   # Web routes (Blade views, sessions)
```

- API v1: `/api/v1/*` (prefix `/api/v1`, stateless)
- Web: `/` (Blade views, sessions)

## Common Commands

```bash
# Development
composer run dev              # Start all dev services
composer run test             # Run tests

# Artisan generators
php artisan make:model Post -mfsc      # Model + migration + factory + seeder + controller
php artisan make:controller Api/PostController --api  # API resource controller
php artisan make:request StorePostRequest             # Form request validation
php artisan make:resource PostResource                # API resource
php artisan make:resource PostCollection              # API collection

# Database
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Cache
php artisan optimize:clear
php artisan route:cache
```

## Laravel Best Practices

### Controllers
- Keep controllers thin - move business logic to Services
- Use Form Requests for validation
- API controllers return `JsonResponse` or API Resources
- Web controllers return `view()` with data

```php
// API Controller
class PostController extends Controller
{
    public function index(): JsonResponse
    {
        return PostResource::collection(Post::paginate())->response();
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::create($request->validated());
        return PostResource::make($post)->response()->setStatusCode(201);
    }
}

// Web Controller
class PostController extends Controller
{
    public function index(): View
    {
        return view('posts.index', ['posts' => Post::paginate()]);
    }
}
```

### Models
- Use fillable/guarded for mass assignment protection
- Define relationships explicitly
- Use scopes for reusable queries
- Cast attributes properly

```php
class Post extends Model
{
    protected $fillable = ['title', 'content', 'user_id'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }
}
```

### Form Requests
```php
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or auth logic
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }
}
```

### API Resources
```php
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author' => UserResource::make($this->whenLoaded('user')),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
```

### HTTP Status Codes
- 200 OK, 201 Created, 204 No Content
- 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 422 Unprocessable
- 500 Internal Server Error

## Implemented Features

### Authentication API (v1)
All auth endpoints: `/api/v1/auth/*`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/register` | No | Register new user |
| POST | `/login` | No | Login, returns token |
| POST | `/logout` | Yes | Revoke current token |
| GET | `/user` | Yes | Get authenticated user |
| POST | `/forgot-password` | No | Send reset email |
| POST | `/reset-password` | No | Reset password with token |
| POST | `/email/resend-verification` | Yes | Resend verification email |

Email verification: `GET /verify-email/{id}/{hash}` (web route with Blade view)

### Environment Variables
```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:8000
MOBILE_APP_SCHEME=ironman
```

### Services (Business Logic)
```php
class PostService
{
    public function create(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            $post = Post::create($data);
            // Additional business logic
            return $post;
        });
    }
}
```

## Blade Templates

### Layout Structure
```
resources/views/
├── layouts/
│   └── app.blade.php         # Main layout
├── components/               # Blade components
├── partials/                 # Reusable partials
└── [feature]/
    ├── index.blade.php
    ├── show.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### Components
```php
// Anonymous component: resources/views/components/button.blade.php
<button {{ $attributes->merge(['class' => 'btn']) }}>
    {{ $slot }}
</button>

// Usage
<x-button class="btn-primary">Submit</x-button>
```

### AJAX with Blade
```javascript
// resources/js/app.js - Use fetch for API calls from Blade
async function fetchPosts() {
    const response = await fetch('/api/posts', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    return response.json();
}
```

## Code Style

- **PSR-12** coding standards
- **Type hints** for all parameters and return types
- **Strict types** declaration in PHP files: `declare(strict_types=1);`
- Format with Laravel Pint: `vendor/bin/pint`
- Use named arguments for clarity when appropriate
- Prefer early returns over nested conditions

## Testing

```bash
composer run test
php artisan test --filter=PostTest
php artisan test --coverage
```

### Test Structure
```php
// Feature test for API
class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_posts(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_post(): void
    {
        $data = ['title' => 'Test', 'content' => 'Content'];

        $response = $this->postJson('/api/posts', $data);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Test');
        $this->assertDatabaseHas('posts', $data);
    }
}
```

## Security

- Always validate input with Form Requests
- Use Eloquent or Query Builder (never raw SQL with user input)
- Escape output in Blade: `{{ $var }}` (auto-escaped), `{!! $html !!}` (unescaped - careful!)
- Use CSRF protection for web forms: `@csrf`
- API authentication: Laravel Sanctum for SPA/tokens
- Authorize actions with Policies
