<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go API Task Manager</title>
</head>
<body>

    <h1>Laravel + Go REST API</h1>

    @if(session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    @if($errors->any())
        <p style="color: red;">
            {{ $errors->first() }}
        </p>
    @endif

    <h2>Add New Task</h2>

    <form method="POST" action="{{ route('go-tasks.store') }}">
        @csrf

        <input
            type="text"
            name="title"
            placeholder="Enter task title"
            required
        >

        <button type="submit">Add Task</button>
    </form>

    <h2>Tasks from Go Backend</h2>

    @forelse($tasks as $task)

        <div style="margin: 15px 0;">

            <strong>{{ $task['title'] }}</strong>

            <form
                method="POST"
                action="{{ route('go-tasks.destroy', $task['id']) }}"
                style="display: inline;"
            >
                @csrf
                @method('DELETE')

                <button type="submit">Delete</button>
            </form>

        </div>

    @empty

        <p>No tasks found in Go Backend.</p>

    @endforelse

</body>
</html>
