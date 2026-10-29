<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function __construct(protected AuditLogService $audit) {}

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

        try {
            $coupon = Coupon::create($data);

            $this->audit->logAction(
                "Coupon created: {$coupon->code}",
                $coupon,
                ['new' => $coupon->only(['code', 'type', 'value', 'is_active'])]
            );

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon created successfully.');
        } catch (\Throwable $e) {
            Log::error('Coupon create failed: ' . $e->getMessage());
            return back()->with('error', 'Could not create coupon.')->withInput();
        }
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validated($request);
        $data['code'] = strtoupper($data['code']);

        try {
            $coupon->update($data);

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Coupon update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update coupon.')->withInput();
        }
    }

    public function destroy(Coupon $coupon)
    {
        try {
            $code = $coupon->code;
            $coupon->delete();

            $this->audit->logAction("Coupon deleted: {$code}");

            return back()->with('success', 'Coupon deleted.');
        } catch (\Throwable $e) {
            Log::error('Coupon delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete coupon.');
        }
    }

    public function toggle(Coupon $coupon)
    {
        try {
            $coupon->update(['is_active' => ! $coupon->is_active]);

            return back()->with('success', 'Status updated.');
        } catch (\Throwable $e) {
            Log::error('Coupon toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update status.');
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'code'           => 'required|string|max:50',
            'name'           => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'type'           => 'required|in:fixed,percent',
            'value'          => 'required|numeric|min:0',
            'min_order'      => 'nullable|numeric|min:0',
            'max_discount'   => 'nullable|numeric|min:0',
            'usage_limit'    => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at'      => 'nullable|date',
            'expires_at'     => 'nullable|date|after:starts_at',
            'is_active'      => 'boolean',
        ]);
    }
}