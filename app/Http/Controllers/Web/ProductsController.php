<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['home']);
    }

    /**
     * Display the home page with featured products.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        // // Get featured products (random selection)
        // $featuredProducts = Product::inRandomOrder()->take(4)->get();
        
        // // Get latest products
        // $newProducts = Product::latest()->take(8)->get();
        
        // // Get product categories (models)
        // $categories = Product::select('model')->distinct()->get()->pluck('model');
        
        // // Get best selling products
        // $bestSellers = Product::withCount('purchases')
        //                     ->orderBy('purchases_count', 'desc')
        //                     ->take(4)
        //                     ->get();
        
        return view('products.home');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return view('products.list', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        $request->validate([
            'code' => 'required|string|max:64|unique:products',
            'name' => 'required|string|max:256',
            'price' => 'required|numeric|min:0',
            'model' => 'required|string|max:128',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
        ]);

        $product = new Product();
        $product->code = $request->code;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->model = $request->model;
        $product->description = $request->description;
        $product->stock = $request->stock;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $product->photo = $filename;
        }

        $product->save();

        return redirect()->route('products_index')
            ->with('success', 'Product created successfully.');
    }

    public function addstock(Request $request)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        $request->validate([
            'code' => 'required|string|max:64|unique:products',
            'stock' => 'required|integer|min:0',
        ]);



        $product->save(); $product = Product::where('code', $request->code)->first();
        $product->addstock($request->stock);
    
        return redirect()->route('products_index')
            ->with('success', 'Product stock updated successfully.');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        try {
            // Log the request data for debugging
            Log::info('Product update request data:', $request->all());
            Log::info('Current product being edited:', ['id' => $product->id, 'code' => $product->code]);
            
            // Check if the code is being changed
            $codeChanged = $request->code !== $product->code;
            
            // If code is changed, check if it's unique
            if ($codeChanged) {
                $codeExists = Product::where('code', $request->code)
                    ->where('id', '!=', $product->id)
                    ->exists();
                
                if ($codeExists) {
                    return back()->withErrors(['code' => 'The code has already been taken.'])
                        ->withInput();
                }
            }
            
            // Validate other fields
            $validated = $request->validate([
                'name' => 'required|string|max:256',
                'price' => 'required|numeric|min:0',
                'model' => 'required|string|max:128',
                'description' => 'nullable|string',
                'stock' => 'required|integer|min:0',
            ]);
            
            // Update product attributes
            $product->code = $request->code;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->model = $request->model;
            $product->description = $request->description;
            $product->stock = $request->stock;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $filename);
                $product->photo = $filename;
            }

            // Save the product
            $saved = $product->save();
            
            if (!$saved) {
                Log::error('Failed to save product:', ['product' => $product]);
                return back()->with('error', 'Failed to update product. Please try again.');
            }

            return redirect()->route('products_index')
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            // Log any exceptions
            Log::error('Exception during product update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Check if user has Employee role
        if (!Auth::user()->hasRole('Employee') && !Auth::user()->hasRole('Admin')) {
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
        
        $product->delete();

        return redirect()->route('products_index')
            ->with('success', 'Product deleted successfully.');
    }


    

    /**
     * Purchase a product
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     * 
     * 
     * 
     */



    public function purchase(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $quantity = $request->quantity;
        $totalPrice = $product->price * $quantity;

        // Check if user is a customer
        if (!$user->hasRole('Customer')) {
            return redirect()->back()->with('error', 'Only customers can purchase products.');
        }

        // Check if product is in stock
        if (!$product->isInStock() || $product->stock < $quantity) {
            return redirect()->back()->with('error', 'Product is out of stock or not enough quantity available.');
        }

        // Check if user has enough credit
        if (!$user->hasEnoughCredit($totalPrice)) {
            return redirect()->back()->with('error', 'You do not have enough credit to purchase this product.');
        }



        // Create purchase
        $purchase = new Purchase();
        $purchase->user_id = $user->id;
        $purchase->product_id = $product->id;
        $purchase->quantity = $quantity;
        $purchase->price = $product->price;
        $purchase->total = $totalPrice;
        $purchase->save();

        // Deduct credit from user
        $user->deductCredit($totalPrice);

        // Reduce product stock
        $product->reduceStock($quantity);

        return redirect()->route('purchases_index')
            ->with('success', 'Product purchased successfully.');
    }
}
