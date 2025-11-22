<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statuses = $request->get('status', []);

        $sales = Sales::query()
            ->with('user', 'items')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('sales_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q2) use ($search) {
                            $q2->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                });
            })
            ->when(!empty($statuses), function ($query) use ($statuses) {
                return $query->whereIn('status', $statuses);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        return view('admin.sales', compact(
            'sales',
            'search',
            'statuses'
        ));
    }

    public function show($id)
    {
        $sale = Sales::with(['user', 'items.product'])->findOrFail($id);

        return response()->json($sale);
    }
}
