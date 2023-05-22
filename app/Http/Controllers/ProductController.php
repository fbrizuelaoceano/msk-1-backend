<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function GetProductById($id)
    {
        $product = Product::find($id);
        return response()->json([
            $product,
        ]);
    }
    public function GetProducts()
    {
        $products = Product::all();
        return response()->json([
            $products,
        ]);
    }

    // Get
    public function CreateView()
    {
        return response()->json([
            "redirect" => "vistacreate",
        ]);    
    }
    // Post
    public function Create(Request $request)
    {
        $product = new Product;
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        // ... asigna los valores para los demás campos del modelo

        $product->save();

        return response()->json(
             $product
        );
    }

    public function Edit(Request $resquest, $id)
    {
        $product = Product::find($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        // ... asigna los valores para los demás campos del modelo

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
