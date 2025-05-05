$(document).ready(function() {
    const $apiToken = $("#apiToken");
    const $tasksContainer = $('#tasks-container');
    const $taskTitle = $('#taskTitle');
    const $taskText = $('#taskText');
    const $taskTags = $('#taskTags');
    const $editTaskTitle = $('#editTaskTitle');
    const $editTaskText = $('#editTaskText');
    const $editTaskTags = $('#editTaskTags');
    const $tagTitle = $('#tagTitle');
    const $editTagTitle = $('#editTagTitle');
    const $createTaskModal = $('#createTaskModal');
    const $editTaskModal = $('#editTaskModal');
    const $createTagModal = $('#createTagModal');
    const $editTagModal = $('#editTagModal');
    const $deleteConfirmModal = $('#deleteConfirmModal');
    const $tagsContainer = $('#tags-container');

    const apiToken = $apiToken.val();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    $('#copyTokenBtn').click(function() {
        const tokenInput = document.getElementById('apiToken');
        tokenInput.select();
        document.execCommand('copy');
        alert('API token copied to clipboard!');
    });

    $tasksContainer.sortable({
        items: '.task-item',
        cursor: 'move',
        opacity: 0.7,
        containment: 'parent',
        tolerance: 'pointer',
        update: function() {
            let tasks = [];
            $('.task-item').each(function(index) {
                tasks.push({
                    id: $(this).data('id'),
                    order: index + 1
                });
            });

            $.ajax({
                url: '/api/tasks/reorder',
                type: 'POST',
                data: {
                    _token: csrfToken,
                    tasks: tasks
                },
                success: function() {
                    console.log('Order updated');
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        }
    }).disableSelection();

    $('#saveTaskBtn').click(function() {
        const title = $taskTitle.val();
        const text = $taskText.val();
        const tags = $taskTags.val();

        if (title.length < 3 || title.length > 20) {
            $taskTitle.addClass('is-invalid');
            return;
        } else {
            $taskTitle.removeClass('is-invalid');
        }

        if (text.length > 200) {
            $taskText.addClass('is-invalid');
            return;
        } else {
            $taskText.removeClass('is-invalid');
        }

        $.ajax({
            url: '/api/tasks?api_token=' + apiToken,
            type: 'POST',
            data: JSON.stringify({
                title: title,
                text: text,
                tags: tags
            }),
            contentType: 'application/json',
            success: function(response) {
                const taskHtml = createTaskHtml(response);
                $tasksContainer.append(taskHtml);
                $createTaskModal.modal('hide');
                $('#createTaskForm')[0].reset();
            },
            error: function(error) {
                console.error('Error creating task:', error);
                alert('Error creating task. Please try again.');
            }
        });
    });

    $(document).on('click', '.edit-task', function() {
        const taskId = $(this).data('id');

        $.ajax({
            url: '/api/tasks/' + taskId + '?api_token=' + apiToken,
            type: 'GET',
            success: function(task) {
                $('#editTaskId').val(task.id);
                $editTaskTitle.val(task.title);
                $editTaskText.val(task.text);

                const tagIds = task.tags.map(tag => tag.id);
                $editTaskTags.val(tagIds);

                $editTaskModal.modal('show');
            },
            error: function(error) {
                console.error('Error fetching task:', error);
                alert('Error fetching task details. Please try again.');
            }
        });
    });

    $('#updateTaskBtn').click(function() {
        const taskId = $('#editTaskId').val();
        const title = $editTaskTitle.val();
        const text = $editTaskText.val();
        const tags = $editTaskTags.val();

        if (title.length < 3 || title.length > 20) {
            $editTaskTitle.addClass('is-invalid');
            return;
        } else {
            $editTaskTitle.removeClass('is-invalid');
        }

        if (text.length > 200) {
            $editTaskText.addClass('is-invalid');
            return;
        } else {
            $editTaskText.removeClass('is-invalid');
        }

        $.ajax({
            url: '/api/tasks/' + taskId + '?api_token=' + apiToken,
            type: 'PUT',
            data: JSON.stringify({
                title: title,
                text: text,
                tags: tags
            }),
            contentType: 'application/json',
            success: function(task) {
                const taskItem = $(`.task-item[data-id="${task.id}"]`);
                taskItem.replaceWith(createTaskHtml(task));

                $editTaskModal.modal('hide');
            },
            error: function(error) {
                console.error('Error updating task:', error);
                alert('Error updating task. Please try again.');
            }
        });
    });

    $(document).on('click', '.delete-task', function() {
        const taskId = $(this).data('id');
        $('#deleteItemId').val(taskId);
        $('#deleteItemType').val('task');
        $deleteConfirmModal.modal('show');
    });

    $('#saveTagBtn').click(function() {
        const title = $tagTitle.val();

        if (title.length < 3 || title.length > 20) {
            $tagTitle.addClass('is-invalid');
            return;
        } else {
            $tagTitle.removeClass('is-invalid');
        }

        $.ajax({
            url: '/api/tags?api_token=' + apiToken,
            type: 'POST',
            data: JSON.stringify({
                title: title
            }),
            contentType: 'application/json',
            success: function(tag) {
                const tagHtml = createTagHtml(tag);

                if ($tagsContainer.find('.list-group').length === 0) {
                    $tagsContainer.html('<div class="list-group"></div>');
                }

                $tagsContainer.find('.list-group').append(tagHtml);

                const tagOption = new Option(tag.title, tag.id);
                $taskTags.append(tagOption);
                $editTaskTags.append(tagOption.cloneNode(true));

                $createTagModal.modal('hide');
                $('#createTagForm')[0].reset();
            },
            error: function(error) {
                console.error('Error creating tag:', error);
                alert('Error creating tag. Please try again.');
            }
        });
    });

    $(document).on('click', '.edit-tag', function() {
        const tagId = $(this).data('id');
        const tagTitle = $(this).data('title');

        $('#editTagId').val(tagId);
        $editTagTitle.val(tagTitle);

        $editTagModal.modal('show');
    });

    $('#updateTagBtn').click(function() {
        const tagId = $('#editTagId').val();
        const title = $editTagTitle.val();

        if (title.length < 3 || title.length > 20) {
            $editTagTitle.addClass('is-invalid');
            return;
        } else {
            $editTagTitle.removeClass('is-invalid');
        }

        $.ajax({
            url: '/api/tags/' + tagId + '?api_token=' + apiToken,
            type: 'PUT',
            data: JSON.stringify({
                title: title
            }),
            contentType: 'application/json',
            success: function(tag) {
                const tagItem = $(`.edit-tag[data-id="${tag.id}"]`).closest('.list-group-item');
                tagItem.html(`
                ${tag.title}
                <div>
                    <button class="btn btn-sm btn-primary edit-tag" data-id="${tag.id}" data-title="${tag.title}">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-danger delete-tag" data-id="${tag.id}">
                        Delete
                    </button>
                </div>
                `);

                $taskTags.find(`option[value="${tag.id}"]`).text(tag.title);
                $editTaskTags.find(`option[value="${tag.id}"]`).text(tag.title);

                $editTagModal.modal('hide');
            },
            error: function(error) {
                console.error('Error updating tag:', error);
                alert('Error updating tag. Please try again.');
            }
        });
    });

    $(document).on('click', '.delete-tag', function() {
        const tagId = $(this).data('id');
        $('#deleteItemId').val(tagId);
        $('#deleteItemType').val('tag');
        $deleteConfirmModal.modal('show');
    });

    $('#confirmDeleteBtn').click(function() {
        const itemId = $('#deleteItemId').val();
        const itemType = $('#deleteItemType').val();

        let url = itemType === 'task' ? '/api/tasks/' : '/api/tags/';
        url += itemId + '?api_token=' + apiToken;

        $.ajax({
            url: url,
            type: 'DELETE',
            success: function() {
                if (itemType === 'task') {
                    $(`.task-item[data-id="${itemId}"]`).remove();

                    if ($tasksContainer.find('.task-item').length === 0) {
                        $tasksContainer.html(`
                        <div class="alert alert-info">
                            You don't have any tasks yet. Click on "Add Task" to create your first task.
                        </div>
                        `);
                    }
                } else {
                    $(`.edit-tag[data-id="${itemId}"]`).closest('.list-group-item').remove();

                    $taskTags.find(`option[value="${itemId}"]`).remove();
                    $editTaskTags.find(`option[value="${itemId}"]`).remove();

                    if ($tagsContainer.find('.list-group-item').length === 0) {
                        $tagsContainer.html(`
                        <div class="alert alert-info">
                            You don't have any tags yet. Click on "Add Tag" to create your first tag.
                        </div>
                        `);
                    }
                }

                $deleteConfirmModal.modal('hide');
            },
            error: function(error) {
                console.error(`Error deleting ${itemType}:`, error);
                alert(`Error deleting ${itemType}. Please try again.`);
            }
        });
    });

    function createTaskHtml(task) {
        let tagsHtml = '';

        task.tags.forEach(tag => {
            tagsHtml += `<span class="badge bg-secondary tag-badge">${tag.title}</span>`;
        });

        return `
        <div class="card task-item" data-id="${task.id}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-arrows-alt drag-handle"></i>
                        ${task.title}
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-primary edit-task" data-id="${task.id}">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-task" data-id="${task.id}">
                            Delete
                        </button>
                    </div>
                </div>
                <p class="card-text">${task.text}</p>
                <div class="task-tags">
                    ${tagsHtml}
                </div>
            </div>
        </div>
        `;
    }

    function createTagHtml(tag) {
        return `
        <div class="list-group-item d-flex justify-content-between align-items-center">
            ${tag.title}
            <div>
                <button class="btn btn-sm btn-primary edit-tag" data-id="${tag.id}" data-title="${tag.title}">
                    Edit
                </button>
                <button class="btn btn-sm btn-danger delete-tag" data-id="${tag.id}">
                    Delete
                </button>
            </div>
        </div>
        `;
    }
});
