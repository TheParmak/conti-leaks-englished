<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Angular
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        ini_set('memory_limit', '-1');
        ini_set('post_max_size', '20M');
        ini_set('upload_max_filesize', '20M');

        if (Auth::check()){
//            if(!$request->isMethod('post') && $_SERVER['HTTP_ACCEPT'] == 'application/json, text/plain, */*'){
//                $post = json_decode(file_get_contents("php://input"), true);
//                if(!empty($post)){
//                    $request->merge($post);
//                }
//            }
            return $next($request);
        }
    }
}
