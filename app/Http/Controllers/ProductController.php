<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        sleep(1);
        return Product::all();
    }

    public function show($id)
    {
        sleep(1);
        return Product::findOrFail($id);
    }
}
