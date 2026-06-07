<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Illuminate\Validation\Rules\Password;

uses(Tests\TestCase::class);

test('AppServiceProvider configures production password defaults', function (): void {
    $originalEnv = app()->environment();
    app()->detectEnvironment(fn (): string => 'production');

    $provider = new AppServiceProvider(app());

    $method = new ReflectionMethod(AppServiceProvider::class, 'configureDefaults');
    $configureDefaults = $method->getClosure($provider);

    try {
        $configureDefaults();

        $defaultPassword = Password::default();
        $uncompromised = Closure::bind(
            static fn (Password $password): bool => $password->uncompromised,
            null,
            Password::class,
        );
        $symbols = Closure::bind(
            static fn (Password $password): bool => $password->symbols,
            null,
            Password::class,
        );

        expect($defaultPassword)->toBeInstanceOf(Password::class)
            ->and($uncompromised && $uncompromised($defaultPassword))->toBeTrue()
            ->and($symbols && $symbols($defaultPassword))->toBeTrue();
    } finally {
        app()->detectEnvironment(fn (): string => $originalEnv);
    }
});
