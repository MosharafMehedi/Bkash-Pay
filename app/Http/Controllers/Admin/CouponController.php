<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $coupons = $query->latest()->paginate(15)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['code'] = strtoupper($data['code']);

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validated($request);
        $data['code'] = strtoupper($data['code']);

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);
        return back()->with('success', 'Status updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'code'          => 'required|string|max:50',
            'name'          => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|in:fixed,percent',
            'value'         => 'required|numeric|min:0',
            'min_order'     => 'nullable|numeric|min:0',
            'max_discount'  => 'nullable|numeric|min:0',
            'usage_limit'   => 'nullable|integer|min:1',
            'per_user_limit'=> 'nullable|integer|min:1',
            'starts_at'     => 'nullable|date',
            'expires_at'    => 'nullable|date|after:starts_at',
            'is_active'     => 'boolean',
        ]);
    }
}