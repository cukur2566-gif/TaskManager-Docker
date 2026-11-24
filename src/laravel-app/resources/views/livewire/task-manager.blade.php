
<div class="task-container">

    <h1>Task Manager</h1>

    <p class="task-description">
        Organize your work and manage your daily tasks.
    </p>

    {{-- Add New Task --}}
    <form wire:submit="addTask" class="task-form">

        <input
            type="text"
            wire:model.live="title"
            placeholder="Enter a new task..."
        >

        <button type="submit" class="add-button">
            Add Task
        </button>

    </form>

    @error('title')
        <p class="error-message">{{ $message }}</p>
    @enderror

    {{-- Tasks List --}}
    <ul class="task-list">

        @forelse($tasks as $task)

            <li class="task-item">

                <div class="task-title">

                    <input
                        type="checkbox"
                        wire:click="toggleTask({{ $task->id }})"
                        @checked($task->is_completed)
                    >

                    <span class="{{ $task->is_completed ? 'completed' : '' }}">
                        {{ $task->title }}
                    </span>

                </div>

                <div class="task-actions">

                    <button
                        wire:click="editTask({{ $task->id }})"
                        class="edit-button"
                    >
                        Edit
                    </button>

                    <button
                        wire:click="deleteTask({{ $task->id }})"
                        wire:confirm="Are you sure you want to delete this task?"
                        class="delete-button"
                    >
                        Delete
                    </button>

                </div>

            </li>

        @empty

            <li class="empty-message">
                No tasks found. Add your first task!
            </li>

        @endforelse

    </ul>

    {{-- Edit Task --}}
    @if($editingTaskId)

        <div class="edit-section">

            <h2>Edit Task</h2>

            <form wire:submit="updateTask">

                <input
                    type="text"
                    wire:model="editingTitle"
                    class="edit-input"
                >

                @error('editingTitle')
                    <p class="error-message">{{ $message }}</p>
                @enderror

                <div class="task-actions">

                    <button type="submit" class="save-button">
                        Save Changes
                    </button>

                    <button
                        type="button"
                        wire:click="$set('editingTaskId', null)"
                        class="cancel-button"
                    >
                        Cancel
                    </button>

                </div>

            </form>

        </div>

    @endif

</div>
