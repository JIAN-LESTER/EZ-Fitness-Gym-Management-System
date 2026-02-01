<?php

// ============================================
// TRANSACTION CONTROLLER (App/Http/Controllers/TransactionController.php)
// ============================================

namespace App\Http\Controllers;

use App\Models\Logs;
use Auth;
use Illuminate\Http\Request;
use App\Models\Transactions;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
  public function index(Request $request)
{
    $currentUser = Auth::user();
    $search = $request->get('search');
    $statuses = $request->get('status', []);

    // Determine branch filter
    $branchId = null;
    if ($currentUser->role === 'super_admin') {
        $branchId = session('selected_branch_id');
    } else {
        $branchId = $currentUser->branch_id;
    }

    $transactions = Transactions::query()
        ->with(['sale.user.member.plan', 'sale.items.product', 'performer'])
        ->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('sale', function ($q2) use ($search) {
                        $q2->whereHas('user', function ($q3) use ($search) {
                            $q3->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                    })
                    ->orWhereHas('performer', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        })
        ->when(!empty($statuses), function ($query) use ($statuses) {
            return $query->whereIn('type', $statuses);
        })
        // **ADD BRANCH FILTER HERE**
        ->when($branchId, function ($query) use ($branchId) {
            return $query->whereHas('sale', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(12)
        ->appends($request->query());

    return view('admin.transactions', compact('transactions', 'search', 'statuses'));
}

    public function show($id)
    {
        try {
            // Use where() instead of find() - same as Sales controller
            $transaction = Transactions::where('transaction_id', $id)
                ->with(['sale.user.member.plan', 'sale.items.product', 'performer'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'error' => 'Transaction not found'
                ], 404);
            }

            return response()->json($transaction);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $currentUser = Auth::user();
                         $branchId = $currentUser->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $currentUser->branch_id;
            
            // Only admin can delete transactions
            if ($currentUser->role !== 'admin') {
                return redirect()->back()
                    ->with('error', 'Only administrators can delete transactions.');
            }

            $transaction = Transactions::where('transaction_id', $id)->firstOrFail();
            
            // Store transaction details for logging
            $transactionType = $transaction->type;
            $transactionId = $transaction->transaction_id;
            
            // Get related information before deletion
            $relatedInfo = '';
            if ($transaction->sale && $transaction->type === 'memberships') {
                $relatedInfo = " (Membership - " . $transaction->sale->user->first_name . " " . $transaction->sale->user->last_name . ")";
            } elseif ($transaction->sale) {
                $relatedInfo = " (Sale #" . $transaction->sale->sales_id . ")";
            } elseif ($transaction->product) {
                $relatedInfo = " (Product: " . $transaction->product->name . ")";
            }

            // Delete the transaction
            $transaction->delete();

            // Log the deletion
            Logs::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $branchId,
                'action' => "{$currentUser->last_name} deleted transaction #{$transactionId} - Type: {$transactionType}{$relatedInfo}",
                'timestamp' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Transaction deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting transaction", [
                'transaction_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error deleting transaction: ' . $e->getMessage());
        }
    }
}