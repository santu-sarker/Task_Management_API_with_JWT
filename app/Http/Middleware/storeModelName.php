<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;


/**
 * Class SetModel
 */
class storeModelName
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->segment(1) === 'api') {
            $segment   = Str::singular($request->segment(2));
            $modelName = 'App\\Models\\' . ucfirst(Str::camel($segment));

            $request->attributes->set(
                'model',
                $modelName
            );
        }
        return $next($request);
    }
}
