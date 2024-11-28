<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class Authenticate extends Middleware
{
    use AuthenticatesUsers;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        // if (! $request->expectsJson()) {
        //     return route('login');
        // }
        
        $data_request = array(
            $this->username() =>'admin@email.com',
            'password'  => 'admin123'
        );

        if ($this->attemptLogin($data_request)) {
            return route('upload/import');
            // return redirect('upload/import');
        }
    }

    
    protected function attemptLogin($params){
        return $this->guard()->attempt(
            $params, true
        );
    }
}
