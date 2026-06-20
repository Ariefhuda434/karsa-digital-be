<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();
        return response()->json(Order::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type'  => 'required|string',
            'project_name'  => 'required|string',
            'description'   => 'required|string',
            'budget_range'  => 'required|string',
            'timeline'      => 'nullable|string',
            'client_name'   => 'required|string',
            'client_email'  => 'required|email',
            'client_phone'  => 'nullable|string',
            'company'       => 'nullable|string',
        ]);
        return response()->json(Order::create($data), 201);
    }

    public function show(Order $order)
    {
        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,in_review,accepted,in_progress,done,cancelled']);
        $order->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);
        return response()->json($order);
    }
}
