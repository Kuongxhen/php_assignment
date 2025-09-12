<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value)) {
                // Remove potentially dangerous characters
                $value = trim($value);
                // Basic XSS protection
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                // Remove null bytes
                $value = str_replace(chr(0), '', $value);
            }
        });
        
        $request->replace($input);
        
        return $next($request);
    }
}
