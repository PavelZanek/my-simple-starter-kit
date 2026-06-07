<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithCachedConfig;
use Illuminate\Foundation\Testing\WithCachedRoutes;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    use WithCachedConfig, WithCachedRoutes;

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}
