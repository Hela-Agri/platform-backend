<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Response as ErrorResponse;
use InfyOm\Generator\Utils\ResponseUtil;
class FormatErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        if (!empty($response->exception) && $response->exception instanceof QueryException) {
            return ErrorResponse::json($response->exception,400);
        }
        
        return $response;
    }
    
}
