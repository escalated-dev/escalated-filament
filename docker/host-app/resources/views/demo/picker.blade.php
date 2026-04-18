<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escalated — Demo</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            margin: 0;
            padding: 2rem;
        }
        .wrap { max-width: 720px; margin: 0 auto; }
        h1 { font-size: 1.5rem; margin: 0 0 0.25rem; }
        p.lede { color: #94a3b8; margin: 0 0 2rem; }
        .group { margin-bottom: 1.5rem; }
        .group h2 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin: 0 0 0.5rem;
        }
        form { display: block; margin: 0; }
        button.user {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            color: #f1f5f9;
            font-size: 0.95rem;
            cursor: pointer;
            margin-bottom: 0.5rem;
            text-align: left;
            transition: border-color 120ms, background 120ms;
        }
        button.user:hover { background: #273549; border-color: #475569; }
        .meta { color: #94a3b8; font-size: 0.8rem; }
        .badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            background: #334155;
            color: #cbd5e1;
            margin-left: 0.5rem;
        }
        .badge.admin { background: #7c3aed; color: white; }
        .badge.agent { background: #0ea5e9; color: white; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Escalated Demo</h1>
    <p class="lede">Click a user to log in. Every restart reseeds the database from scratch.</p>

    @php
        $admins = $users->filter(fn ($u) => $u->is_admin);
        $agents = $users->filter(fn ($u) => $u->is_agent && ! $u->is_admin);
        $customers = $users->filter(fn ($u) => ! $u->is_admin && ! $u->is_agent);
    @endphp

    @foreach ([['Admins', $admins, 'admin'], ['Agents', $agents, 'agent'], ['Customers', $customers, '']] as [$label, $group, $badgeClass])
        @if ($group->isNotEmpty())
            <div class="group">
                <h2>{{ $label }}</h2>
                @foreach ($group as $u)
                    <form method="POST" action="{{ route('demo.login', $u) }}">
                        @csrf
                        <button type="submit" class="user">
                            <span>
                                {{ $u->name }}
                                @if ($badgeClass)
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($badgeClass) }}</span>
                                @endif
                            </span>
                            <span class="meta">{{ $u->email }}</span>
                        </button>
                    </form>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
</body>
</html>
