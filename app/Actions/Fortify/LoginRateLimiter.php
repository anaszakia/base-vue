<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Laravel\Fortify\LoginRateLimiter as FortifyLoginRateLimiter;

class LoginRateLimiter extends FortifyLoginRateLimiter
{
    private const MAX_ATTEMPTS = 3;

    private const DECAY_SECONDS = 900;

    public function tooManyAttempts(Request $request): bool
    {
        return $this->limiter->tooManyAttempts(
            $this->lockoutKey($request),
            1,
        );
    }

    public function increment(Request $request): void
    {
        $attempts = $this->limiter->increment(
            $this->throttleKey($request),
            self::DECAY_SECONDS,
        );

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->limiter->hit($this->lockoutKey($request), self::DECAY_SECONDS);
        }
    }

    public function availableIn(Request $request): int
    {
        return $this->limiter->availableIn($this->lockoutKey($request));
    }

    public function clear(Request $request): void
    {
        $this->limiter->clear($this->throttleKey($request));
        $this->limiter->clear($this->lockoutKey($request));
    }

    private function lockoutKey(Request $request): string
    {
        return $this->throttleKey($request).':lockout';
    }
}
