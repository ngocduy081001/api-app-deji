<?php

namespace Vendor\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function index()
    {
        
        return response()->json(['message' => 'Hello from product']);
    }
}
