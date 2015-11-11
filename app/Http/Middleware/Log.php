<?php

namespace App\Http\Middleware;


class Log
{

    public function handle($request, \Closure $next)
    {
        $agent = "";
        
        if ( stripos($request->header('user_agent'), 'Firefox') !== false ) {
            $agent = 'Firefox';
        } elseif ( stripos($request->header('user_agent'), 'MSIE') !== false ) {
            $agent = 'IE';
        } elseif ( stripos($request->header('user_agent'), 'iPad') !== false ) {
            $agent = 'iPad';
        } elseif ( stripos($request->header('user_agent'), 'Android') !== false ) {
            $agent = 'Android';
        } elseif ( stripos($request->header('user_agent'), 'Chrome') !== false ) {
            $agent = 'Chrome';
        } elseif ( stripos($request->header('user_agent'), 'Safari') !== false ) {
            $agent = 'Safari';
        } elseif ( stripos($request->header('user_agent'), 'AIR') !== false ) {
            $agent = 'Air';
        } elseif ( stripos($request->header('user_agent'), 'Fluid') !== false ) {
            $agent = 'Fluid';
        }

        if ($agent == ""){
            $agent = 'Terminal';
        }

        file_put_contents('log',
            '/' . $request->path() . '   ' . $agent . "\r\n" , FILE_APPEND);


        return $next($request);
    }
}
