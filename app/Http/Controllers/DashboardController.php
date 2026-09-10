<?php

namespace App\Http\Controllers;

use App\Models\BkashTransaction;
use App\Models\PayPalTransaction;
use App\Models\Product;
use App\Models\SslCommerzTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Fixed sandbox exchange rate, just to combine PayPal (USD) amounts
    // with bKash/SSLCommerz (BDT) amounts into one BDT total for the
    // dashboard cards/charts. Not a real-time rate.
    private const USD_TO_BDT = 110;

    /**
     * Product grid + payment analytics shown on the dashboard.
     */
    public function index()
    {
        $products = Product::orderBy('id')->get();

        // Pull every *successful* transaction from all three gateways into
        // one flat collection of ['date' => Carbon, 'amount_bdt' => float, 'gateway' => string]
        $successful = collect();

        foreach (BkashTransaction::where('status', 'success')->get() as $trx) {
            $successful->push([
                'date'       => $trx->updated_at,
                'amount_bdt' => (float) $trx->amount,
                'gateway'    => 'bKash',
            ]);
        }

        foreach (SslCommerzTransaction::where('status', 'success')->get() as $trx) {
            $successful->push([
                'date'       => $trx->updated_at,
                'amount_bdt' => (float) $trx->amount,
                'gateway'    => 'SSLCommerz',
            ]);
        }

        foreach (PayPalTransaction::where('status', 'success')->get() as $trx) {
            $successful->push([
                'date'       => $trx->updated_at,
                'amount_bdt' => (float) $trx->amount * self::USD_TO_BDT,
                'gateway'    => 'PayPal',
            ]);
        }

        $today          = Carbon::today();
        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd   = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $todayTotal = $successful
            ->filter(fn ($t) => $t['date']->isSameDay($today))
            ->sum('amount_bdt');

        $lastMonthTotal = $successful
            ->filter(fn ($t) => $t['date']->between($lastMonthStart, $lastMonthEnd))
            ->sum('amount_bdt');

        $grandTotal = $successful->sum('amount_bdt');

        // Last 10 days, oldest first — for the bar chart
        $last10Days = collect(range(9, 0))->map(function ($daysAgo) use ($successful) {
            $day = Carbon::today()->subDays($daysAgo);

            return [
                'label'  => $day->format('d M'),
                'amount' => $successful->filter(fn ($t) => $t['date']->isSameDay($day))->sum('amount_bdt'),
            ];
        });

        // Per-gateway totals — for the pie chart
        $gatewayTotals = [
            'bKash'      => $successful->where('gateway', 'bKash')->sum('amount_bdt'),
            'PayPal'     => $successful->where('gateway', 'PayPal')->sum('amount_bdt'),
            'SSLCommerz' => $successful->where('gateway', 'SSLCommerz')->sum('amount_bdt'),
        ];

        // Success rate across all attempts (any status), all gateways
        $totalAttempts = BkashTransaction::count() + PayPalTransaction::count() + SslCommerzTransaction::count();
        $totalSuccess  = $successful->count();
        $successRate   = $totalAttempts > 0 ? round(($totalSuccess / $totalAttempts) * 100, 1) : 0;

        return view('dashboard', [
            'products'       => $products,
            'todayTotal'     => $todayTotal,
            'lastMonthTotal' => $lastMonthTotal,
            'grandTotal'     => $grandTotal,
            'last10Days'     => $last10Days,
            'gatewayTotals'  => $gatewayTotals,
            'successRate'    => $successRate,
            'totalAttempts'  => $totalAttempts,
        ]);
    }
}