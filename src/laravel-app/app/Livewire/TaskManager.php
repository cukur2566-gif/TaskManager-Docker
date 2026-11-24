<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;

class TaskManager extends Component
{
    public $title = '';
    public $editingTaskId = null;
    public $editingTitle = '';

   public function addTask()
   {
    $this->validate([
        'title' => 'required|min:3|max:255',
    ]);

    Task::create([
        'title' => $this->title,
        'is_completed' => false,
    ]);

    $this->reset('title');
    }

    public function editTask($id)
    {
        $task = Task::findOrFail($id);

        $this->editingTaskId = $task->id;
        $this->editingTitle = $task->title;
    }

    public function updateTask()
    {
        $this->validate([
            'editingTitle' => 'required|min:3|max:255',
        ]);

        $task = Task::findOrFail($this->editingTaskId);

        $task->update([
            'title' => $this->editingTitle,
        ]);

        $this->reset('editingTaskId', 'editingTitle');
    }

    public function deleteTask($id)
    {
        Task::findOrFail($id)->delete();
    }

    public function toggleTask($id)
    {
        $task = Task::findOrFail($id);

        $task->update([
            'is_completed' => !$task->is_completed,
        ]);
    }

    public function render()
    {
        return view('livewire.task-manager', [
            'tasks' => Task::latest()->get(),
        ]);
    }
}
