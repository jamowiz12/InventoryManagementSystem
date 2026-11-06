<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function products(): JsonResponse
    {
        return $this->success(Product::with(['category', 'supplier'])->orderBy('id')->get());
    }

    public function product(int $id): JsonResponse
    {
        $product = Product::with(['category', 'supplier'])->find($id);

        return $product ? $this->success($product) : $this->failure('Product not found.', 404);
    }

    public function createProduct(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'product_name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $result = DB::select(
                'CALL sp_add_product(?, ?, ?, ?, ?, ?, ?, CAST(NULL AS BIGINT))',
                [$data['category_id'], $data['supplier_id'], $data['product_name'], $data['description'] ?? null, $data['unit_price'], $data['stock_quantity'], $data['minimum_stock']]
            );
            $productId = $result[0]->p_product_id ?? null;
            $product = $productId ? Product::with(['category', 'supplier'])->find($productId) : null;

            return $this->success($product, 'Product created.', 201);
        } catch (QueryException $exception) {
            return $this->databaseFailure($exception);
        }
    }

    public function stockIn(Request $request): JsonResponse
    {
        return $this->moveStock($request, 'sp_stock_in');
    }

    public function stockOut(Request $request): JsonResponse
    {
        return $this->moveStock($request, 'sp_stock_out');
    }

    public function stockValue(int $id): JsonResponse
    {
        if (! Product::whereKey($id)->exists()) {
            return $this->failure('Product not found.', 404);
        }
        $value = DB::selectOne('SELECT fn_get_stock_value(?) AS stock_value', [$id]);

        return $this->success(['product_id' => $id, 'stock_value' => $value->stock_value]);
    }

    public function stockStatus(int $id): JsonResponse
    {
        if (! Product::whereKey($id)->exists()) {
            return $this->failure('Product not found.', 404);
        }
        $status = DB::selectOne('SELECT fn_get_stock_status(?) AS stock_status', [$id]);

        return $this->success(['product_id' => $id, 'stock_status' => $status->stock_status]);
    }

    public function categories(): JsonResponse
    {
        return $this->success(Category::orderBy('category_name')->get());
    }

    public function suppliers(): JsonResponse
    {
        return $this->success(Supplier::orderBy('supplier_name')->get());
    }

    public function transactions(): JsonResponse
    {
        return $this->success(StockTransaction::with('product')->orderByDesc('created_at')->orderByDesc('id')->get());
    }

    private function moveStock(Request $request, string $procedure): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($data, $procedure): void {
                DB::statement("CALL {$procedure}(?, ?, ?)", [$data['product_id'], $data['quantity'], $data['remarks'] ?? null]);
            });

            return $this->success(['product_id' => $data['product_id']], 'Stock movement recorded.');
        } catch (QueryException $exception) {
            return $this->databaseFailure($exception);
        }
    }

    private function databaseFailure(QueryException $exception): JsonResponse
    {
        $message = $exception->getMessage();
        if (str_contains($message, 'Insufficient stock')) {
            return $this->failure('Insufficient stock for this operation.', 422);
        }
        if (str_contains($message, 'does not exist')) {
            return $this->failure('The referenced product, category, or supplier does not exist.', 422);
        }

        return $this->failure('The database rejected the operation. Check the submitted values and database setup.', 422);
    }

    private function success(mixed $data, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    private function failure(string $message, int $status): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'data' => null], $status);
    }
}
