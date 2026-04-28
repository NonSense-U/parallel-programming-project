<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    
public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->get();
        return ApiResponse::success('Orders retrieved successfully', data: $orders);
    }
}
