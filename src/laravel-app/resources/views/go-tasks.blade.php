<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager | Laravel + Go</title>
    <style>
        :root {
            --bg1: #eef2ff;
            --bg2: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --success: #15803d;
            --success-bg: #f0fdf4;
            --go: #00acd7;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg1: #0b1020;
                --bg2: #111827;
                --card: #1e293b;
                --text: #f1f5f9;
                --muted: #94a3b8;
                --border: #334155;
                --primary: #6366f1;
                --primary-hover: #818cf8;
                --danger: #f87171;
                --danger-bg: #3b1d1d;
                --success: #4ade80;
                --success-bg: #12301f;
            }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: var(--text);
            background: linear-gradient(160deg, var(--bg1), var(--bg2));
            display: flex;
            justify-content: center;
            padding: 48px 16px;
        }
        .wrap { width: 100%; max-width: 640px; }
        header { text-align: center; margin-bottom: 28px; }
        h1 { margin: 0 0 8px; font-size: 2rem; letter-spacing: -0.02em; }
        .flow {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: .85rem; color: var(--muted);
        }
        .pill {
            padding: 3px 10px; border-radius: 999px; font-weight: 600;
            border: 1px solid var(--border); background: var(--card);
        }
        .pill.laravel { color: #f05340; }
        .pill.go { color: var(--go); }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
            margin-bottom: 20px;
        }
        .alert {
            padding: 12px 14px; border-radius: 10px; margin-bottom: 16px;
            font-size: .95rem; border: 1px solid transparent;
        }
        .alert.success { background: var(--success-bg); color: var(--success); border-color: var(--success); }
        .alert.error { background: var(--danger-bg); color: var(--danger); border-color: var(--danger); }
        form.add { display: flex; gap: 10px; }
        input[type="text"] {
            flex: 1; min-width: 0;
            padding: 12px 14px; font-size: 1rem;
            border: 1px solid var(--border); border-radius: 10px;
            background: transparent; color: var(--text);
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        input[type="text"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .25);
        }
        button {
            font: inherit; cursor: pointer; border: 0; border-radius: 10px;
            transition: background .15s, transform .05s;
        }
        button:active { transform: scale(.98); }
        .btn-primary { padding: 12px 20px; font-weight: 600; color: #fff; background: var(--primary); }
        .btn-primary:hover { background: var(--primary-hover); }
        .list-head {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 12px;
        }
        .list-head h2 { margin: 0; font-size: 1.1rem; }
        .count {
            font-size: .8rem; font-weight: 600; color: var(--primary);
            background: rgba(99, 102, 241, .12); padding: 3px 10px; border-radius: 999px;
        }
        ul { list-style: none; margin: 0; padding: 0; }
        li {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 4px; border-top: 1px solid var(--border);
        }
        li:first-child { border-top: 0; }
        .num {
            flex: none; width: 32px; height: 32px; border-radius: 50%;
            display: grid; place-items: center; font-size: .85rem; font-weight: 700;
            color: var(--primary); background: rgba(99, 102, 241, .12);
        }
        .title { flex: 1; word-break: break-word; }
        .btn-delete {
            padding: 7px 12px; font-size: .85rem; font-weight: 600;
            color: var(--danger); background: transparent; border: 1px solid var(--border);
        }
        .btn-delete:hover { background: var(--danger-bg); border-color: var(--danger); }
        .empty { text-align: center; padding: 28px 8px; color: var(--muted); }
        .empty .icon { font-size: 2rem; display: block; margin-bottom: 6px; }
        footer { text-align: center; font-size: .8rem; color: var(--muted); }
        @media (max-width: 480px) {
            form.add { flex-direction: column; }
            h1 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
<div class="wrap">

    <header>
        <h1>Task Manager</h1>
        <div class="flow">
            <span class="pill laravel">Laravel</span>
            <span>&rarr;</span>
            <span class="pill go">Go REST API</span>
        </div>
    </header>

    <div class="card">
        @if(session('success'))
            <div class="alert success" role="status" id="flash">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form class="add" method="POST" action="{{ route('go-tasks.store') }}">
            @csrf
            <input
                type="text"
                name="title"
                value="{{ old('title') }}"
                placeholder="What needs to be done?"
                aria-label="Task title"
                minlength="3"
                maxlength="255"
                required
            >
            <button type="submit" class="btn-primary">Add Task</button>
        </form>
    </div>

    <div class="card">
        <div class="list-head">
            <h2>Tasks from Go Backend</h2>
            <span class="count">{{ count($tasks ?? []) }} {{ count($tasks ?? []) === 1 ? 'task' : 'tasks' }}</span>
        </div>

        @forelse($tasks as $task)
            @if($loop->first)<ul>@endif
            <li>
                <span class="num">{{ $loop->iteration }}</span>
                <span class="title">{{ $task['title'] }}</span>
                <form
                    method="POST"
                    action="{{ route('go-tasks.destroy', $task['id']) }}"
                    onsubmit="return confirm('Delete this task?');"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete">Delete</button>
                </form>
            </li>
            @if($loop->last)</ul>@endif
        @empty
            <div class="empty">
                <span class="icon">&#128203;</span>
                No tasks yet. Add your first one above.
            </div>
        @endforelse
    </div>

    <footer>Tasks are stored by the Go service and managed through Laravel.</footer>
</div>

<script>
    // Hide the success message after a few seconds
    var flash = document.getElementById('flash');
    if (flash) {
        setTimeout(function () { flash.style.display = 'none'; }, 4000);
    }
</script>
</body>
</html>