<x-app-layout>
    @section('title', 'Order #' . $order->order_number)

    <style>
        .admin-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .od-back {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: var(--text-mu); font-size: 0.85rem; font-weight: 500;
            text-decoration: none; margin-bottom: 1.25rem;
        }
        .od-back:hover { color: var(--cyan); }

        .od-grid { display: grid; grid-template-columns: 1fr 1.15fr; gap: 1.25rem; }
        @media (max-width: 1000px) { .od-grid { grid-template-columns: 1fr; } }

        .od-panel {
            border-radius: 1.1rem; background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); padding: 1.5rem; margin-bottom: 1.25rem;
        }
        .od-panel-title {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--cyan); margin-bottom: 1rem;
        }

        .od-header-row {
            display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;
            margin-bottom: 1rem; flex-wrap: wrap;
        }
        .od-order-num { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--text-hi); }
        .od-order-date { font-size: 0.78rem; color: var(--text-mu); margin-top: 0.25rem; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.35rem 0.75rem; border-radius: 999px;
            font-size: 0.72rem; font-weight: 600;
        }
        .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; box-shadow: 0 0 8px currentColor; }

        .od-field {
            display: flex; justify-content: space-between; padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.85rem;
        }
        .od-field:last-child { border-bottom: none; }
        .od-field-label { color: var(--text-mu); font-size: 0.78rem; }
        .od-field-value { color: var(--text-hi); font-weight: 500; text-align: right; }

        .code-box {
            text-align: center; padding: 1.25rem 1rem; border-radius: 0.9rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 2px dashed rgba(41,231,255,0.4); margin-bottom: 1rem;
        }
        .code-label {
            font-size: 0.68rem; letter-spacing: 0.18em; text-transform: uppercase;
            color: var(--cyan); font-weight: 700; margin-bottom: 0.6rem;
        }
        .code-value {
            font-family: 'JetBrains Mono', monospace; font-size: 1.9rem; font-weight: 700;
            letter-spacing: 0.4em; color: var(--text-hi); line-height: 1;
        }
        .code-note { font-size: 0.72rem; color: var(--text-mu); margin-top: 0.5rem; }
        .code-verified { color: #6ee7b7; font-weight: 700; padding: 0.5rem 0; font-size: 0.9rem; }
        .code-expired { color: #f87171; font-weight: 600; }

        /* Form inputs */
        .a-input, .a-select, .a-textarea {
            width: 100%; background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09); border-radius: 0.6rem;
            padding: 0.6rem 0.85rem; font-size: 0.85rem; color: var(--text-hi);
            outline: none; font-family: inherit;
        }
        .a-input:focus, .a-select:focus, .a-textarea:focus { border-color: rgba(41,231,255,0.5); }
        .a-select option { background: #111827; }
        .a-textarea { resize: vertical; min-height: 60px; }

        .a-label {
            display: block; font-size: 0.75rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;
        }

        .a-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem;
            padding: 0.65rem 1.15rem; border-radius: 0.65rem;
            font-weight: 600; font-size: 0.82rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            border: none; cursor: pointer; transition: filter 0.15s;
        }
        .a-btn:hover { filter: brightness(1.08); }
        .a-btn.danger {
            background: rgba(239,68,68,0.15); color: #f87171;
            border: 1px solid rgba(239,68,68,0.3);
        }
        .a-btn.danger:hover { background: rgba(239,68,68,0.25); }
        .a-btn.warning {
            background: rgba(251,191,36,0.15); color: #fbbf24;
            border: 1px solid rgba(251,191,36,0.3);
        }
        .a-btn.warning:hover { background: rgba(251,191,36,0.25); }

        /* Timeline */
        .timeline { position: relative; padding-left: 1.75rem; }
        .timeline::before {
            content: ''; position: absolute; left: 8px; top: 8px; bottom: 8px;
            width: 2px; background: linear-gradient(180deg, var(--cyan), rgba(41,231,255,0.1));
        }
        .tl-item { position: relative; padding-bottom: 1.1rem; }
        .tl-item:last-child { padding-bottom: 0; }
        .tl-item::before {
            content: ''; position: absolute; left: -1.75rem; top: 4px;
            width: 18px; height: 18px; border-radius: 50%;
            background: #0b0f19; border: 2px solid var(--cyan);
            box-shadow: 0 0 12px var(--cyan);
        }
        .tl-status { font-size: 0.85rem; font-weight: 600; color: var(--text-hi); text-transform: capitalize; margin-bottom: 0.15rem; }
        .tl-time { font-size: 0.72rem; color: var(--text-mu); }
        .tl-note { font-size: 0.72rem; color: var(--text-mu); font-style: italic; margin-top: 0.2rem; }
    </style>

    <div class="admin-page max-w-7xl mx-auto">

        <a href="{{ route('admin.orders.index') }}" class="od-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to orders
        </a>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
        @endif

        @php $badge = $order->statusBadge(); @endphp

        <div class="od-grid">

            {{-- ══ LEFT column ══ --}}
            <div>

                {{-- Header --}}
                <div class="od-panel">
                    <div class="od-header-row">
                        <div>
                            <div class="od-order-num">#{{ $order->order_number }}</div>
                            <div class="od-order-date">{{ $order->created_at->format('M d, Y · h:i A') }}</div>
                        </div>
                        <span class="status-badge" style="color: {{ $badge['color'] }}; background: {{ $badge['color'] }}1a; border: 1px solid {{ $badge['color'] }}44;">
                            {{ $badge['label'] }}
                        </span>
                    </div>

                    {{-- Delivery code --}}
                    @if (! $order->isDeliveryCodeUsed())
                        <div class="code-box">
                            <div class="code-label">Delivery Code</div>
                            @if ($order->isDeliveryCodeExpired())
                                <div class="code-expired">Expired</div>
                            @else
                                <div class="code-value">{{ $order->delivery_code }}</div>
                                <div class="code-note">Attempts: {{ $order->delivery_code_attempts }}/3</div>
                            @endif
                        </div>
                    @else
                        <div class="code-box">
                            <div class="code-label">Delivery Code</div>
                            <div class="code-verified">✓ Verified {{ $order->delivery_code_used_at->format('M d, Y · h:i A') }}</div>
                        </div>
                    @endif
                </div>

                {{-- Customer info --}}
                <div class="od-panel">
                    <div class="od-panel-title">Customer</div>
                    <div class="od-field">
                        <span class="od-field-label">Name</span>
                        <span class="od-field-value">{{ $order->user->name }}</span>
                    </div>
                    <div class="od-field">
                        <span class="od-field-label">Email</span>
                        <span class="od-field-value">{{ $order->user->email }}</span>
                    </div>
                </div>

                {{-- Delivery info --}}
                <div class="od-panel">
                    <div class="od-panel-title">{{ $order->isPickup() ? 'Pickup' : 'Delivery Address' }}</div>
                    <div class="od-field">
                        <span class="od-field-label">Name</span>
                        <span class="od-field-value">{{ $order->delivery_name }}</span>
                    </div>
                    <div class="od-field">
                        <span class="od-field-label">Phone</span>
                        <span class="od-field-value">{{ $order->delivery_phone }}</span>
                    </div>
                    @if (! $order->isPickup())
                        <div class="od-field">
                            <span class="od-field-label">Address</span>
                            <span class="od-field-value">{{ $order->delivery_address }}</span>
                        </div>
                        <div class="od-field">
                            <span class="od-field-label">City</span>
                            <span class="od-field-value">{{ $order->delivery_city ?? '—' }}</span>
                        </div>
                        @if ($order->delivery_postal)
                            <div class="od-field">
                                <span class="od-field-label">Postal</span>
                                <span class="od-field-value">{{ $order->delivery_postal }}</span>
                            </div>
                        @endif
                    @endif
                    @if ($order->delivery_note)
                        <div class="od-field">
                            <span class="od-field-label">Note</span>
                            <span class="od-field-value">{{ $order->delivery_note }}</span>
                        </div>
                    @endif
                </div>

                {{-- Order items --}}
                <div class="od-panel">
                    <div class="od-panel-title">Order Items</div>
                    <div class="od-field">
                        <span class="od-field-label">{{ $order->product_name }} × {{ $order->quantity }}</span>
                        <span class="od-field-value">৳{{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    @if ((float) $order->discount_amount > 0)
                        <div class="od-field" style="color:#6ee7b7;">
                            <span class="od-field-label">Discount {{ $order->coupon_code ? '('.$order->coupon_code.')' : '' }}</span>
                            <span class="od-field-value" style="color:#6ee7b7;">−৳{{ number_format($order->discount_amount, 0) }}</span>
                        </div>
                    @endif
                    @if ((float) $order->delivery_charge > 0)
                        <div class="od-field">
                            <span class="od-field-label">Delivery Charge</span>
                            <span class="od-field-value">৳{{ number_format($order->delivery_charge, 0) }}</span>
                        </div>
                    @endif
                    <div class="od-field" style="border-top:1px dashed var(--glass-border); margin-top:0.4rem; padding-top:0.75rem;">
                        <span class="od-field-label" style="font-weight:700; color:var(--text-hi); font-size:0.88rem;">Total</span>
                        <span class="od-field-value" style="font-family:'Sora'; font-size:1.15rem; color:var(--cyan); font-weight:700;">
                            ৳{{ number_format($order->total_amount, 0) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT column ══ --}}
            <div>

                {{-- Update Status --}}
                <div class="od-panel">
                    <div class="od-panel-title">Update Status</div>

                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf @method('PATCH')

                        <div style="margin-bottom:0.9rem;">
                            <label class="a-label">New Status</label>
                            <select name="status" class="a-select" required>
                                <option value="">— Select status —</option>
                                @if ($order->isPickup())
                                    <option value="processing">Processing</option>
                                    <option value="ready_for_pickup">Ready for Pickup</option>
                                    <option value="picked_up">Picked Up</option>
                                    <option value="cancelled">Cancelled</option>
                                @else
                                    <option value="processing">Processing</option>
                                    <option value="out_for_delivery">Out for Delivery</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="returned">Returned</option>
                                @endif
                            </select>
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label class="a-label">Note (optional)</label>
                            <textarea name="note" rows="2" class="a-textarea" placeholder="Internal note..."></textarea>
                        </div>

                        <button type="submit" class="a-btn" style="width:100%;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Status
                        </button>
                    </form>
                </div>

                {{-- Assign Delivery --}}
                @if (! $order->isPickup())
                    <div class="od-panel">
                        <div class="od-panel-title">Delivery Assignment</div>

                        <form method="POST" action="{{ route('admin.orders.assign', $order) }}">
                            @csrf @method('PATCH')

                            <div style="margin-bottom:0.9rem;">
                                <label class="a-label">Delivery Method</label>
                                <select name="delivery_method" class="a-select" id="deliveryMethod" required>
                                    <option value="self" {{ $order->delivery_method === 'self' ? 'selected' : '' }}>Self — Our Delivery Man</option>
                                    <option value="vendor" {{ $order->delivery_method === 'vendor' ? 'selected' : '' }}>Vendor — Third Party</option>
                                </select>
                            </div>

                            <div style="margin-bottom:0.9rem;" id="deliveryManField">
                                <label class="a-label">Assign Delivery Man</label>
                                <select name="assigned_to" class="a-select">
                                    <option value="">— Select delivery man —</option>
                                    @foreach ($deliveryMen as $dm)
                                        <option value="{{ $dm->id }}" {{ $order->assigned_to == $dm->id ? 'selected' : '' }}>
                                            {{ $dm->name }} ({{ $dm->phone ?? $dm->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="margin-bottom:0.9rem; display:none;" id="vendorField">
                                <label class="a-label">Assign Vendor</label>
                                <select name="vendor_id" class="a-select">
                                    <option value="">— Select vendor —</option>
                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->id }}" {{ $order->vendor_id == $v->id ? 'selected' : '' }}>
                                            {{ $v->name }} ({{ $v->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="margin-bottom:1rem;">
                                <label class="a-label">Tracking Number (optional)</label>
                                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}"
                                       class="a-input" placeholder="e.g. Pathao-123456">
                            </div>

                            <button type="submit" class="a-btn" style="width:100%;">
                                Save Assignment
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Timeline --}}
                <div class="od-panel">
                    <div class="od-panel-title">Timeline</div>
                    <div class="timeline">
                        @forelse ($order->statusLogs as $log)
                            <div class="tl-item">
                                <div class="tl-status">{{ ucfirst(str_replace('_', ' ', $log->status)) }}</div>
                                <div class="tl-time">{{ $log->created_at->format('M d, Y · h:i A') }}</div>
                                @if ($log->note)
                                    <div class="tl-note">"{{ $log->note }}"</div>
                                @endif
                                @if ($log->user)
                                    <div class="tl-time" style="margin-top:0.15rem;">by {{ $log->user->name }}</div>
                                @endif
                            </div>
                        @empty
                            <p style="color:var(--text-mu); font-size:0.82rem;">No activity yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Danger zone --}}
                @if (! in_array($order->status, ['delivered', 'picked_up', 'cancelled', 'returned']))
                    <div class="od-panel" style="border-color: rgba(239,68,68,0.25);">
                        <div class="od-panel-title" style="color:#f87171;">Danger Zone</div>

                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
                              onsubmit="return confirm('Cancel this order? Stock will be returned.');"
                              style="margin-bottom:0.5rem;">
                            @csrf
                            <input type="hidden" name="note" value="Cancelled by admin">
                            <button type="submit" class="a-btn danger" style="width:100%;">Cancel Order</button>
                        </form>

                        @if (! $order->isPickup() && $order->status === 'out_for_delivery')
                            <form method="POST" action="{{ route('admin.orders.return', $order) }}"
                                  onsubmit="return confirm('Mark as returned?');">
                                @csrf
                                <input type="hidden" name="note" value="Customer did not receive">
                                <button type="submit" class="a-btn warning" style="width:100%;">Mark as Returned</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const dm = document.getElementById('deliveryMethod');
        const dmField = document.getElementById('deliveryManField');
        const vField = document.getElementById('vendorField');

        function toggleAssignFields() {
            if (! dm) return;
            if (dm.value === 'self') {
                dmField.style.display = '';
                vField.style.display = 'none';
            } else {
                dmField.style.display = 'none';
                vField.style.display = '';
            }
        }

        dm?.addEventListener('change', toggleAssignFields);
        toggleAssignFields();
    </script>
</x-app-layout>