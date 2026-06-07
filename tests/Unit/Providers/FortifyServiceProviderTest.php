<?php

declare(strict_types=1);

use App\Providers\FortifyServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

uses(Tests\TestCase::class);

test('FortifyServiceProvider configures rate limiting branches', function (): void {
    $provider = new FortifyServiceProvider(app());

    $method = new ReflectionMethod(FortifyServiceProvider::class, 'configureRateLimiting');
    $configureRateLimiting = $method->getClosure($provider);

    $configureRateLimiting();

    $session = app('session.store');
    $session->start();

    $loginRequest = Request::create('/login', 'POST', []);
    $loginRequest->server->set('REMOTE_ADDR', '203.0.113.10');
    $loginRequest->setLaravelSession($session);
    $session->put('login.id', 'login-id');

    $twoFactorRequest = Request::create('/two-factor', 'POST', []);
    $twoFactorRequest->server->set('REMOTE_ADDR', '203.0.113.10');
    $twoFactorRequest->setLaravelSession($session);

    $passkeyRequest = Request::create('/passkeys', 'POST', [
        'credential' => ['id' => 'credential-id'],
    ]);
    $passkeyRequest->server->set('REMOTE_ADDR', '203.0.113.10');
    $passkeyRequest->setLaravelSession($session);

    $loginLimiter = RateLimiter::limiter('login');
    $passkeyLimiter = RateLimiter::limiter('passkeys');
    $twoFactorLimiter = RateLimiter::limiter('two-factor');

    expect($loginLimiter)->not->toBeNull()
        ->and($loginLimiter($loginRequest)->key)->toBe('|203.0.113.10')
        ->and($loginLimiter($loginRequest)->maxAttempts)->toBe(5)
        ->and($passkeyLimiter)->not->toBeNull()
        ->and($passkeyLimiter($passkeyRequest)->key)->toBe('credential-id|203.0.113.10')
        ->and($twoFactorLimiter)->not->toBeNull()
        ->and($twoFactorLimiter($twoFactorRequest)->key)->toBe('login-id');
});
