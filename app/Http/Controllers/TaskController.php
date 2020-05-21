<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskListResource;
use App\Http\Resources\TaskResource;
use App\TaskClient;
use Google_Client;
use Google_Service_Tasks;
use Google_Service_Tasks_Task;
use Google_Service_Tasks_TaskList;
use Google_Service_Tasks_Tasks;
use Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $ts = new TaskClient;
        if ($request->filled('id')) {
            $task = $ts->getTask($request->input('id'));
            return new TaskResource($task);
        } else {
            return response()->json([
                'id' => $ts->default->getId(),
                'title' => $ts->default->getTitle(),
                'tasks' => TaskResource::collection($ts->service->tasks->listTasks($ts->default->getId())->getItems())
            ]);
        }
    }
    public function addUpdate(Request $request, $task = null)
    {
        $request->validate([
            'title' => 'required|string',
            'notes' => 'required|string'
        ]);
        $ts = new TaskClient;
        if ($task) {
            $task = $ts->updateTask($task, $request->input('title'), $request->input('notes'));
        } else {
            $task = $ts->addTask($request->input('title'), $request->input('notes'));
        }
        return new TaskResource($task);
    }
    public function delete(string $task)
    {
        $ts = new TaskClient;
        return $ts->deleteTask($task);
    }
}
