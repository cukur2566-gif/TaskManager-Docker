<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoTaskController extends Controller
{
    // Display tasks from Go API
    public function index()
    {
        $response = Http::get(
            config('services.go_api.url') . '/tasks'
        );

        $tasks = $response->successful()
            ? $response->json()
            : [];

        return view('go-tasks', compact('tasks'));
    }

    // Add a new task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
        ]);

        Http::post(
            config('services.go_api.url') . '/tasks',
            $validated
        )->throw();

        return redirect()->route('go-tasks.index')
            ->with('success', 'Task added successfully!');
    }

    // Delete a task
    public function destroy($id)
    {
        Http::delete(
            config('services.go_api.url') . '/tasks/' . $id
        )->throw();

        return redirect()->route('go-tasks.index')
            ->with('success', 'Task deleted successfully!');
    }
}
