<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class TaskController
 * @group Task
 *
 * APIs to managing task
 */
class TaskController extends Controller
{
    /**
     *
     * @param Request $request
     * @return TaskCollection
     * @apiResourceCollection App\Http\Resources\TaskCollection
     * @apiResourceModel App\Models\Task with=user
     */
    public function index(Request $request): TaskCollection
    {
        $tasksQuery = Auth::user()->tasks()->getQuery();
        // $tasksQuery = Task::query();

        return $this->runIndex($request, $tasksQuery, TaskCollection::class);
    }

    /**
     *
     * @param TaskStoreRequest $request
     * @return TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function store(TaskStoreRequest $request): TaskResource
    {

        $task = Task::create($request->validated());

        return new TaskResource($task);
    }

    /**
     *
     * @param Task $task
     * @return TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function show(Task $task): TaskResource
    {
        // Check if the authenticated user is authorized to view the task
        $this->authorize('view', $task);


        return new TaskResource($task);
    }

    /**
     *
     * @param TaskUpdateRequest $request
     * @param Task $task
     * @return TaskResource
     * @apiResourceModel App\Models\Task
     */
    public function update(TaskUpdateRequest $request, Task $task): TaskResource
    {
        // Check if the authenticated user is authorized to update the task
        $this->authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task);
    }

    /**
     *
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        // Check if the authenticated user is authorized to delete the task
        $this->authorize('delete', $task);

        // Delete the specified task
        $task->delete();

        // Return a response indicating successful deletion
        return response()->noContent();
    }
}
