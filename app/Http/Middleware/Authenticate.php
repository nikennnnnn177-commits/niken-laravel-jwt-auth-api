<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
    return response()->json([
        'code' => 401,
        'message' => 'Unauthorized - Token not provided',
        'data' => null
    ], 401);
}

}
