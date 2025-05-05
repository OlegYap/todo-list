<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Services\Interfaces\TagServiceInterface;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private TaskServiceInterface $taskService;
    private TagServiceInterface $tagService;

    public function __construct(TaskServiceInterface $taskService, TagServiceInterface $tagService)
    {
        $this->taskService = $taskService;
        $this->tagService = $tagService;
    }


    public function index()
    {
        $user = Auth::user();
        $tasks = $this->taskService->getTasksForUser($user);
        $tags = $this->tagService->getAllTags();

        return view('dashboard', [
            'user' => $user,
            'tasks' => $tasks,
            'tags' => $tags,
        ]);
    }
}
