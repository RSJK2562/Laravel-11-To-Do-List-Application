<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    // Display a list of all tasks.
    // Shows only active (non-completed) tasks by default.

    public function index(): View
    {
        $tasks = Task::active()->orderBy('created_at', 'desc')->get();
        return view('index', compact('tasks'));
    }

    // Store a new task.
    // Validates input and prevents duplicate tasks.

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255|min:1',
        ]);

        $title = trim($request->title);

        // Check for duplicate tasks (case-insensitive)
        if (Task::titleExists($title)) {
            return response()->json([
                'success' => false,
                'message' => 'A task with this title already exists!'
            ], 400);
        }

        // Create the task
        $task = Task::create([
            'title' => $title,
            'completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully!',
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'completed' => $task->completed,
                'created_at' => $task->created_at->format('M d, Y'),
            ]
        ]);
    }

    // Toggle task completion status.
    // When completed, task should disappear from active view.

    public function toggle(Request $request, Task $task): JsonResponse
    {
        $task->toggleCompletion();

        return response()->json([
            'success' => true,
            'message' => $task->completed ? 'Task marked as completed!' : 'Task marked as active!',
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'completed' => $task->completed,
            ]
        ]);
    }

    // Delete a task.
    // Permanently removes the task from database.
    public function destroy(Task $task): JsonResponse
    {
        $taskTitle = $task->title;
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => "Task '{$taskTitle}' has been deleted successfully!"
        ]);
    }


    // Get all tasks (both completed and active).
    // Used for "Show All Tasks" functionality.
    public function all(): JsonResponse
    {
        $activeTasks = Task::active()->orderBy('created_at', 'desc')->get();
        $completedTasks = Task::completed()->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'activeTasks' => $activeTasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'completed' => $task->completed,
                    'created_at' => $task->created_at->format('M d, Y'),
                ];
            }),
            'completedTasks' => $completedTasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'completed' => $task->completed,
                    'completed_at' => $task->updated_at->format('M d, Y'),
                ];
            })
        ]);
    }

    // Get only active tasks.
    // Used to return to normal view from "Show All Tasks".
    public function active(): JsonResponse
    {
        $tasks = Task::active()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'tasks' => $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'completed' => $task->completed,
                    'created_at' => $task->created_at->format('M d, Y'),
                ];
            })
        ]);
    }
}
