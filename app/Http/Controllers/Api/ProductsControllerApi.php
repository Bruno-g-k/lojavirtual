<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProductsControllerApi extends Controller
{
    public function index()
    {
        $productList = Product::all();
        return response()->json([
            "success" => true,
            "message" => "Lista de produtos",
            "data" => $productList
        ]);
    }

    public function loginapi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check(
            $request->password,
            $user->password
        )) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais são inválidas.'],
            ]);
        }
        return $user->createToken('token')->plainTextToken;
    }

        public function store(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'type_id' => 'required|integer|exists:types,id',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Produto criado com sucesso',
                'data' => $product
            ], 201);
        }

        public function update(Request $request, $id)
        {
            $product = Product::find($id);
            if (! $product) {
                return response()->json(['success' => false, 'message' => 'Produto não encontrado'], 404);
            }

            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'sometimes|required|integer|min:0',
                'price' => 'sometimes|required|numeric|min:0',
                'type_id' => 'sometimes|required|integer|exists:types,id',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Produto atualizado com sucesso',
                'data' => $product
            ]);
        }

        public function destroy($id)
        {
            $product = Product::find($id);
            if (! $product) {
                return response()->json(['success' => false, 'message' => 'Produto não encontrado'], 404);
            }

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json(['success' => true, 'message' => 'Produto excluído com sucesso']);
        }
}
