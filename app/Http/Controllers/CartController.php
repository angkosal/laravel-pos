<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response(
                $request->user()->cart()->get()
            );
        }
        return view('cart.index', ['user_id' => Auth::user()->id]);
    }

    public function getProducts(Request $request){
        $user = User::find($request->user_id);

        $products = Product::where('store_id', $user->store->id)->with('productOptions.optionDetails')->get();

        if ($request->search) {
            $products = Product::where('store_id', $user->store->id)->where('name', 'like', "%" . $request->search . "%")->get();
        }

        return response()->json([
            'products' => $products
        ]);
    }

    public function getStudentOrders(Request $request){
        $student_number = $request->student_number;
        $user = User::find($request->user_id);
        $productIds = $user->store->products()->pluck('id')->toArray();
        $now = Carbon::now();

        $student = Student::where('student_number', $student_number)->get();

        if($student->count() == 0){
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        $orders = Order::whereHas('student', function ($query) use ($student_number) {
                $query->where('student_number', $student_number);
            })
            ->whereHas('orderDetails', function ($query) use ($productIds) {
                $query->whereIn('product_id', $productIds);
            })
//            ->whereDate('pick_up_start', '>=', $now)
//            ->whereDate('pick_up_end', '<=', $now)
            ->get();

        //$orders = filterOrders($orders);

        foreach ($orders as $order) {
            $details = OrderDetail::where('order_id', $order->id)->whereIn('product_id', $productIds)->get();
            $orders->find($order->id)->order_details = $details;

        }

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|exists:products,barcode',
        ]);
        $barcode = $request->barcode;

        $product = Product::where('barcode', $barcode)->first();
        $cart = $request->user()->cart()->where('barcode', $barcode)->first();
        if ($cart) {
            // check product quantity
            if($product->quantity <= $cart->pivot->quantity) {
                return response([
                    'message' => 'Product available only: '. $product->quantity,
                ], 400);
            }
            // update only quantity
            $cart->pivot->quantity = $cart->pivot->quantity + 1;
            $cart->pivot->save();
        } else {
            if($product->quantity < 1) {
                return response([
                    'message' => 'Product out of stock',
                ], 400);
            }
            $request->user()->cart()->attach($product->id, ['quantity' => 1]);
        }

        return response('', 204);
    }

    public function changeQty(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $request->user()->cart()->where('id', $request->product_id)->first();

        if ($cart) {
            $cart->pivot->quantity = $request->quantity;
            $cart->pivot->save();
        }

        return response([
            'success' => true
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);
        $request->user()->cart()->detach($request->product_id);

        return response('', 204);
    }

    public function empty(Request $request)
    {
        $request->user()->cart()->detach();

        return response('', 204);
    }
}
