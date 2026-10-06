<footer style="background: rgba(11,15,25,0.7); backdrop-filter: blur(20px); border-top: 1px solid var(--glass-border); margin-top: 4rem;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 2rem 1.25rem; text-align: center;">
        <p style="font-size: 0.82rem; color: #94a3b8;">
            &copy; {{ date('Y') }}
            <span style="color: #29e7ff; font-weight: 600;">{{ $settings['site_title'] ?? config('app.name') }}</span>.
            {{ $settings['footer_copyright'] ?? 'All rights reserved.' }}
        </p>
    </div>
</footer>