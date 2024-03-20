<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\storeOrUpdateApiMhsRequest;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function storeOrUpdate(storeOrUpdateApiMhsRequest $request){
        dd($request);
    }
}
