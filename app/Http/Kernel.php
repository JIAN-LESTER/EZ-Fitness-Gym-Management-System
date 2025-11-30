protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\CheckMembershipExpiration::class,
    ],
];