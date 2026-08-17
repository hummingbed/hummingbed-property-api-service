<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureServerlessSqlite
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/v1/health')) {
            return $next($request);
        }

        if ($this->requiresInitialization()) {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        }

        return $next($request);
    }

    private function requiresInitialization(): bool
    {
        return (bool) env('VERCEL')
            && config('database.default') === 'sqlite'
            && ! Schema::hasTable('migrations');
    }
}
