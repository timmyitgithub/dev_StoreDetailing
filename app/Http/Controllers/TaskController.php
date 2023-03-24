<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Jobs\SendEmail;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $tasks = Task::all();

        return view('admin.tasks', compact('tasks'));
    }

    public function store(Request $request)
    {
        $task = new Task();
        $task->name = $request->name;
        $task->save();

        $users = User::all();
        $message = [
            'type' => 'Create task',
            'task' => $task->name,
            'content' => 'has been created!',
        ];
        SendEmail::dispatch($message, $users)->delay(now()->addMinute(1));

        return redirect()->back();
    }

    public function delete($id)
    {
        $task = Task::find($id);
        $task->delete();

        $users = User::all();
        $message = [
            'type' => 'Delete task',
            'task' => $task->name,
            'content' => 'has been deleted!',
        ];
        SendEmail::dispatch($message, $users)->delay(now()->addMinute(1));

        return redirect()->back();
    }
}
