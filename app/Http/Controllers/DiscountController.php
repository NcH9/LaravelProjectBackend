<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountDeleteRequest;
use App\Http\Requests\DiscountSaveRequest;
use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DiscountController extends Controller
{
    public function __construct(
        private DiscountService $discountService
    ) {}
    public function index()
    {
        if (Gate::denies('is-manager-or-admin')) {
            abort(403);
        }

        $discounts = $this->discountService->getAll();
        return response()->json([
            'data' => $discounts,
        ]);
    }
    public function save(DiscountSaveRequest $request)
    {
        if (Gate::denies('is-manager-or-admin')) {
            abort(403);
        }

        if ($request->filled('discount_id')) {
            $this->discountService->update($request);

            return response()->json('updated');

        } else {
            $this->discountService->create($request);

            return response()->json('created');
        }
    }
    public function delete(DiscountDeleteRequest $request)
    {
        if (Gate::denies('is-manager-or-admin')) {
            abort(403);
        }

        $this->discountService->delete($request['discount_id']);

        return response()->json('deleted');
    }
}
