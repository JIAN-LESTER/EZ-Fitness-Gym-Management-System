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
        $paymentMethods = $request->get('payment_method', []);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $sales = Sales::query()
            ->with('user', 'items')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('sales_id', 'like', "%{$search}%")
                        ->orWhere('reference_code', 'like', "%{$search}%")
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
            ->when(!empty($paymentMethods), function ($query) use ($paymentMethods) {
                return $query->whereIn('payment_method', $paymentMethods);
            })
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->query());


        // Create removeFilter function for the view
        $removeFilter = function ($key, $value = null) {
            $query = request()->query();

            if ($value && isset($query[$key]) && is_array($query[$key])) {
                $query[$key] = array_diff($query[$key], [$value]);
                if (empty($query[$key])) {
                    unset($query[$key]);
                }
            } else {
                unset($query[$key]);
            }

            return url()->current() . '?' . http_build_query($query);
        };

        return view('admin.sales', compact(
            'sales',
            'search',
            'statuses',
            'paymentMethods',
            'startDate',
            'endDate',
            'removeFilter'
        ));
    }

    public function show($id)
    {
        $sale = Sales::with(['user', 'items.product'])->findOrFail($id);

        return response()->json($sale);
    }
}
