<?php

declare(strict_types=1);

return [

    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    // The release version of your application
    // Example with dynamic git SHA: trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD'))
    'release' => env('SENTRY_RELEASE'),

    // When left empty or `null` the Laravel environment will be used
    'environment' => env('SENTRY_ENVIRONMENT'),

    'breadcrumbs' => [
        // Capture Laravel logs in breadcrumbs
        'logs' => true,

        // Capture SQL queries in breadcrumbs
        'sql_queries' => true,

        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,

        // Capture queue job information in breadcrumbs
        'queue_info' => true,

        // Capture command information in breadcrumbs
        'command_info' => true,
    ],

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#send-default-pii
    'send_default_pii' => env('SENTRY_SEND_DEFAULT_PII', false),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#traces-sample-rate
    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.2),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#profiles-sample-rate
    'profiles_sample_rate' => (float) env('SENTRY_PROFILES_SAMPLE_RATE', 0.2),

    // Ignore specific exceptions
    'ignore_exceptions' => [
        Illuminate\Auth\AuthenticationException::class,
        Illuminate\Auth\Access\AuthorizationException::class,
        Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        Illuminate\Database\Eloquent\ModelNotFoundException::class,
        Illuminate\Session\TokenMismatchException::class,
        Illuminate\Validation\ValidationException::class,
    ],

    // Ignore transactions matching these patterns
    'ignore_transactions' => [
        // Health check endpoints
        '/up',
        '/health',
        '/health/*',
        // Static assets
        '*.js',
        '*.css',
        '*.jpg',
        '*.jpeg',
        '*.png',
        '*.gif',
        '*.svg',
        '*.ico',
        '*.woff',
        '*.woff2',
        '*.ttf',
        '*.eot',
    ],

    // Performance monitoring
    'tracing' => [
        // Trace queue jobs
        'queue_job_transactions' => env('SENTRY_TRACE_QUEUE_ENABLED', true),

        // Trace SQL queries
        'sql_queries' => env('SENTRY_TRACE_SQL_QUERIES_ENABLED', true),

        // Trace Redis commands
        'redis_commands' => env('SENTRY_TRACE_REDIS_COMMANDS', true),

        // Trace HTTP client requests
        'http_client_requests' => env('SENTRY_TRACE_HTTP_CLIENT_REQUESTS', true),
    ],

    // Context
    'context' => [
        // Capture user context
        'user' => true,

        // Capture server context
        'server' => true,

        // Capture extra context
        'extra' => [
            'environment' => env('APP_ENV'),
            'version' => env('APP_VERSION', '1.0.0'),
        ],
    ],

    // Before send callback
    'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
        // You can modify or filter events here
        // Return null to not send the event
        return $event;
    },

    // Before send transaction callback
    'before_send_transaction' => function (\Sentry\Event $event): ?\Sentry\Event {
        return $event;
    },

    // Integrations
    'integrations' => [
        // Enable Laravel integration
        \Sentry\Laravel\Integration::class,
    ],

];
