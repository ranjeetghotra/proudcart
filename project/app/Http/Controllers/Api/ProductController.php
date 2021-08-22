<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductClick;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $productLimit = 4;
        $ps = DB::table('pagesettings')->find(1);
        // $data["categories"] = Category::where('is_featured', '=', 1)->get();
        $selectable = ['id', 'name', 'thumbnail', 'price', 'previous_price', 'attributes', 'user_id'];
        // $data['sliders'] = DB::table('sliders')->get();
        // $data['top_small_banners'] = DB::table('banners')->where('type', '=', 'TopSmall')->get();
        // $data['bottom_small_banners'] = DB::table('banners')->where('type', '=', 'BottomSmall')->get();
        // $data['large_banners'] = DB::table('banners')->where('type', '=', 'Large')->get();
        $products['feature_products'] = Product::where('featured', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['discount_products'] = Product::where('is_discount', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['best_products'] = Product::where('best', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['top_products'] = Product::where('top', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['big_products'] = Product::where('big', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['hot_products'] = Product::where('hot', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['latest_products'] = Product::where('latest', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['trending_products'] = Product::where('trending', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $products['sale_products'] = Product::where('sale', '=', 1)->where('status', '=', 1)->select($selectable)->orderBy('id', 'desc')->take($productLimit)->get();
        $data['products'] = $this->getRatings($products);
        return response(["status" => true, "data" => $data]);
    }
    public function product($id)
    {
        // $this->code_image();
        $productt = Product::where('id', '=', $id)->firstOrFail();
        if ($productt->status == 0) {
            return response(["status" => false]);
        }
        $productt->views += 1;
        $productt->update();
        $productt->rating = Rating::ratingAvg($productt->id);
        $productt->galleries;
        $this->setCharges($productt);
        $product_click = new ProductClick;
        $product_click->product_id = $productt->id;
        $product_click->date = Carbon::now()->format('Y-m-d');
        $product_click->save();
        return response(["status" => true, 'data' => $productt]);
    }
    private function getRatings($products)
    {
        foreach ($products as $data) {
            foreach ($data as $item) {
                $this->setCharges($item);
                $item->rating = Rating::ratingAvg($item->id);
            }
        }
        return $products;
    }
    private function setCharges($productt)
    {
        $gs = cache()->remember('generalsettings', now()->addDay(), function () {
            return DB::table('generalsettings')->first();
        });
        if ($productt->user_id != 0) {
            $productt->price = $productt->price + $gs->fixed_commission + ($productt->price / 100) * $gs->percentage_commission;
            $productt->previous_price = $productt->previous_price + $gs->fixed_commission + ($productt->previous_price / 100) * $gs->percentage_commission;
        }

        // Attribute Section

        $attributes = $productt->attributes;
        if (!empty($attributes)) {
            $attrArr = json_decode($attributes, true);
        }

        // dd($attrArr);
        if (!empty($attrArr)) {

            foreach ($attrArr as $attrKey => $attrVal) {
                if (array_key_exists("details_status", $attrVal) && $attrVal['details_status'] == 1) {
                    foreach ($attrVal['values'] as $optionKey => $optionVal) {
                        $productt->price += $attrVal['prices'][$optionKey];
                        $productt->previous_price += $attrVal['prices'][$optionKey];
                        // only the first price counts
                        break;
                    }

                }
            }
        }

        // Attribute Section Ends

        $curr = cache()->remember('default_currency', now()->addDay(), function () {
            return DB::table('currencies')->where('is_default', '=', 1)->first();
        });
        $productt->price = round(($productt->price) * $curr->value, 2);
        $productt->previous_price = round(($productt->previous_price) * $curr->value, 2);
    }
}
