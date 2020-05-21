<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Tasks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;

class ClientController extends Controller
{
    public function redirectToGoogle()
    {
        $parameters = ['access_type' => 'offline'];
        return Socialite::driver('google')
            ->stateless()
            ->with($parameters)
            ->scopes([Google_Service_Tasks::TASKS, Google_Service_Tasks::TASKS_READONLY])
            ->redirect();
    }
    public function callback(Request $request)
    {
        $user = Socialite::driver('google')->stateless()->user();
        Cache::forever('token', $user->token);
        Cache::forever('expiresIn', $user->expiresIn);
        return view('welcome');
    }
}
