<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __('messages.page_expired') }} — MoreNova</title>
    <link rel="icon" type="image/x-icon" href="https://direxgroup.com/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #124C78;
            --navy-deep: #0d3a5c;
            --teal: #52E0D7;
            --sky: #6EC1E4;
            --ink: #54595F;
            --muted: #7A7A7A;
            --page: #F9FBFF;
            --card: #ffffff;
            --ease: cubic-bezier(0.22, 1, 0.36, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: var(--navy);
            background:
                radial-gradient(ellipse 70% 45% at 15% 0%, rgba(82, 224, 215, 0.16), transparent 55%),
                radial-gradient(ellipse 55% 40% at 100% 10%, rgba(110, 193, 228, 0.14), transparent 50%),
                radial-gradient(ellipse 50% 35% at 50% 100%, rgba(18, 76, 120, 0.06), transparent 55%),
                var(--page);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.35;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.45'/%3E%3C/svg%3E");
            background-size: 180px 180px;
            mix-blend-mode: soft-light;
        }

        .shell {
            position: relative;
            width: min(34rem, 100%);
            text-align: center;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1.75rem;
            opacity: 0;
            animation: rise 0.7s var(--ease) 0.05s forwards;
            text-decoration: none;
            color: var(--navy);
        }

        .brand img {
            width: 2.25rem;
            height: 2.25rem;
            object-fit: contain;
        }

        .brand span {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
        }

        .card {
            position: relative;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(18, 76, 120, 0.08);
            border-radius: 1.35rem;
            padding: 2.5rem 2rem 2.25rem;
            box-shadow:
                0 18px 45px rgba(18, 76, 120, 0.1),
                0 1px 0 rgba(255, 255, 255, 0.8) inset;
            overflow: hidden;
            opacity: 0;
            animation: rise 0.75s var(--ease) 0.12s forwards;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(267deg, #124C78 9%, #52E0D7 95%);
        }

        .glyph {
            width: 5.5rem;
            height: 5.5rem;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 30% 25%, rgba(82, 224, 215, 0.35), transparent 55%),
                linear-gradient(145deg, rgba(18, 76, 120, 0.08), rgba(110, 193, 228, 0.18));
            box-shadow: 0 10px 28px rgba(18, 76, 120, 0.12);
            position: relative;
        }

        .glyph svg {
            width: 2.35rem;
            height: 2.35rem;
            color: var(--navy);
            animation: pulse 2.8s var(--ease) infinite;
        }

        .code {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--teal);
            margin-bottom: 0.65rem;
            opacity: 0;
            animation: rise 0.7s var(--ease) 0.22s forwards;
        }

        h1 {
            font-size: clamp(1.55rem, 4vw, 1.9rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 0.85rem;
            color: var(--navy);
            opacity: 0;
            animation: rise 0.7s var(--ease) 0.28s forwards;
        }

        .lead {
            font-size: 1.02rem;
            line-height: 1.55;
            color: var(--ink);
            max-width: 28rem;
            margin: 0 auto 1.85rem;
            opacity: 0;
            animation: rise 0.7s var(--ease) 0.34s forwards;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
            opacity: 0;
            animation: rise 0.7s var(--ease) 0.42s forwards;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.8rem 1.35rem;
            border-radius: 0.75rem;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s var(--ease), box-shadow 0.2s var(--ease), background 0.2s var(--ease);
        }

        .btn-primary {
            color: #fff;
            background: var(--navy);
            box-shadow: 0 8px 22px rgba(18, 76, 120, 0.28);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-deep) 70%, var(--sky) 160%);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(18, 76, 120, 0.32);
        }

        .btn-ghost {
            color: var(--navy);
            background: rgba(18, 76, 120, 0.05);
            border: 1px solid rgba(18, 76, 120, 0.12);
        }

        .btn-ghost:hover {
            background: rgba(82, 224, 215, 0.12);
            transform: translateY(-1px);
        }

        .foot {
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: var(--muted);
            opacity: 0;
            animation: rise 0.7s var(--ease) 0.5s forwards;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.06); opacity: 0.85; }
        }

        @media (prefers-reduced-motion: reduce) {
            .brand, .card, .code, h1, .lead, .actions, .foot { animation: none; opacity: 1; }
            .glyph svg { animation: none; }
            .btn:hover { transform: none; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <a class="brand" href="{{ url('/') }}">
            <img src="{{ asset('img/DirexLogo.png') }}" alt="MoreNova">
            <span>MoreNova</span>
        </a>

        <section class="card" aria-labelledby="expired-title">
            <div class="glyph" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 2"></path>
                </svg>
            </div>

            <p class="code">419</p>
            <h1 id="expired-title">{{ __('messages.page_expired') }}</h1>
            <p class="lead">{{ __('messages.page_expired_hint') }}</p>

            <div class="actions">
                <a class="btn btn-primary" href="{{ url('/') }}">
                    {{ __('messages.back_to_home') }}
                </a>
                <a class="btn btn-ghost" href="javascript:history.back()">
                    {{ __('messages.go_back') }}
                </a>
            </div>
        </section>

        <p class="foot">MoreNova · Direx</p>
    </main>
</body>
</html>
