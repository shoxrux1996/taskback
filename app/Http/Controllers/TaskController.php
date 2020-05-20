<?php

namespace App\Http\Controllers;

use Google_Client;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('credentials.json'));

    }
}
