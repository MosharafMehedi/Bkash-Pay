<x-app-layout>
    @section('title', $product->name)

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .pd-page {
            font-family: 'Inter', sans-serif;
            --cyan:#29e7ff;
            --violet:#a78bfa;
            --pink:#ff5fb0;
            --green:#34d399;
            --text-hi:#f1f0fb;
            --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045);
            --glass-border:rgba(255,255,255,0.09);
        }

        .pd-wrap { max-width: 1200px; margin: 0 auto; }

        /* Breadcrumb */
        .pd-breadcrumb {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.78rem; color: var(--text-mu);
            margin-bottom: 1.5rem; flex-wrap: wrap;
        }
        .pd-breadcrumb a {
            color: var(--text-mu); text-decoration: none;
            transition: color 0.2s;
        }
        .pd-breadcrumb a:hover { color: var(--cyan); }
        .pd-breadcrumb-sep { opacity: 0.5; }
        .pd-breadcrumb-current { color: var(--text-hi); }

        /* Main grid */
        .pd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        @media (max-width: 900px) { .pd-grid { grid-template-columns: 1fr; gap: 1.5rem; } }

        /* Gallery */
        .pd-gallery { position: sticky; top: 6rem; }
        @media (max-width: 900px) { .pd-gallery { position: static; } }

        .pd-main-img {
            width: 100%; aspect-ratio: 1/1;
            border-radius: 1.1rem; overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 1px solid var(--glass-border);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            position: relative;
        }
        .pd-main-img img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.5s ease;
        }
        .pd-main-img:hover img { transform: scale(1.03); }
        .pd-main-img svg { color: #64748b; width: 80px; height: 80px; }

        .pd-thumbs {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;
        }
        .pd-thumb {
            aspect-ratio: 1/1; border-radius: 0.6rem; overflow: hidden;
            border: 2px solid transparent; cursor: pointer;
            background: rgba(255,255,255,0.03);
            transition: border-color 0.2s, transform 0.15s;
        }
        .pd-thumb:hover { transform: translateY(-2px); }
        .pd-thumb.active { border-color: var(--cyan); }
        .pd-thumb img { width: 100%; height: 100%; object-fit: cover; }

        /* Right column */
        .pd-info { display: flex; flex-direction: column; gap: 1.25rem; }

        .pd-badges {
            display: flex; gap: 0.5rem; flex-wrap: wrap;
        }
        .pd-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.28rem 0.65rem; border-radius: 999px;
            font-size: 0.68rem; font-weight: 600; letter-spacing: 0.03em;
            background: rgba(41,231,255,0.12); color: var(--cyan);
            border: 1px solid rgba(41,231,255,0.25);
        }
        .pd-badge.green { background: rgba(52,211,153,0.12); color: #34d399; border-color: rgba(52,211,153,0.28); }
        .pd-badge.pink  { background: rgba(255,95,176,0.12); color: var(--pink); border-color: rgba(255,95,176,0.28); }
        .pd-badge.red   { background: rgba(239,68,68,0.12); color: #f87171; border-color: rgba(239,68,68,0.28); }
        .pd-badge.violet { background: rgba(167,139,250,0.12); color: var(--violet); border-color: rgba(167,139,250,0.28); }

        .pd-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.85rem; font-weight: 700;
            color: var(--text-hi); line-height: 1.2;
        }
        .pd-subtitle {
            font-size: 0.95rem; color: var(--text-mu); margin-top: -0.5rem;
        }

        .pd-rating {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.85rem; color: var(--text-mu);
        }
        .pd-rating .stars { color: #fbbf24; font-size: 1rem; letter-spacing: 0.05em; }
        .pd-rating .num { color: var(--text-hi); font-weight: 600; }

        .pd-price-box {
            padding: 1rem 1.15rem; border-radius: 0.9rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
        }
        .pd-price {
            font-family: 'Sora', sans-serif; font-weight: 700;
            font-size: 2rem; color: var(--cyan); line-height: 1;
        }
        .pd-price-old {
            font-size: 1rem; color: var(--text-mu);
            text-decoration: line-through; margin-left: 0.6rem;
        }
        .pd-price-sub {
            font-size: 0.78rem; color: var(--text-mu);
            margin-top: 0.4rem; display: block;
        }
        .pd-save {
            display: inline-block; margin-top: 0.5rem;
            padding: 0.2rem 0.6rem; border-radius: 999px;
            background: rgba(52,211,153,0.12); color: #34d399;
            border: 1px solid rgba(52,211,153,0.3);
            font-size: 0.72rem; font-weight: 700;
        }

        .pd-desc {
            font-size: 0.9rem; color: #cbd5e1;
            line-height: 1.65;
        }

        .pd-specs {
            display: grid; grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        .pd-spec {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.55rem 0.75rem; border-radius: 0.5rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            font-size: 0.82rem;
        }
        .pd-spec-label { color: var(--text-mu); font-size: 0.72rem; }
        .pd-spec-value { color: var(--text-hi); font-weight: 500; }

        /* Stock */
        .pd-stock {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 0.85rem; border-radius: 0.6rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            font-size: 0.85rem; font-weight: 600;
        }
        .pd-stock::before {
            content: ''; width: 8px; height: 8px; border-radius: 50%;
            background: currentColor; box-shadow: 0 0 8px currentColor;
        }
        .pd-stock.in  { color: #34d399; }
        .pd-stock.low { color: var(--pink); }
        .pd-stock.out { color: #f87171; }

        /* Quantity + Actions */
        .pd-actions {
            display: grid; grid-template-columns: auto 1fr 1fr; gap: 0.6rem;
            padding-top: 1rem;
            border-top: 1px solid var(--glass-border);
        }
        @media (max-width: 640px) {
            .pd-actions { grid-template-columns: 1fr 1fr; }
            .pd-actions .pd-qty { grid-column: 1 / -1; }
        }

        .pd-qty {
            display: flex; align-items: center;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            border-radius: 0.7rem; overflow: hidden;
        }
        .pd-qty button {
            width: 38px; height: 46px;
            background: none; border: none;
            color: var(--text-hi); font-size: 1.1rem; font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
        }
        .pd-qty button:hover:not(:disabled) { background: rgba(41,231,255,0.1); color: var(--cyan); }
        .pd-qty button:disabled { opacity: 0.3; cursor: not-allowed; }
        .pd-qty input {
            width: 44px; height: 46px;
            text-align: center; background: none; border: none;
            color: var(--text-hi); font-family: 'Sora', sans-serif;
            font-size: 0.95rem; font-weight: 700; outline: none;
        }

        .pd-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.85rem 1.25rem; border-radius: 0.7rem;
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.9rem;
            border: none; cursor: pointer;
            transition: all 0.2s; text-decoration: none;
            height: 46px;
        }
        .pd-btn-cart {
            color: var(--cyan);
            background: rgba(41,231,255,0.12);
            border: 1px solid rgba(41,231,255,0.35);
        }
        .pd-btn-cart:hover:not(:disabled) { background: rgba(41,231,255,0.22); transform: translateY(-1px); }
        .pd-btn-buy {
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
        }
        .pd-btn-buy:hover:not(:disabled) { transform: translateY(-1px); filter: brightness(1.08); box-shadow: 0 14px 34px -10px rgba(41,231,255,0.85); }
        .pd-btn:disabled { opacity: 0.6; cursor: not-allowed; }

        /* Trust */
        .pd-trust {
            display: flex; gap: 1rem; flex-wrap: wrap;
            font-size: 0.75rem; color: var(--text-mu);
            padding-top: 1rem;
            border-top: 1px solid var(--glass-border);
        }
        .pd-trust-item { display: inline-flex; align-items: center; gap: 0.35rem; }
        .pd-trust-item svg { width: 14px; height: 14px; color: var(--cyan); }

        /* Related products */
        .pd-related-title {
            font-family: 'Sora', sans-serif; font-size: 1.35rem; font-weight: 700;
            color: var(--text-hi); margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 0.6rem;
        }
        .pd-related-title::before {
            content: ''; width: 4px; height: 22px; border-radius: 2px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }

        .pd-related-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;
        }
        @media (max-width: 1024px) { .pd-related-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px)  { .pd-related-grid { grid-template-columns: repeat(2, 1fr); } }

        .pd-rel-card {
            display: flex; flex-direction: column;
            border-radius: 1rem; overflow: hidden;
            background: var(--glass); border: 1px solid var(--glass-border);
            text-decoration: none; color: inherit;
            transition: all 0.25s;
        }
        .pd-rel-card:hover {
            transform: translateY(-4px);
            border-color: rgba(41,231,255,0.4);
            box-shadow: 0 20px 40px -20px rgba(41,231,255,0.3);
        }
        .pd-rel-img {
            width: 100%; aspect-ratio: 1/1;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .pd-rel-img img { width: 100%; height: 100%; object-fit: cover; }
        .pd-rel-body { padding: 0.85rem; }
        .pd-rel-name {
            font-size: 0.85rem; font-weight: 600; color: var(--text-hi);
            margin-bottom: 0.35rem;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .pd-rel-price {
            font-family: 'Sora', sans-serif; font-weight: 700;
            font-size: 0.95rem; color: var(--cyan);
        }

        /* ═══════════ REVIEWS SECTION ═══════════ */
        .pdr-section {
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 1px solid var(--glass-border);
        }
        .pdr-header {
            margin-bottom: 1.5rem;
        }
        .pdr-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-hi);
            margin-bottom: 0.4rem;
        }
        .pdr-summary {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.9rem; color: var(--text-mu);
        }
        .pdr-avg {
            font-family: 'Sora', sans-serif; font-weight: 700;
            font-size: 1.15rem; color: var(--text-hi);
        }
        .pdr-stars { color: #fbbf24; letter-spacing: 0.05em; }
        .pdr-count { font-size: 0.85rem; }

        .pdr-alert {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.7rem 1rem; margin-bottom: 1rem;
            border-radius: 0.7rem; font-size: 0.82rem; font-weight: 500;
        }
        .pdr-alert.success { background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.3); color: #6ee7b7; }
        .pdr-alert.error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.3);  color: #fca5a5; }

        .pdr-layout {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 900px) { .pdr-layout { grid-template-columns: 1fr; } }

        .pdr-left { display: flex; flex-direction: column; gap: 1.25rem; }

        /* Rating breakdown */
        .pdr-breakdown {
            padding: 1.15rem 1.25rem;
            border-radius: 0.95rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
        }
        .pdr-breakdown-title {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; color: var(--cyan); margin-bottom: 0.85rem;
        }
        .pdr-bar-row {
            display: flex; align-items: center; gap: 0.6rem;
            margin-bottom: 0.5rem;
        }
        .pdr-bar-row:last-child { margin-bottom: 0; }
        .pdr-bar-star {
            font-size: 0.78rem; font-weight: 600; color: var(--text-mu);
            width: 26px; flex-shrink: 0;
        }
        .pdr-bar-track {
            flex: 1; height: 6px; border-radius: 999px;
            background: rgba(255,255,255,0.06);
            overflow: hidden;
        }
        .pdr-bar-fill {
            height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, var(--cyan), var(--violet));
            transition: width 0.4s ease;
        }
        .pdr-bar-count {
            font-size: 0.72rem; color: var(--text-mu);
            width: 24px; text-align: right; flex-shrink: 0;
        }

        /* Review form */
        .pdr-form-card {
            padding: 1.35rem 1.25rem;
            border-radius: 0.95rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
        }
        .pdr-form-title {
            font-family: 'Sora', sans-serif;
            font-size: 1rem; font-weight: 700; color: var(--text-hi);
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--glass-border);
        }
        .pdr-field { margin-bottom: 0.9rem; }
        .pdr-field label.pdr-label {
            display: block; font-size: 0.75rem; font-weight: 600;
            color: #cbd5e1; margin-bottom: 0.35rem;
        }
        .pdr-field .req { color: #f87171; }
        .pdr-field input,
        .pdr-field textarea {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.6rem;
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem; color: var(--text-hi);
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .pdr-field input:focus,
        .pdr-field textarea:focus { border-color: rgba(41,231,255,0.55); }
        .pdr-field textarea { resize: vertical; min-height: 70px; }
        .pdr-field input::placeholder,
        .pdr-field textarea::placeholder { color: var(--text-mu); }
        .pdr-error {
            font-size: 0.7rem; color: #f87171; margin-top: 0.3rem;
        }

        /* Star selector */
        .pdr-star-input { display: flex; gap: 0.3rem; }
        .pdr-star {
            background: none; border: none; padding: 0;
            font-size: 1.6rem; line-height: 1;
            color: rgba(255,255,255,0.15);
            cursor: pointer;
            transition: color 0.15s, transform 0.15s;
        }
        .pdr-star:hover,
        .pdr-star.hover { transform: scale(1.15); color: #fbbf24; }
        .pdr-star.active { color: #fbbf24; }

        /* Form buttons */
        .pdr-form-actions { display: flex; gap: 0.5rem; margin-top: 1rem; }
        .pdr-submit {
            flex: 1;
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.75rem 1.25rem;
            border-radius: 0.7rem;
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.88rem;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            border: none; cursor: pointer;
            transition: transform 0.2s, filter 0.2s;
        }
        .pdr-submit:hover { transform: translateY(-1px); filter: brightness(1.08); }
        .pdr-cancel {
            padding: 0.75rem 1.15rem;
            border-radius: 0.7rem;
            font-size: 0.85rem; font-weight: 600;
            color: var(--text-mu);
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            cursor: pointer;
        }

        /* Reviewed notice */
        .pdr-reviewed-notice {
            display: flex; align-items: center; gap: 0.85rem;
            padding: 1.1rem;
            border-radius: 0.95rem;
            background: rgba(52,211,153,0.06);
            border: 1px solid rgba(52,211,153,0.28);
        }
        .pdr-reviewed-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(52,211,153,0.15);
            border: 2px solid rgba(52,211,153,0.4);
            color: #34d399;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700;
            flex-shrink: 0;
        }
        .pdr-reviewed-title { font-weight: 700; color: #6ee7b7; font-size: 0.88rem; }
        .pdr-reviewed-sub { font-size: 0.72rem; color: var(--text-mu); margin-top: 0.1rem; }
        .pdr-btn-edit {
            margin-left: auto;
            padding: 0.4rem 0.85rem;
            border-radius: 0.55rem;
            font-size: 0.75rem; font-weight: 600;
            color: var(--cyan);
            background: rgba(41,231,255,0.1);
            border: 1px solid rgba(41,231,255,0.3);
            cursor: pointer;
            transition: background 0.2s;
        }
        .pdr-btn-edit:hover { background: rgba(41,231,255,0.2); }

        /* Login notice */
        .pdr-notice {
            display: flex; align-items: center; gap: 0.85rem;
            padding: 1.1rem;
            border-radius: 0.95rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
        }
        .pdr-notice-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(41,231,255,0.12);
            border: 2px solid rgba(41,231,255,0.3);
            color: var(--cyan);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .pdr-notice-title {
            font-size: 0.85rem; font-weight: 600; color: var(--text-hi);
            margin-bottom: 0.4rem;
        }
        .pdr-notice-btn {
            display: inline-flex; align-items: center;
            padding: 0.5rem 0.9rem;
            border-radius: 0.55rem;
            font-size: 0.78rem; font-weight: 600;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none;
        }

        /* Filters */
        .pdr-filters {
            display: flex; align-items: center; gap: 0.75rem;
            margin-bottom: 1.15rem; flex-wrap: wrap;
        }
        .pdr-filter-tabs { display: flex; gap: 0.35rem; flex-wrap: wrap; flex: 1; }
        .pdr-filter-tab {
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem; font-weight: 600;
            color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .pdr-filter-tab:hover { color: var(--text-hi); border-color: rgba(41,231,255,0.4); }
        .pdr-filter-tab.active {
            color: var(--cyan);
            background: rgba(41,231,255,0.1);
            border-color: rgba(41,231,255,0.5);
        }
        .pdr-sort-select {
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-hi);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            outline: none;
            cursor: pointer;
        }
        .pdr-sort-select option { background: #111827; }

        /* Review card */
        .pdr-card {
            padding: 1.25rem;
            border-radius: 0.95rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            margin-bottom: 1rem;
        }
        .pdr-card:last-child { margin-bottom: 0; }

        .pdr-card-head {
            display: flex; align-items: flex-start; gap: 0.85rem;
            margin-bottom: 0.85rem;
        }
        .pdr-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(41,231,255,0.25), rgba(167,139,250,0.25));
            border: 1px solid rgba(41,231,255,0.3);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Sora', sans-serif; font-weight: 700;
            color: var(--cyan); font-size: 1rem;
            flex-shrink: 0;
        }
        .pdr-card-info { flex: 1; min-width: 0; }
        .pdr-card-name {
            font-size: 0.9rem; font-weight: 700; color: var(--text-hi);
            margin-bottom: 0.25rem;
            display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
        }
        .pdr-card-meta {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.72rem; color: var(--text-mu);
        }
        .pdr-stars-sm { color: #fbbf24; letter-spacing: 0.03em; font-size: 0.85rem; }
        .pdr-edited { font-style: italic; }

        .pdr-badge {
            display: inline-flex; align-items: center; gap: 0.25rem;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.62rem; font-weight: 700;
            letter-spacing: 0.03em;
        }
        .pdr-badge.verified {
            background: rgba(52,211,153,0.12);
            color: #34d399;
            border: 1px solid rgba(52,211,153,0.28);
        }
        .pdr-badge.you {
            background: rgba(41,231,255,0.12);
            color: var(--cyan);
            border: 1px solid rgba(41,231,255,0.28);
        }
        .pdr-badge.admin {
            background: rgba(167,139,250,0.15);
            color: var(--violet);
            border: 1px solid rgba(167,139,250,0.3);
        }

        .pdr-card-actions { display: flex; gap: 0.35rem; }
        .pdr-icon-btn {
            width: 30px; height: 30px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.45rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            color: var(--text-mu);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .pdr-icon-btn:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); background: rgba(41,231,255,0.08); }
        .pdr-icon-btn.danger:hover { color: #f87171; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.08); }
        .pdr-icon-btn.sm { width: 26px; height: 26px; }

        .pdr-card-body { margin-top: 0.5rem; }
        .pdr-review-title {
            font-size: 0.9rem; font-weight: 600; color: var(--text-hi);
            margin-bottom: 0.35rem;
        }
        .pdr-review-comment {
            font-size: 0.85rem; color: #cbd5e1;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Replies */
        .pdr-replies {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--glass-border);
        }
        .pdr-reply { display: flex; gap: 0.75rem; margin-bottom: 0.85rem; }
        .pdr-reply:last-child { margin-bottom: 0; }
        .pdr-reply-line {
            width: 2px;
            background: linear-gradient(180deg, rgba(41,231,255,0.4), transparent);
            border-radius: 2px;
            flex-shrink: 0;
        }
        .pdr-reply-body { flex: 1; }
        .pdr-reply-head {
            display: flex; align-items: center; gap: 0.6rem;
            margin-bottom: 0.5rem;
        }
        .pdr-reply-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
            color: var(--text-hi);
            flex-shrink: 0;
        }
        .pdr-reply-avatar.admin {
            background: rgba(167,139,250,0.15);
            border-color: rgba(167,139,250,0.35);
        }
        .pdr-reply-info { flex: 1; min-width: 0; }
        .pdr-reply-name {
            font-size: 0.8rem; font-weight: 600; color: var(--text-hi);
            display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;
        }
        .pdr-reply-meta {
            font-size: 0.68rem; color: var(--text-mu);
            display: flex; align-items: center; gap: 0.3rem;
            margin-top: 0.1rem;
        }
        .pdr-reply-comment {
            font-size: 0.82rem; color: #cbd5e1;
            line-height: 1.55;
            padding-left: 2.3rem;
            white-space: pre-wrap;
        }

        /* Reply form */
        .pdr-reply-form-wrap {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--glass-border);
        }
        .pdr-reply-form { display: flex; gap: 0.5rem; align-items: center; }
        .pdr-reply-form input {
            flex: 1;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.6rem;
            padding: 0.55rem 0.8rem;
            font-size: 0.82rem; color: var(--text-hi);
            outline: none;
            font-family: inherit;
        }
        .pdr-reply-form input:focus { border-color: rgba(41,231,255,0.55); }
        .pdr-reply-form button[type="submit"] {
            padding: 0.55rem 1rem;
            border-radius: 0.6rem;
            font-size: 0.78rem; font-weight: 600;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            border: none; cursor: pointer;
            white-space: nowrap;
        }
        .pdr-cancel-sm {
            padding: 0.55rem 0.85rem;
            border-radius: 0.6rem;
            font-size: 0.75rem; font-weight: 600;
            color: var(--text-mu);
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            cursor: pointer;
        }
        .pdr-reply-trigger {
            display: inline-flex; align-items: center; gap: 0.4rem;
            margin-top: 0.85rem;
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem; font-weight: 600;
            color: var(--text-mu);
            background: none;
            border: 1px solid var(--glass-border);
            cursor: pointer;
            transition: all 0.2s;
        }
        .pdr-reply-trigger:hover {
            color: var(--cyan);
            border-color: rgba(41,231,255,0.4);
            background: rgba(41,231,255,0.06);
        }

        /* Empty state */
        .pdr-empty {
            text-align: center;
            padding: 3rem 1.5rem;
            border-radius: 0.95rem;
            background: var(--glass);
            border: 1px dashed var(--glass-border);
        }
        .pdr-empty-icon {
            width: 72px; height: 72px;
            margin: 0 auto 0.85rem;
            border-radius: 50%;
            background: rgba(41,231,255,0.08);
            border: 2px dashed rgba(41,231,255,0.3);
            color: var(--cyan);
            display: flex; align-items: center; justify-content: center;
        }
        .pdr-empty-title {
            font-family: 'Sora', sans-serif;
            font-size: 1rem; font-weight: 700; color: var(--text-hi);
            margin-bottom: 0.35rem;
        }
        .pdr-empty-sub { font-size: 0.82rem; color: var(--text-mu); }

        /* Pagination */
        .pdr-pagination { margin-top: 1.25rem; }

        /* Toast + spinner */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes slideIn {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to   { transform: translateX(120%); opacity: 0; }
        }
    </style>

    <div class="pd-page pd-wrap">

        {{-- Breadcrumb --}}
        <nav class="pd-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="pd-breadcrumb-sep">/</span>
            <a href="{{ route('products.index') }}">Products</a>
            <span class="pd-breadcrumb-sep">/</span>
            <a href="{{ route('products.index', ['category' => $product->category]) }}">{{ $product->category ?? 'All' }}</a>
            <span class="pd-breadcrumb-sep">/</span>
            <span class="pd-breadcrumb-current">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</span>
        </nav>

        {{-- Main grid --}}
        <div class="pd-grid">

            {{-- ══ LEFT: Gallery ══ --}}
            <div class="pd-gallery">

                @php
                    $images = [];
                    if ($product->image) $images[] = $product->image;
                    if ($product->gallery && is_array($product->gallery)) {
                        $images = array_merge($images, $product->gallery);
                    }
                @endphp

                <div class="pd-main-img" id="mainImage">
                    @if (! empty($images))
                        <img src="{{ Storage::url($images[0]) }}" alt="{{ $product->name }}" id="mainImg">
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>

                @if (count($images) > 1)
                    <div class="pd-thumbs">
                        @foreach ($images as $idx => $img)
                            <div class="pd-thumb {{ $idx === 0 ? 'active' : '' }}"
                                 data-src="{{ Storage::url($img) }}">
                                <img src="{{ Storage::url($img) }}" alt="thumb {{ $idx + 1 }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ══ RIGHT: Info ══ --}}
            <div class="pd-info">

                {{-- Badges --}}
                <div class="pd-badges">
                    @if ($product->category)
                        <span class="pd-badge">{{ $product->category }}</span>
                    @endif
                    @if ($product->brand)
                        <span class="pd-badge violet">{{ $product->brand }}</span>
                    @endif
                    @if ($product->discount_price && $product->discount_price < $product->price_bdt)
                        @php $pct = round((($product->price_bdt - $product->discount_price) / $product->price_bdt) * 100); @endphp
                        <span class="pd-badge pink">-{{ $pct }}% OFF</span>
                    @endif
                    @if ($product->is_featured)
                        <span class="pd-badge">⭐ Featured</span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="pd-title">{{ $product->name }}</h1>
                @if ($product->subtitle)
                    <p class="pd-subtitle">{{ $product->subtitle }}</p>
                @endif

                {{-- Rating --}}
                @php
                    $rating  = (float) ($product->rating ?? 0);
                    $rounded = (int) round($rating);
                    $stars   = str_repeat('★', $rounded) . str_repeat('☆', 5 - $rounded);
                @endphp
                <div class="pd-rating">
                    <span class="stars">{{ $stars }}</span>
                    <span class="num">{{ number_format($rating, 1) }}</span>
                    <a href="#reviews" style="color:var(--text-mu); text-decoration:underline;">
                        · {{ $product->review_count ?? 0 }} reviews
                    </a>
                </div>

                {{-- Price --}}
                @php
                    $hasDiscount = $product->discount_price && $product->discount_price < $product->price_bdt;
                    $finalPrice  = $hasDiscount ? $product->discount_price : $product->price_bdt;
                @endphp
                <div class="pd-price-box">
                    <div>
                        <span class="pd-price">৳{{ number_format($finalPrice, 0) }}</span>
                        @if ($hasDiscount)
                            <span class="pd-price-old">৳{{ number_format($product->price_bdt, 0) }}</span>
                        @endif
                    </div>
                    <span class="pd-price-sub">${{ number_format($product->price_usd, 2) }} via PayPal</span>
                    @if ($hasDiscount)
                        <span class="pd-save">Save ৳{{ number_format($product->price_bdt - $product->discount_price, 0) }}</span>
                    @endif
                </div>

                {{-- Description --}}
                @if ($product->description)
                    <p class="pd-desc">{{ $product->description }}</p>
                @endif

                {{-- Specs --}}
                <div class="pd-specs">
                    @if ($product->sku)
                        <div class="pd-spec">
                            <span class="pd-spec-label">SKU</span>
                            <span class="pd-spec-value">{{ $product->sku }}</span>
                        </div>
                    @endif
                    @if ($product->category)
                        <div class="pd-spec">
                            <span class="pd-spec-label">Category</span>
                            <span class="pd-spec-value">{{ $product->category }}</span>
                        </div>
                    @endif
                    @if ($product->brand)
                        <div class="pd-spec">
                            <span class="pd-spec-label">Brand</span>
                            <span class="pd-spec-value">{{ $product->brand }}</span>
                        </div>
                    @endif
                    <div class="pd-spec">
                        <span class="pd-spec-label">Available</span>
                        <span class="pd-spec-value">{{ $product->stock }} units</span>
                    </div>
                </div>

                {{-- Stock --}}
                @php
                    $stock = (int) $product->stock;
                    if ($stock <= 0) {
                        $stockClass = 'out'; $stockText = 'Out of Stock';
                    } elseif ($stock <= 3) {
                        $stockClass = 'low'; $stockText = "Only {$stock} left in stock";
                    } else {
                        $stockClass = 'in';  $stockText = 'In Stock';
                    }

                    $maxQty = min(5, max($stock, 1));
                @endphp
                <div>
                    <span class="pd-stock {{ $stockClass }}">{{ $stockText }}</span>
                </div>

                {{-- Actions --}}
                @if ($stock > 0)
                    <div class="pd-actions">
                        <div class="pd-qty">
                            <button type="button" id="qtyMinus">−</button>
                            <input type="number" id="qtyInput" value="1" min="1" max="{{ $maxQty }}" readonly>
                            <button type="button" id="qtyPlus">+</button>
                        </div>

                        <button type="button" class="pd-btn pd-btn-cart" id="addToCartBtn">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Add to Cart
                        </button>

                        <button type="button" class="pd-btn pd-btn-buy" id="buyNowBtn">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Buy Now
                        </button>
                    </div>
                @else
                    <div class="pd-actions" style="grid-template-columns: 1fr;">
                        <button class="pd-btn pd-btn-cart" disabled style="grid-column: 1 / -1;">
                            Out of Stock
                        </button>
                    </div>
                @endif

                {{-- Trust --}}
                <div class="pd-trust">
                    <span class="pd-trust-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Secure Payment
                    </span>
                    <span class="pd-trust-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Fast Delivery
                    </span>
                    <span class="pd-trust-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Money Back
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══ Related Products ═══ --}}
        @if ($relatedProducts->count() > 0)
            <div>
                <h2 class="pd-related-title">You may also like</h2>

                <div class="pd-related-grid">
                    @foreach ($relatedProducts as $rel)
                        @php
                            $relHasDiscount = $rel->discount_price && $rel->discount_price < $rel->price_bdt;
                            $relFinal = $relHasDiscount ? $rel->discount_price : $rel->price_bdt;
                        @endphp

                        <a href="{{ route('products.show', $rel) }}" class="pd-rel-card">
                            <div class="pd-rel-img">
                                @if ($rel->image)
                                    <img src="{{ Storage::url($rel->image) }}" alt="{{ $rel->name }}">
                                @else
                                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#64748b;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="pd-rel-body">
                                <div class="pd-rel-name">{{ $rel->name }}</div>
                                <div class="pd-rel-price">৳{{ number_format($relFinal, 0) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ═══ Reviews Section ═══ --}}
        @include('products.partials._reviews')

    </div>

    <script>
    (function () {
        // ── Config ──
        const productId  = {{ $product->id }};
        const csrfToken  = '{{ csrf_token() }}';
        const addRoute   = "{{ route('cart.add') }}";
        const cartUrl    = "{{ route('cart.index') }}";

        // ── Gallery thumbnails ──
        const mainImg = document.getElementById('mainImg');
        document.querySelectorAll('.pd-thumb').forEach((thumb) => {
            thumb.addEventListener('click', () => {
                const src = thumb.dataset.src;
                if (mainImg && src) mainImg.src = src;
                document.querySelectorAll('.pd-thumb').forEach((t) => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });

        // ── Quantity stepper ──
        const qtyInput  = document.getElementById('qtyInput');
        const minusBtn  = document.getElementById('qtyMinus');
        const plusBtn   = document.getElementById('qtyPlus');
        const maxQty    = {{ $maxQty ?? 5 }};
        const minQty    = 1;

        function syncQty() {
            const val = Math.min(Math.max(parseInt(qtyInput.value) || 1, minQty), maxQty);
            qtyInput.value = val;
            minusBtn.disabled = val <= minQty;
            plusBtn.disabled  = val >= maxQty;
        }

        minusBtn?.addEventListener('click', () => {
            qtyInput.value = (parseInt(qtyInput.value) || 1) - 1;
            syncQty();
        });

        plusBtn?.addEventListener('click', () => {
            qtyInput.value = (parseInt(qtyInput.value) || 1) + 1;
            syncQty();
        });

        syncQty();

        // ── Toast ──
        function showToast(message, type = 'success') {
            let container = document.getElementById('cartToastContainer');
            if (! container) {
                container = document.createElement('div');
                container.id = 'cartToastContainer';
                container.style.cssText = 'position:fixed;top:1.5rem;right:1.5rem;z-index:9999;display:flex;flex-direction:column;gap:0.5rem;';
                document.body.appendChild(container);
            }

            const bg     = type === 'error' ? 'rgba(239,68,68,0.15)' : 'rgba(52,211,153,0.15)';
            const border = type === 'error' ? 'rgba(239,68,68,0.4)'  : 'rgba(52,211,153,0.4)';
            const color  = type === 'error' ? '#fca5a5'              : '#6ee7b7';

            const toast = document.createElement('div');
            toast.style.cssText = `
                padding: 0.85rem 1.15rem;
                border-radius: 0.7rem;
                background: ${bg};
                border: 1px solid ${border};
                color: ${color};
                font-size: 0.85rem;
                font-weight: 600;
                backdrop-filter: blur(15px);
                box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
                animation: slideIn 0.3s ease;
                max-width: 320px;
            `;
            toast.textContent = message;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function updateCartBadge(count) {
            const badge   = document.getElementById('cartBadge');
            const countEl = document.getElementById('cartCount');
            if (! badge) return;

            if (count > 0) {
                badge.style.display = 'flex';
                if (countEl) countEl.textContent = count;
            } else {
                badge.style.display = 'none';
            }
        }

        function setBtnLoading(btn, loading, text = 'Loading...') {
            if (! btn) return;
            if (loading) {
                btn.disabled = true;
                if (! btn.dataset.originalText) btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = '<svg width="16" height="16" style="animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> ' + text;
            } else {
                btn.disabled = false;
                if (btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
            }
        }

        async function addToCart(redirectToCart = false) {
            const qty = parseInt(qtyInput.value) || 1;

            try {
                const res = await fetch(addRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: qty,
                    }),
                });

                const data = await res.json();

                if (! data.ok) {
                    showToast(data.message || 'Failed to add to cart', 'error');
                    return false;
                }

                updateCartBadge(data.cart_count);
                showToast(data.message || 'Added to cart!', 'success');

                if (redirectToCart) {
                    setTimeout(() => {
                        window.location.href = cartUrl;
                    }, 400);
                }

                return true;
            } catch (e) {
                showToast('Something went wrong. Try again.', 'error');
                return false;
            }
        }

        const addBtn = document.getElementById('addToCartBtn');
        const buyBtn = document.getElementById('buyNowBtn');

        addBtn?.addEventListener('click', async () => {
            setBtnLoading(addBtn, true, 'Adding...');
            await addToCart(false);
            setBtnLoading(addBtn, false);
        });

        buyBtn?.addEventListener('click', async () => {
            setBtnLoading(buyBtn, true, 'Redirecting...');
            await addToCart(true);
        });

        // ── Review star selector ──
        const starInput   = document.getElementById('starInput');
        const ratingInput = document.getElementById('ratingInput');

        if (starInput) {
            starInput.querySelectorAll('.pdr-star').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const value = parseInt(btn.dataset.value);
                    ratingInput.value = value;

                    starInput.querySelectorAll('.pdr-star').forEach((s) => {
                        s.classList.toggle('active', parseInt(s.dataset.value) <= value);
                    });
                });

                btn.addEventListener('mouseenter', () => {
                    const hoverValue = parseInt(btn.dataset.value);
                    starInput.querySelectorAll('.pdr-star').forEach((s) => {
                        s.classList.toggle('hover', parseInt(s.dataset.value) <= hoverValue);
                    });
                });
            });

            starInput.addEventListener('mouseleave', () => {
                starInput.querySelectorAll('.pdr-star').forEach((s) => s.classList.remove('hover'));
            });
        }
    })();
    </script>
</x-app-layout>