<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $c = Category::all();
        return response(["status" => true, "data"=> $c]);
    }
    public function feature(Request $request)
    {
        $c = Category::where('is_featured',1)->get();
        return response(["status" => true, "data"=> $c]);
    }
    public function Subcategory(Request $request, $cid)
    {
        $c = Subcategory::where('category_id', $cid)->get();
        return response(["status" => true, "data"=> $c]);
    }
    public function Childcategory(Request $request, $sid)
    {
        $c = Childcategory::where('subcategory_id', $sid)->get();
        return response(["status" => true, "data"=> $c]);
    }
}
