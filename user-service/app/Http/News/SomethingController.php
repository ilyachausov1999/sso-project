<?php

declare(strict_types = 1);

namespace App\Http\News;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SomethingController extends Controller
{
    public function getSomething(Request $request): JsonResponse
    {
        return response()->json([$request->get('user')]);
    }
}
