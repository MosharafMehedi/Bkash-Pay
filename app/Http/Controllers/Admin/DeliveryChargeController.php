<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCharge;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryCharge::query();

        if ($q = $request->query('q')) {
            $query->where('city', 'like', "%{$q}%");
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $charges = $query->orderBy('city')->paginate(20)->withQueryString();

        return view('admin.delivery-charges.index', compact('charges'));
    }

    public function create()
    {
        return view('admin.delivery-charges.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'city'           => 'required|string|max:100|unique:delivery_charges,city',
            'charge'         => 'required|numeric|min:0',
            'free_above'     => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0|max:30',
            'is_active'      => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        DeliveryCharge::create($data);

        return redirect()->route('admin.delivery-charges.index')
            ->with('success', 'Delivery charge added.');
    }

    public function edit(DeliveryCharge $deliveryCharge)
    {
        return view('admin.delivery-charges.edit', compact('deliveryCharge'));
    }

    public function update(Request $request, DeliveryCharge $deliveryCharge)
    {
        $data = $request->validate([
            'city'           => ['required', 'string', 'max:100',
                                 Rule::unique('delivery_charges', 'city')->ignore($deliveryCharge->id)],
            'charge'         => 'required|numeric|min:0',
            'free_above'     => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0|max:30',
            'is_active'      => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $deliveryCharge->update($data);

        return redirect()->route('admin.delivery-charges.index')
            ->with('success', 'Delivery charge updated.');
    }

    public function destroy(DeliveryCharge $deliveryCharge)
    {
        $deliveryCharge->delete();

        return back()->with('success', 'Delivery charge removed.');
    }

    public function toggle(DeliveryCharge $deliveryCharge)
    {
        $deliveryCharge->update(['is_active' => ! $deliveryCharge->is_active]);

        return back()->with('success', 'Status updated.');
    }
}