<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Trait ini menyediakan $this->authorize() (Materi 5: Gates & Policies)
    use AuthorizesRequests;
}
