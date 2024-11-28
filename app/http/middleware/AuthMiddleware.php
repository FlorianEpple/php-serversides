<?php

namespace app\http\middleware;

use lib\http\Request;

class AuthMiddleware
{
    public static function validate(Request $request)
    {
        $middlewareReturns = $request->getMiddlewareReturns();

        //

        return;
    }
}
