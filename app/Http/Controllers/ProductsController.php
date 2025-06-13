<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function index()
    {
        try {
            $products = Product::all();
            return view('modules.products.index', [
                'products' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar Produtos: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar a lista de Produtos.'));
        }
    }

    public function form()
    {
        try {
            $product = new Product();
            return view('modules.products.form', [
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de product: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar o formulário.'));
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::findOrFail($id);
            return view('modules.products.form', [
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao editar product com ID {$id}: " . $e->getMessage());
            return redirect()->route('products.index')->withErrors(__('product não encontrado ou ocorreu um erro.'));
        }
    }

    public function store(ProductRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $product = new Product();
            $product->fill($request->all());
            $product->save();

            Log::info("producto criado com ID {$product->id}");

            return redirect()->route('products.edit', ['id' => $product->id])
                ->with('success', __('modules.product_created'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar product: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao criar o product.'));
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->fill($request->all());
            $product->save();

            Log::info("Producto atualizado com ID {$product->id}");

            return redirect()->route('products.edit', ['id' => $product->id])
                ->with('success', __('modules.product_updated'));
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar product com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao atualizar o producto.'));
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            Log::info("product removido com ID {$id}");

            return redirect()->route('products.index')->with('success', __('modules.product_deleted'));
        } catch (\Exception $e) {
            Log::error("Erro ao eliminar product com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao eliminar o product.'));
        }
    }
}
