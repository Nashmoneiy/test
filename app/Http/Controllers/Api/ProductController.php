<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request) {
         $validator = Validator::make($request->all(),[
            'name' => 'required',
            'category' => 'required',
            'slug' => 'required',
            'brand' => 'required',
            'image' => 'required',
            'description' => 'required',
            'meta_title' => 'required',
            'original_price'=>'required',
            'selling_price'=>'required',
            'quantity'=>"required",
            'trending'=>'nullable',
            'status'=>'nullable',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }else {

            $product = new Product;
            $product->name = $request->name;
            $product->category_id = $request->category;
            $product->slug = $request->slug;
            $product->brand= $request->brand;
            $product->description = $request->description;
            $product->original_price = $request->original_price;
            $product->selling_price = $request->selling_price;
            $product->quantity = $request->quantity;
            $product->trending = $request->trending == true ? 1:0;
            $product->status = $request->status == true ? 1:0;
            $product-> meta_title = $request->meta_title;

            
        if ($request->hasFile('image')) {
                        
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() .'.'. $ext;
            $file->move('uploads/products/', $filename);
            $product->image= "uploads/products/$filename";

        }
        $product->save();

            return response()->json([
                'message' => 'inserted',
                'data' => $product
            ]);
        }
  
    }

    
    public function index() {
        $product = Product::all();
        return response()->json([
            'message' => 'ok',
            'data' => $product
        ]);
    }

    public function show($id) {

    $product = Product::Find($id);
        if ($product) {
            return response()->json($product, 200);
        }else {
            return response()->json([
                'message' => 'page not found',
                'status' => 404
            ],404);
        }

    }

    public function update(Request $request, $id) {
         $validator = Validator::make($request->all(),[
            'name' => 'required',
            'category' => 'required',
            'slug' => 'required',
            'brand' => 'required',         
            'description' => 'required',
            'meta_title' => 'required',
            'original_price'=>'required',
            'selling_price'=>'required',
            'quantity'=>"required",
            'trending'=>'nullable',
            'status'=>'nullable',

        ]);
         if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }else{
            $product = Product::find($id);
            if ($product) {
            $product->name = $request->name;
            $product->category_id = $request->category;
            $product->slug = $request->slug;
            $product->brand= $request->brand;
            $product->description = $request->description;
            $product->original_price = $request->original_price;
            $product->selling_price = $request->selling_price;
            $product->quantity = $request->quantity;
            $product->trending = $request->trending == true ? 1:0;
            $product->status = $request->status == true ? 1:0;
            $product-> meta_title = $request->meta_title;

             if ($request->hasFile('image')) {
            $path = 'uploads/products/'.$product->image;
            if (File::exists($path)) {
                File::delete();
            }  
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() .'.'. $ext;
            $file->move('uploads/products/', $filename);
            $product->image= "uploads/products/$filename";

        }
        $product->update();



                return response()->json([
                    'message' => 'updated',
                'data' => $product
            ]);
            }
            
        }
      

    }

 
}



