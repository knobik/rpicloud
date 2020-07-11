<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\ConfigResource;

class ConfigController extends ApiController
{
    public function index()
    {
        return new ConfigResource(null);
    }
}
