<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index()
    {
        \Gate::authorize('view', 'products');

        $products = Product::paginate();

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        \Gate::authorize('view', 'products');

        return new ProductResource(Product::find($id));
    }

    public function store(Request $request)
    {
        \Gate::authorize('edit', 'products');

        $file = $request->file('image');
        $name = \Str::random(10);
        $url = \Storage::putFileAs('images', $file, $name . '.' . $file->extension());

        $product = Product::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => env('DOMEN') . '/' . $url,
            'price' => $request->input('price'),
        ]);

        return response($product, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        \Gate::authorize('edit', 'products');

        $product = Product::find($id);

        $product->update($request->only('title', 'description', 'image', 'price'));
    }

    public function destroy($id)
    {
        \Gate::authorize('edit', 'products');

        Product::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
