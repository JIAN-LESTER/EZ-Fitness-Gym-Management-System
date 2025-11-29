<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Categories;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\SalesItem;
use App\Models\Sales;
use App\Models\Transactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('product.category');
        $categories = Categories::all();


        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('category') && $request->category !== '') {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }


        $query->whereHas('product', function ($q) {
            $q->where('status', 'available');
        })->where('quantity', '>', 0);


        $products = $query->paginate(12)->withQueryString();

        return view('admin.pos', compact('products', 'categories'));
    }

    public function addToCart(Request $request)
    {
        try {
            $productId = $request->product_id;
            $user = Auth::user();


            $inventory = Inventory::where('product_id', $productId)
                ->with('product')
                ->first();

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if ($inventory->quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product out of stock'
                ], 400);
            }


            $cart = Cart::firstOrCreate([
                'user_id' => $user->user_id,
                'status' => 'active'
            ]);


            $cartItem = CartItem::where('cart_id', $cart->cart_id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {

                if ($cartItem->quantity + 1 > $inventory->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available. Only ' . $inventory->quantity . ' in stock.'
                    ], 400);
                }


                $cartItem->quantity += 1;
                $cartItem->sub_total = $cartItem->quantity * $cartItem->price;
                $cartItem->save();
            } else {

                $cartItem = CartItem::create([
                    'cart_id'    => $cart->cart_id,
                    'product_id' => $inventory->product_id,
                    'quantity'   => 1,
                    'price'      => $inventory->product->price,
                    'sub_total'   => $inventory->product->price,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart'
            ]);
        } catch (\Exception $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add to cart. Please try again.'
            ], 500);
        }
    }

    public function getCart()
    {
        try {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->user_id)
                ->where('status', 'active')
                ->with(['items.product'])
                ->first();

            if (!$cart) {
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'total' => 0
                ]);
            }

            $items = $cart->items->map(function ($item) {
                return [
                    'cart_item_id' => $item->cart_item_id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->sub_total,
                    'image' => $item->product->image
                ];
            });

            $total = $cart->items->sum('sub_total');

            return response()->json([
                'success' => true,
                'items' => $items,
                'total' => $total
            ]);
        } catch (\Exception $e) {
            Log::error('Get cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart'
            ], 500);
        }
    }

    public function updateCartItem(Request $request)
    {
        try {
            $request->validate([
                'cart_item_id' => 'required|exists:cart_items,cart_item_id',
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem = CartItem::findOrFail($request->cart_item_id);


            $inventory = Inventory::where('product_id', $cartItem->product_id)->firstOrFail();

            if ($request->quantity > $inventory->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available. Only ' . $inventory->quantity . ' available.'
                ], 400);
            }

            // Update quantity and subtotal
            $cartItem->quantity = $request->quantity;
            $cartItem->sub_total = $cartItem->quantity * $cartItem->price;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Update cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    public function removeCartItem(Request $request)
    {
        try {
            $request->validate([
                'cart_item_id' => 'required|exists:cart_items,cart_item_id'
            ]);

            $cartItem = CartItem::findOrFail($request->cart_item_id);
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
        } catch (\Exception $e) {
            Log::error('Remove cart item error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item'
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        $userId = Auth::id();
        $paymentMethod = $request->payment_method;
        $referenceCode = $request->reference_code;
        try {
            // Validate reference code for GCash payments
            if ($paymentMethod === 'gcash' && empty($referenceCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'GCash reference code is required'
                ], 400);
            }

            DB::transaction(function () use ($paymentMethod, $referenceCode, $userId) {

                $cart = Cart::where('user_id', $userId)
                    ->where('status', 'active')
                    ->with('items')
                    ->firstOrFail();

                $cartItems = $cart->items;

                if ($cartItems->isEmpty()) {
                    throw new \Exception('Cart is empty.');
                }

                // Calculate totals with discount
                $finalTotal = $cartItems->sum('sub_total');

                $saleData = [
                    'user_id' => $userId,
                    'total_amount' => $finalTotal,
                    'tax' => 0,
                    'discount' => 0,
                    'payment_method' => $paymentMethod,
                    'status' => 'paid',
                    'date' => now()
                ];

                // Add reference code only for GCash payments
                if ($paymentMethod === 'gcash' && !empty($referenceCode)) {
                    $saleData['reference_code'] = $referenceCode;
                }

                $sale = Sales::create($saleData);

                foreach ($cartItems as $item) {
                    $inventory = Inventory::where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$inventory || $inventory->quantity < $item->quantity) {
                        throw new \Exception("Not enough stock for product ID: {$item->product_id}");
                    }

                    // Deduct inventory
                    $inventory->decrement('quantity', $item->quantity);

                    // Check if quantity of the product reached 0 after checkout
                    if ($inventory->fresh()->quantity == 0) {
                        // Update product status to unavailable
                        Product::where('product_id', $item->product_id)
                            ->update(['status' => 'unavailable']);
                    }


                    SalesItem::create([
                        'sales_id'   => $sale->sales_id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price' => $item->price,
                        'sub_total'  => $item->sub_total,
                    ]);
                }


                $cart->items()->delete();
                $cart->status = 'checked_out';
                $cart->save();

                if ($cart->status == 'checked_out') {
                    $cart->delete();
                }
                $currentUser = Auth::user();

                Transactions::create([
                    'sales_id' => $sale->sales_id,
                    'type' => 'sales',
                    'timestamp' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Checkout completed successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
