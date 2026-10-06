@if (isset($announcements) && $announcements->count() > 0)
    @php
        $ann = $announcements->first();
        $bgClass = $ann->bg_color ? ' ' . $ann->bg_color : '';
        $storageKey = 'dismissed_announcement_' . $ann->id;
    @endphp

    <div class="announce-bar{{ $bgClass }}"
         id="announceBar"
         data-id="{{ $ann->id }}"
         style="display: none;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 0.6rem; flex-wrap: wrap;">
            @if ($ann->icon)
                <span style="font-size: 1rem;">{{ $ann->icon }}</span>
            @endif

            @if ($ann->link)
                <a href="{{ $ann->link }}"
                   style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">
                    {{ $ann->text }}
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            @else
                <span>{{ $ann->text }}</span>
            @endif
        </div>

        @if ($ann->is_dismissible)
            <button type="button"
                    class="announce-bar-close"
                    onclick="dismissAnnouncement('{{ $ann->id }}')"
                    aria-label="Dismiss">
                ×
            </button>
        @endif
    </div>

    @push('scripts')
    <script>
        (function () {
            const bar = document.getElementById('announceBar');
            if (! bar) return;

            const id = bar.dataset.id;
            const storageKey = 'dismissed_announcement_' + id;

            // Check if already dismissed
            if (localStorage.getItem(storageKey) !== 'true') {
                bar.style.display = '';
            }
        })();

        function dismissAnnouncement(id) {
            const bar = document.getElementById('announceBar');
            if (bar) bar.style.display = 'none';
            localStorage.setItem('dismissed_announcement_' + id, 'true');
        }
    </script>
    @endpush
@endif