<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // If user is a customer, show only their purchases
        if ($user->isCustomer()) {
            $purchases = $user->purchases()->with('product')->latest()->get();
        } 
        // If user is an employee, show all purchases
        else {
            $purchases = Purchase::with(['user', 'product'])->latest()->get();
        }
        
        return view('purchases.index', compact('purchases'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        $user = Auth::user();
        
        // Check if user is authorized to view this purchase
        if ($user->isCustomer() && $purchase->user_id !== $user->id) {
            return redirect()->route('purchases_index')
                ->with('error', 'You are not authorized to view this purchase.');
        }
        
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Update the quantity of a purchase.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function updateQuantity(Request $request, Purchase $purchase)
    {
        $user = Auth::user();
        
        // Check if user is authorized to update this purchase
        if ($user->isCustomer() && $purchase->user_id !== $user->id) {
            return redirect()->route('purchases_index')
                ->with('error', 'You are not authorized to update this purchase.');
        }
        
        // Validate the request
        $request->validate([
            'new_quantity' => 'required|integer|min:1',
        ]);
        
        // Get the new quantity
        $newQuantity = $request->new_quantity;
        
        // Determine if we're adding or removing items
        $quantityDifference = $newQuantity - $purchase->quantity;
        
        // If no change, just return
        if ($quantityDifference == 0) {
            return redirect()->back()->with('info', 'No change in quantity.');
        }
        
        // Get the product
        $product = Product::find($purchase->product_id);
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            if ($quantityDifference > 0) {
                // Adding items
                
                if (!$product) {
                    return redirect()->back()->with('error', 'Product not found.');
                }
                
                // Check if product has enough stock
                if ($product->stock < $quantityDifference) {
                    return redirect()->back()->with('error', 'Not enough stock available. Only ' . $product->stock . ' units left.');
                }
                
                // Calculate additional cost
                $additionalCost = $purchase->price * $quantityDifference;
                
                // Check if user has enough credit
                if (!$user->hasEnoughCredit($additionalCost)) {
                    return redirect()->back()->with('error', 'You do not have enough credit to add these items.');
                }
                
                // Deduct credit from user
                $user->deductCredit($additionalCost);
                
                // Reduce product stock
                $product->stock -= $quantityDifference;
                $product->save();
                
                $message = 'Added ' . $quantityDifference . ' item(s) to your purchase.';
            } else {
                // Removing items
                $quantityToRemove = abs($quantityDifference);
                
                // Calculate refund amount
                $refundAmount = $purchase->price * $quantityToRemove;
                
                // Refund credit to user
                $user->addCredit($refundAmount);
                
                // Restore product stock if product exists
                if ($product) {
                    $product->stock += $quantityToRemove;
                    $product->save();
                }
                
                $message = 'Removed ' . $quantityToRemove . ' item(s) from your purchase and refunded $' . number_format($refundAmount, 2) . '.';
            }
            
            // Update purchase
            $purchase->quantity = $newQuantity;
            $purchase->total = $purchase->price * $newQuantity;
            $purchase->save();
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('purchases_show', $purchase->id)
                ->with('success', $message);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        $user = Auth::user();
        
        // Check if user is authorized to delete this purchase
        if ($user->isCustomer() && $purchase->user_id !== $user->id) {
            return redirect()->route('purchases_index')
                ->with('error', 'You are not authorized to delete this purchase.');
        }
        
        // Get the product and quantity to restore stock
        $product = Product::find($purchase->product_id);
        $quantity = $purchase->quantity;
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Restore credit to user
            $user->addCredit($purchase->total);
            
            // Restore stock to product
            if ($product) {
                $product->stock += $quantity;
                $product->save();
            }
            
            // Delete the purchase
            $purchase->delete();
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('purchases_index')
                ->with('success', 'Purchase deleted successfully and credit restored.');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return redirect()->route('purchases_index')
                ->with('error', 'An error occurred while deleting the purchase: ' . $e->getMessage());
        }
    }
}
