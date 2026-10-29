<x-app-layout>
    @section('title', 'Audit Log #' . $auditLog->id)

    <style>
        .ad-page { --cyan:#29e7ff; --violet:#a78bfa; --green:#34d399; --red:#f87171;
            --amber:#fbbf24; --text-hi:#f1f0fb; --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .ad-back {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: var(--text-mu); font-size: 0.85rem; font-weight: 500;
            text-decoration: none; margin-bottom: 1.25rem;
        }
        .ad-back:hover { color: var(--cyan); }

        .ad-panel {
            border-radius: 1.1rem; background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); padding: 1.5rem; margin-bottom: 1.25rem;
        }
        .ad-panel-title {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--cyan); margin-bottom: 1rem;
        }

        .ad-field {
            display: flex; justify-content: space-between; padding: 0.55rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.85rem;
        }
        .ad-field:last-child { border-bottom: none; }
        .ad-field-label { color: var(--text-mu); font-size: 0.78rem; }
        .ad-field-value { color: var(--text-hi); font-weight: 500; text-align: right; max-width: 60%; word-break: break-word; }

        .ad-event {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.5rem 1rem; border-radius: 999px;
            font-size: 0.82rem; font-weight: 700;
        }
        .ad-event::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: currentColor; }

        .ad-diff {
            border-radius: 0.85rem; overflow: hidden;
            border: 1px solid var(--glass-border); background: rgba(11,15,25,0.6);
        }
        .ad-diff-row {
            display: grid; grid-template-columns: 160px 1fr 1fr;
            gap: 0.75rem; padding: 0.65rem 0.85rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            font-size: 0.8rem; align-items: center;
        }
        .ad-diff-row:last-child { border-bottom: none; }
        .ad-diff-row.header {
            background: rgba(41,231,255,0.05);
            font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: var(--text-mu);
        }
        .ad-diff-key {
            font-family: monospace; font-weight: 600; color: var(--text-hi);
        }
        .ad-diff-old {
            font-family: monospace; font-size: 0.75rem; color: #fca5a5;
            background: rgba(239,68,68,0.08); padding: 0.25rem 0.5rem;
            border-radius: 0.35rem; word-break: break-all;
        }
        .ad-diff-new {
            font-family: monospace; font-size: 0.75rem; color: #6ee7b7;
            background: rgba(52,211,153,0.08); padding: 0.25rem 0.5rem;
            border-radius: 0.35rem; word-break: break-all;
        }
        .ad-diff-empty {
            font-style: italic; color: var(--text-mu); font-size: 0.72rem;
        }
    </style>

    <div class="ad-page max-w-4xl mx-auto">

        <a href="{{ route('admin.audit-logs.index') }}" class="ad-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Audit Logs
        </a>

        @php $badge = $auditLog->eventBadge(); @endphp

        {{-- Header --}}
        <div class="ad-panel" style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
            <div>
                <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600; margin-bottom:0.35rem;">Log #{{ $auditLog->id }}</div>
                <div style="font-family:'Sora'; font-size:1.25rem; font-weight:700; color:var(--text-hi);">{{ $auditLog->description }}</div>
                <div style="font-size:0.78rem; color:var(--text-mu); margin-top:0.35rem;">
                    {{ $auditLog->created_at->format('M d, Y · h:i A') }} ({{ $auditLog->created_at->diffForHumans() }})
                </div>
            </div>
            <span class="ad-event"
                  style="color:{{ $badge['color'] }}; background:{{ $badge['color'] }}1a; border:1px solid {{ $badge['color'] }}44;">
                {{ $badge['label'] }}
            </span>
        </div>

        {{-- Meta --}}
        <div class="ad-panel">
            <div class="ad-panel-title">Context</div>
            <div class="ad-field">
                <span class="ad-field-label">User</span>
                <span class="ad-field-value">
                    @if ($auditLog->user)
                        {{ $auditLog->user->name }} ({{ $auditLog->user->email }})
                    @else
                        System
                    @endif
                </span>
            </div>
            <div class="ad-field">
                <span class="ad-field-label">Event</span>
                <span class="ad-field-value">{{ ucfirst($auditLog->event) }}</span>
            </div>
            @if ($auditLog->auditable_type)
                <div class="ad-field">
                    <span class="ad-field-label">Model</span>
                    <span class="ad-field-value">
                        {{ $auditLog->model_short_name }}
                        @if ($auditLog->auditable_id)
                            #{{ $auditLog->auditable_id }}
                        @endif
                    </span>
                </div>
            @endif
            <div class="ad-field">
                <span class="ad-field-label">IP Address</span>
                <span class="ad-field-value" style="font-family:monospace;">{{ $auditLog->ip_address ?? '—' }}</span>
            </div>
            @if ($auditLog->url)
                <div class="ad-field">
                    <span class="ad-field-label">URL</span>
                    <span class="ad-field-value" style="font-family:monospace; font-size:0.75rem; word-break:break-all;">{{ $auditLog->url }}</span>
                </div>
            @endif
            @if ($auditLog->user_agent)
                <div class="ad-field">
                    <span class="ad-field-label">User Agent</span>
                    <span class="ad-field-value" style="font-size:0.72rem; color:var(--text-mu);">{{ $auditLog->user_agent }}</span>
                </div>
            @endif
        </div>

        {{-- Changes --}}
        @php $changedFields = $auditLog->changed_fields; @endphp

        @if (! empty($changedFields))
            <div class="ad-panel">
                <div class="ad-panel-title">Changes ({{ count($changedFields) }} field{{ count($changedFields) > 1 ? 's' : '' }})</div>

                <div class="ad-diff">
                    <div class="ad-diff-row header">
                        <div>Field</div>
                        <div>Old Value</div>
                        <div>New Value</div>
                    </div>

                    @foreach ($changedFields as $field => $change)
                        <div class="ad-diff-row">
                            <div class="ad-diff-key">{{ $field }}</div>
                            <div>
                                @if ($change['old'] !== null && $change['old'] !== '')
                                    <span class="ad-diff-old">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</span>
                                @else
                                    <span class="ad-diff-empty">— empty —</span>
                                @endif
                            </div>
                            <div>
                                @if ($change['new'] !== null && $change['new'] !== '')
                                    <span class="ad-diff-new">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</span>
                                @else
                                    <span class="ad-diff-empty">— empty —</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif ($auditLog->new_values || $auditLog->old_values)
            <div class="ad-panel">
                <div class="ad-panel-title">Raw Values</div>

                @if ($auditLog->old_values)
                    <div style="margin-bottom:1rem;">
                        <div style="font-size:0.72rem; color:var(--text-mu); margin-bottom:0.4rem;">Old Values:</div>
                        <pre style="background: rgba(11,15,25,0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.75rem; font-size: 0.75rem; color: #fca5a5; overflow-x:auto; margin:0;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                @if ($auditLog->new_values)
                    <div>
                        <div style="font-size:0.72rem; color:var(--text-mu); margin-bottom:0.4rem;">New Values:</div>
                        <pre style="background: rgba(11,15,25,0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.75rem; font-size: 0.75rem; color: #6ee7b7; overflow-x:auto; margin:0;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        @endif

        {{-- Delete --}}
        @can('audit-log.delete')
            <div class="ad-panel" style="border-color: rgba(239,68,68,0.25);">
                <div class="ad-panel-title" style="color:#f87171;">Danger Zone</div>
                <form method="POST" action="{{ route('admin.audit-logs.destroy', $auditLog) }}"
                      onsubmit="return confirm('Delete this audit log permanently?');">
                    @csrf @method('DELETE')
                    <button type="submit" style="padding:0.7rem 1.25rem; border-radius:0.55rem; font-weight:600; font-size:0.85rem; color:#f87171; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); cursor:pointer;">
                        Delete Log
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-app-layout>