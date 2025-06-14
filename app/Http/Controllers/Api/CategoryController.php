<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function store (Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required',
            'description' => 'required',
             'meta_title' => 'required',
              'status' => 'nullable'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }else {
            $category = new Category;
            $category->name = $request->input('name');
            $category->slug = $request->input('slug');
            $category->description = $request->input('description') ;
            $category->meta_title = $request->input('meta_title') ;
            $category->status = $request->status == true ? '1' : '0';

             if ($request->hasFile('image')) {
                        
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() .'.'. $ext;
            $file->move('uploads/products/', $filename);
            $category->image= "uploads/products/$filename";

        }
        

            $category->save();
            return response()->json([
                'message' => 'inserted'
            ],200);
        }
    }

    public function index() {
        $categories = Category::all();
        return response()->json([
            'data' => $categories
        ]);

    }

    public function show($category) {
        $category = Category::Find($category);
        return response()->json($category, 200);

    }

    public function update(Request $request,$category){
        $category = Category::Find($category);

        $validator = Validator::make($request->all(),[
             'name' => 'required',
            'slug' => 'required',
            'description' => 'required',
             'meta_title' => 'required',
              'status' => 'nullable'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'all fields are required',
                    'error' => $validator->messages(),
                ],422);
            }else {
                 $category->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'status' => $request->status == true ? '0' : '1'
            ]);
            return response()->json([
                'message'=>'updated',
            ]);
             
            }
      
    }

    public function destroy($id) {
        $category = Category::Find($id);
        if ($category) {
            $category->delete();
            return response()->json([
                'message' => 'deleted'
            ],200);
            
        }else {
             return response()->json([
                'message' => 'deleted'
            ],404);
        } 
    }

    public function view(){
        $category = Category::where('status', '1')->get();
        return response()->json([
            'data' => $category
        ],200);
    }

    public function products($slug) {
        $category = Category::where('slug', $slug)->where('status', 1)->first();
        if ($category) {
            $product = Product::where('category_id', $category->id)
            ->where('status', 1)->get();
            if ($product) {
                return response()->json([
                    'data' => $product,
                    'category' => $category
                ],200);
            }else {
                return response()->json([
                    'message' => 'no product available for this category'
                ], 404);
            }
        }else {
             return response()->json([
            'message' => 'no such category'
        ], 404);
        }
       
    }

    public function viewProduct($category_slug, $product_slug) {
         $category = Category::where('slug', $category_slug)
         ->where('status', 1)->first();
        if ($category) {
            $product = Product::where('category_id', $category->id)
            ->where('slug', $product_slug)
            ->where('status', 1)->first();
            if ($product) {
                return response()->json([
                    'status' => 200,
                    'data' => $product
                ]);
            }else {
                return response()->json([
                    'message' => 'no product available'
                ], 404);
            }
        }else {
             return response()->json([
            'message' => 'no such category'
        ], 404);
        }
    }

    public function cart(Request $request) {
        $user_id = Auth::user()->id;
        $prod_id = $request->product_id;
        $prod_qty = $request->product_quantity;
        $prod_name = $request->product_name;
        $prod_price = $request->product_price;
         

        $productCheck = Product::where('id', $prod_id)->first();
        if ($productCheck) {
            if (Cart::where('prod_id', $prod_id)->where('user_id', $user_id)->exists()) {
                return response()->json([
                    'message' => 'product already added to cart'
                ]);
            }else {
                $cartitem = new Cart;
                $cartitem->user_id = $user_id;
                
                $cartitem->product_quantity = $prod_qty;
                $cartitem->prod_id = $prod_id;
                $cartitem->product_name = $prod_name;
                $cartitem->product_price = $prod_price;
                
                $cartitem->product_image = $request->image;

        
      
                $cartitem->save();
                return response()->json([
                    'message' => 'product added to cart'
                ]);
            }
        }else {
            
            return response()->json([
                
                    'message' => 'product not found'
                ],404);
        }   
        
    }

    public function viewCart() {
        $cartitem = Cart::where('user_id', Auth::user()->id)->get();
        return response()->json([
            'data' => $cartitem
        ]);
        
    }

    public function updateCart($cart_id, $scope){
        $cartitem = Cart::where('id', $cart_id)->where('user_id', Auth::user()->id)->first();
        if ($scope === 'inc') {
            $cartitem->product_quantity += 1;
        }elseif ($scope === 'dec') {
            $cartitem->product_quantity -= 1;
        }
        $cartitem->update();
        return response()->json([
            'message' => 'ok'
        ],200);
    }

    public function deleteCart($id) {
        $cartitem = Cart::where('id', $id)->where('user_id', Auth::user()->id)->first();
            $cartitem->delete();
             return response()->json([
            'message' => 'item removed from card'
        ]);
        
       
    }

   public function checkout(Request $request) {
    $validator = Validator::make($request->all(), [
        'firstname' => 'required',
        'surname' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'street' => 'required',
        'district' => 'required',
        'city' => 'required',
        'state' => 'required',
        'total' => 'required|numeric',
        'user_type' => 'required',
        'items' => 'required|array',
      
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages(),
        ], 422);
    }

    $amount = $request->total * 100;

    $response = Http::withHeaders([
        'Authorization' => 'Bearer sk_test_5f51876ef5ba1a3ea542c81b04310311fa8a87ba',
        'Content-Type' => 'application/json',
    ])->post('https://api.paystack.co/transaction/initialize', [
        'amount' => $amount,
        'email' => $request->email,
        'callback_url' => "http://localhost:5173/payment-success",
    ])->json();

    $order = new Order;
    $order->firstname = $request->firstname;
    $order->surname = $request->surname;
    $order->email = $request->email;
    $order->phone = $request->phone;
    $order->street = $request->street;
    $order->district = $request->district;
    $order->city = $request->city;
    $order->state = $request->state;
    $order->total = $request->total;
    $order->reference = $response['data']['reference'];
    $order->user_type = $request->user_type;
    $order->status = 'pending';
    $order->save();

    foreach ($request->items as $item) {
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $item['product_id'],
        'product_quantity' => $item['quantity'], // typo fixed below
        'product_price' => $item['price'],
    ]);

    // Reduce product stock
    $product = Product::find($item['product_id']);
    if ($product) {
        $product->quantity -= $item['quantity'];
        $product->save();
    }
}


    return response()->json([
        'access_code' => $response['data']['access_code'],
        'reference' => $response['data']['reference'],
    ], 200);
}



public function verify($reference) {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer sk_test_5f51876ef5ba1a3ea542c81b04310311fa8a87ba',
    ])->get("https://api.paystack.co/transaction/verify/{$reference}");

    if ($response->successful() && $response['data']['status'] === 'success') {
         $order = Order::where('reference', $reference)->first();
        if ($order) {
            $order->status = 'paid';
            $order->update();

            return response()->json([
            'status' => 200,
            'message' => 'ok sweets',
            
        ]);
        }else{
            return response()->json([
                'message' => 'failed'
            ]);
        }
     
        
    }else {
        return response()->json([
            'message' => 'payment failed'
        ], 500);
    }

   
}

 
     public function clear () {
        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if ($cart) {
             $cart->delete();
        return response()->json([
            'data' => $cart,
            'messsage' => 'ok'
        ]);
        }else {
            return response()->json([
                
            ],404);
        }
       
    }

    public function orders()
{
    $orders = Order::all(); // Or use pagination: Order::paginate(10)
    return response()->json($orders);
}

public function details ($id) {
   $order = Order::with('orderItems.product')->find($id);

    return response()->json([
        'message' => 'ok',
        'data' => $order
    ]);
}

}
