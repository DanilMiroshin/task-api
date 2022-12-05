<?php

namespace App\Http\Controllers;

use App\Enums\Statuses;
use App\Models\Task;
use App\Http\Requests\Tasks\{
    IndexRequest,
    StoreRequest,
    UpdateRequest,
};

class TaskController extends Controller
{
    public function index(IndexRequest $request): \Illuminate\Http\JsonResponse
    {
        $query = Task::query();

        $page = $request->get('page', Task::DEFAULT_PAGE);
        $limit = $request->get('limit', Task::DEFAULT_PAGE_SIZE);

        $query->when($request->filled('status'), static function ($query, $request){
            $query->where('status', Statuses::tryFrom($request->get('status')));
        });

        $total = $query->count();
        $tasks = $query
            ->skip($page * $limit - $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'total_tasks' => $total,
            'tasks' => $tasks,
        ]);
    }

    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $task = Task::create([
            'name' => $request->get('name'),
            'status' => $request->get('status', Statuses::NEW->value)
        ]);

        return response()->json([
            'task' => $task,
        ]);
    }

    public function update(Task $task, UpdateRequest $request): \Illuminate\Http\JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
           'task' => $task,
        ]);
    }

    public function destroy(Task $task): \Illuminate\Http\Response
    {
        $task->delete();

        return response()->noContent();
    }
}
