protected $middlewareGroups = [
    'web' => [
        // ... other middleware ...
        \App\Http\Middleware\TrackLastLoginAt::class,
    ],
];