<?php

namespace App\Http\Controllers;

use App\Models\SalesItem;
use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\SaleItems;
use App\Models\Transactions;
use App\Models\Logs;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
   public function index(Request $request)
{
    $currentUser = Auth::user();
    $search = $request->get('search');
    $statuses = $request->get('status', []);
    $paymentMethods = $request->get('payment_method', []);
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Determine branch filter
    $branchId = null;
    if ($currentUser->role === 'super_admin') {
        $branchId = session('selected_branch_id');
    } else {
        $branchId = $currentUser->branch_id;
    }

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
        // **ADD BRANCH FILTER HERE**
        ->when($branchId, function ($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
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
        // Load sales with user, items, and both product and plan relationships
        $sale = Sales::with([
            'user', 
            'items.product',
            'items.plan'
        ])->findOrFail($id);

        return response()->json($sale);
    }

    public function destroy($id)
    {
        try {
            $currentUser = Auth::user();

                 $branchId = $currentUser->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $currentUser->branch_id;
            
            // Only admin can delete sales
            if ($currentUser->role !== 'admin') {
                return redirect()->back()
                    ->with('error', 'Only administrators can delete sales.');
            }

            DB::beginTransaction();

            $sale = Sales::with(['items', 'user'])->findOrFail($id);
            
            // Store sale details for logging
            $saleId = $sale->sales_id;
            $totalAmount = $sale->total_amount;
            $cashierName = $sale->user->first_name . ' ' . $sale->user->last_name;
            $itemCount = $sale->items->count();
            
            // Delete related transactions first
            Transactions::where('sales_id', $saleId)->delete();
            
            // Delete sale items
            SalesItem::where('sales_id', $saleId)->delete();
            
            // Delete the sale
            $sale->delete();

            // Log the deletion with details
            Logs::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $branchId,
                'action' => "{$currentUser->last_name} deleted sale #{$saleId} - Amount: ₱" . number_format($totalAmount, 2) . ", Items: {$itemCount}, Cashier: {$cashierName}",
                'timestamp' => now(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Sale deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Error deleting sale", [
                'sale_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }
}