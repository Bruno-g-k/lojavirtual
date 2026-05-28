<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Type;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function apiIndex(){
        return response()->json(Product::all());
    }

    public function reportPdf(Request $request)
    {
        $products = DB::table('products')
            ->join('types', 'products.type_id', '=', 'types.id')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.quantity',
                'products.price',
                //'products.image',
                //'products.type_id',
                'types.name as type_name',
                'products.created_at',
                'products.updated_at'
            )
            ->when($request->name, fn($query, $name) => $query->where('products.name', 'like', "%{$name}%"))
            ->when($request->type_id, fn($query, $typeId) => $query->where('products.type_id', $typeId))
            ->when($request->min_quantity, fn($query, $quantity) => $query->where('products.quantity', '>=', $quantity))
            ->when($request->max_quantity, fn($query, $quantity) => $query->where('products.quantity', '<=', $quantity))
            ->orderBy('products.name')
            ->get();

        return Pdf::loadView('products.report-pdf', compact('products'))
            ->download('relatorio-produtos.pdf');
    }

    public function report()
    {
        return view('products.report', [
            'types' => Type::orderBy('name')->get()
        ]);
    }
    //
    public function create()
    {
        return view('products.create', ['types' => Type::all()]);
    }

    //função chamada no submit do form..
    //será um POST com os dados
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
            'quantity' => 'required|gt:0',
            'price' => 'required|gt:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        //não esquecer import do Product model.
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        try {
            Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'image' => $imagePath,
                'type_id' => $request->type_id
            ]);
            return redirect('/products')->with('success', 'Produto criado com sucesso');
        } catch (\Exception $e) {
            //storage/logs/laravel.log
            Log::error('erro ao salvar produto', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Não foi possível salvar o produto.')
                ->withInput();
        }
    }

    //função que irá mostrar a view de listagem
    //passando como parâmetro a consulta no banco com ::all()
    public function index()
    {
        $products = Product::with('type')->paginate(10);
        return view('products.index', [
            'products' => $products
        ]);
    }

    public function edit($id)
    {
        //find é o método que faz select * from products where id= ?
        $product = Product::find($id);
        //retornamos a view passando a TUPLA de produto consultado
        return view('products.edit', ['product' => $product, 'types' => Type::all()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
            'quantity' => 'required|gt:0',
            'price' => 'required|gt:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);
        $product = Product::find($request->id);
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'image' => $imagePath,
            'type_id' => $request->type_id
        ]);
        return redirect('/products')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy($id)
    {
        //select * from product where id = ?
        $product = Product::find($id);
        //deleta o produto no banco
        $product->delete();
        return redirect('/products')->with('success', 'Produto excluído com sucesso!');
    }
}
