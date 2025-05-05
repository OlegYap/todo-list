@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Tasks</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                        Add Task
                    </button>
                </div>
                <div class="card-body">
                    <div id="tasks-container" class="sortable-tasks">
                        @if(count($tasks) > 0)
                            @foreach($tasks as $task)
                                <div class="card task-item" data-id="{{ $task->id }}" draggable="true">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title">
                                                {{ $task->title }}
                                            </h5>
                                            <div>
                                                <button class="btn btn-sm btn-primary edit-task" data-id="{{ $task->id }}">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-task" data-id="{{ $task->id }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                        <p class="card-text">{{ $task->text }}</p>
                                        <div class="task-tags">
                                            @foreach($task->tags as $tag)
                                                <span class="badge bg-secondary tag-badge">{{ $tag->title }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                You don't have any tasks yet. Click on "Add Task" to create your first task.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tags</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTagModal">
                        Add Tag
                    </button>
                </div>
                <div class="card-body">
                    <div id="tags-container">
                        @if(count($tags) > 0)
                            <div class="list-group">
                                @foreach($tags as $tag)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $tag->title }}
                                        <div>
                                            <button class="btn btn-sm btn-primary edit-tag" data-id="{{ $tag->id }}" data-title="{{ $tag->title }}">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-tag" data-id="{{ $tag->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                You don't have any tags yet. Click on "Add Tag" to create your first tag.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">API Token</h5>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $user->api_token }}" readonly id="apiToken">
                        <button class="btn btn-outline-secondary" type="button" id="copyTokenBtn">Copy</button>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Use this token to access the API.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="taskTitle" maxlength="20" required>
                            <div class="invalid-feedback">
                                Title must be between 3 and 20 characters
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="taskText" class="form-label">Description</label>
                            <textarea class="form-control" id="taskText" rows="3" maxlength="200" required></textarea>
                            <div class="invalid-feedback">
                                Description must be at most 200 characters
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="taskTags" class="form-label">Tags</label>
                            <select class="form-select" id="taskTags" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTaskBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm">
                        <input type="hidden" id="editTaskId">
                        <div class="mb-3">
                            <label for="editTaskTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTaskTitle" maxlength="20" required>
                            <div class="invalid-feedback">
                                Title must be between 3 and 20 characters
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskText" class="form-label">Description</label>
                            <textarea class="form-control" id="editTaskText" rows="3" maxlength="200" required></textarea>
                            <div class="invalid-feedback">
                                Description must be at most 200 characters
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskTags" class="form-label">Tags</label>
                            <select class="form-select" id="editTaskTags" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateTaskBtn">Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createTagModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createTagForm">
                        <div class="mb-3">
                            <label for="tagTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="tagTitle" maxlength="20" required>
                            <div class="invalid-feedback">
                                Title must be between 3 and 20 characters
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTagBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTagModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTagForm">
                        <input type="hidden" id="editTagId">
                        <div class="mb-3">
                            <label for="editTagTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTagTitle" maxlength="20" required>
                            <div class="invalid-feedback">
                                Title must be between 3 and 20 characters
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateTagBtn">Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this item?</p>
                    <input type="hidden" id="deleteItemId">
                    <input type="hidden" id="deleteItemType">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
