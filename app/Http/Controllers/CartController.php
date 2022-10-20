<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Show the POS page.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response(
                $request->user()->cart()->get()
            );
        }
        return view('cart.index', ['user_id' => Auth::user()->id]);
    }

    /**
     * Get the given product's detail.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $products = Product::where('store_id', $user->store->id)
            ->where('status', true)
            ->with('productOptions.optionDetails')
            ->get();

        if ($request->search) {
            $products = Product::where('store_id', $user->store->id)->where('name', 'like', "%" . $request->search . "%")->get();
        }

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Get the products by a barcode.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsByBarcode(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $products = Product::where('store_id', $user->store->id)
            ->where('barcode', $request->barcode)
            ->where('status', true)
            ->with('productOptions.optionDetails')
            ->get();

        if($products->count() == 0){
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Get the given student's order.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentOrders(Request $request)
    {
        $student_number = $request->student_number;
        $user = User::find(Auth::user()->id);
        $productIds = $user->store->products()->pluck('id')->toArray();
        $now = Carbon::now();

        $student = Student::where('student_number', $student_number)->get();

        if($student->count() == 0){
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        $student = Student::where('student_number', $student_number)->first();

        $orders = Order::whereHas('student', function ($query) use ($student_number) {
                $query->where('student_number', $student_number);
            })
            ->whereHas('orderDetails', function ($query) use ($productIds) {
                $query->whereIn('product_id', $productIds)->where('is_pickup', false);
            })
            ->where('status', '>=', Order::PAYMENT_SUCCESS)
            ->whereDate('pick_up_start', '>=', $now)
            ->whereDate('pick_up_end', '<=', $now)
            ->get();

        foreach ($orders as $order) {
            $details = OrderDetail::where('order_id', $order->id)->whereIn('product_id', $productIds)->get();
            $orders->find($order->id)->order_details = $details;
        }

        return response()->json([
            'orders' => $orders,
            'student' => $student,
        ]);
    }

    /**
     * Update the student's existing order or create a new order.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'isNewOrder' => 'required|boolean',
            'student_number' => 'required',
            'order_details' => 'required_if:isNewOrder,==,true',
            'order_ids' => 'required_if:isNewOrder,==,false',
        ]);

        $user = User::find(Auth::user()->id);
        $productIds = $user->store->products()->pluck('id')->toArray();

        if($request->isNewOrder){

            $student = Student::where('student_number', $request->student_number)->first();
            $orderDetails = $request->order_details;

            $order = Order::create([
                'student_id' => $student->id,
                'pick_up_start' => Carbon::now(),
                'pick_up_end' => Carbon::now(),
                'total_price' => $request->total_price,
                'status' => Order::PICKUP_ALL,
                'is_sandbox_order' => $student->is_a_sandbox_student,
            ]);

            $order->payments()->create([
                'payment_type_id' => PaymentType::PAYMENT_CASH,
                'amount' => $request->total_price,
                'status' => Payment::STATUS_SUCCESS,
                'is_sandbox_payment' => $student->is_a_sandbox_student,
            ]);

            foreach($orderDetails as $detail){
                $order->orderDetails()->create([
                    'product_id' => $detail['product_id'],
                    'product_options' => $detail['product_options'],
                    'price' => $detail['price'],
                    'notes' => $detail['notes'],
                    'is_pickup' => true,
                ]);
            }

            return response()->json(['message' => 'Order created successful.']);

        } else {

            $orders = Order::whereIn('id', $request->order_ids)->get();

            foreach($orders as $order){
                $details = $order->orderDetails()->whereIn('product_id', $productIds)->get();
                foreach ($details as $detail) {
                    $detail->update([
                        'is_pickup' => true,
                    ]);
                }

                if($order->orderDetails()->where('is_pickup', false)->count() == 0){
                    $order->update([
                        'status' => Order::PICKUP_ALL,
                    ]);
                } else {
                    $order->update([
                        'status' => Order::PICKUP_PARTIALLY,
                    ]);
                }
            }

            return response()->json(['message' => 'Orders update successful.']);
        }
    }

}
