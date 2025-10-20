<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BillsController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $bills = $user->bills()
            ->orderBy('due_date')
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'name' => $bill->name,
                    'amount' => (float) $bill->amount,
                    'due_date' => $bill->due_date->format('Y-m-d'),
                ];
            });

        return Inertia::render('bills/index', [
            'bills' => $bills,
        ]);
    }

    public function store(StoreBillRequest $request): RedirectResponse
    {
        auth()->user()->bills()->create($request->validated());

        return back();
    }

    public function update(UpdateBillRequest $request, Bill $bill): RedirectResponse
    {
        if ($bill->user_id !== auth()->id()) {
            abort(403);
        }

        $bill->update($request->validated());

        return back();
    }

    public function destroy(Bill $bill): RedirectResponse
    {
        if ($bill->user_id !== auth()->id()) {
            abort(403);
        }

        $bill->delete();

        return back();
    }
}
