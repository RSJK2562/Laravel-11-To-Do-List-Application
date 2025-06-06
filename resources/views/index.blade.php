@extends('layouts.app')

@section('title', 'To-Do List')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-6">
        <div class="todo-card p-4 p-md-5">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="header-gradient mb-2">
                    <i class="fas fa-tasks me-2"></i>
                    My To-Do List
                </h1>
                <p class="text-muted">Stay organized and productive</p>
            </div>
            
            <!-- Alert Container -->
            <div id="alert-container"></div>
            
            <!-- Add Task Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form id="task-form" class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <input 
                                type="text" 
                                id="task-input" 
                                class="form-control" 
                                placeholder="Enter a new task..."
                                autocomplete="off"
                                maxlength="255"
                                required
                            >
                        </div>
                        <button type="submit" class="btn btn-primary" id="add-btn">
                            <span class="btn-text">
                                <i class="fas fa-plus me-1"></i>
                                Add
                            </span>
                            <span class="btn-spinner d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Adding...
                            </span>
                        </button>
                    </form>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Press Enter to add a task
                    </small>
                </div>
            </div>
            
            <!-- Show All Tasks Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" id="tasks-heading">
                    <i class="fas fa-list-ul me-2"></i>
                    Active Tasks (<span id="task-count">{{ $tasks->count() }}</span>)
                </h5>
                <button type="button" class="btn btn-outline-info btn-sm" id="toggle-view-btn">
                    <i class="fas fa-eye me-1"></i>
                    <span id="toggle-text">Show All Tasks</span>
                </button>
            </div>
            
            <!-- Tasks Container -->
            <div id="tasks-container">
                @if($tasks->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($tasks as $task)
                            <div class="list-group-item task-item border-0 rounded-3 mb-2 p-3" data-task-id="{{ $task->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input 
                                            class="form-check-input task-checkbox" 
                                            type="checkbox" 
                                            data-task-id="{{ $task->id }}"
                                            {{ $task->completed ? 'checked' : '' }}
                                        >
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 task-title {{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}">
                                            {{ $task->title }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Created {{ $task->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger btn-sm delete-btn"
                                        data-task-id="{{ $task->id }}"
                                        data-task-title="{{ $task->title }}"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5" id="empty-state">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tasks yet</h5>
                        <p class="text-muted">Add your first task to get started!</p>
                    </div>
                @endif
            </div>
            
            <!-- Loading Spinner -->
            <div id="loading-spinner" class="text-center py-4 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const taskForm = document.getElementById('task-form');
    const taskInput = document.getElementById('task-input');
    const addBtn = document.getElementById('add-btn');
    const tasksContainer = document.getElementById('tasks-container');
    const toggleViewBtn = document.getElementById('toggle-view-btn');
    const toggleText = document.getElementById('toggle-text');
    const tasksHeading = document.getElementById('tasks-heading');
    const taskCount = document.getElementById('task-count');
    const loadingSpinner = document.getElementById('loading-spinner');
    
    let isShowingAll = false;
    
    // Initialize
    updateTaskCount();
    
    // Add Task Form Submission
    taskForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const title = taskInput.value.trim();
        if (!title) {
            showAlert('Please enter a task title', 'warning');
            return;
        }
        
        setButtonLoading(addBtn, true);
        
        try {
            const response = await apiCall('/tasks', {
                method: 'POST',
                body: JSON.stringify({ title }),
            });
            
            if (response.success) {
                taskInput.value = '';
                addTaskToDOM(response.task);
                showAlert(response.message);
                updateTaskCount();
                hideEmptyState();
            }
        } catch (error) {
            showAlert(error.message, 'danger');
        } finally {
            setButtonLoading(addBtn, false);
        }
    });
    
    // Toggle View (Show All / Show Active)
    toggleViewBtn.addEventListener('click', async function() {
        setLoading(tasksContainer, true);
        showLoadingSpinner(true);
        
        try {
            if (!isShowingAll) {
                // Show all tasks
                const response = await apiCall('/tasks/all');
                if (response.success) {
                    renderAllTasks(response.activeTasks, response.completedTasks);
                    toggleText.textContent = 'Show Active Only';
                    tasksHeading.innerHTML = '<i class="fas fa-list me-2"></i>All Tasks';
                    isShowingAll = true;
                }
            } else {
                // Show active only
                const response = await apiCall('/tasks/active');
                if (response.success) {
                    renderActiveTasks(response.tasks);
                    toggleText.textContent = 'Show All Tasks';
                    tasksHeading.innerHTML = '<i class="fas fa-list-ul me-2"></i>Active Tasks (<span id="task-count">' + response.tasks.length + '</span>)';
                    isShowingAll = false;
                }
            }
        } catch (error) {
            showAlert('Failed to load tasks', 'danger');
        } finally {
            setLoading(tasksContainer, false);
            showLoadingSpinner(false);
        }
    });
    
    // Event Delegation for Task Actions
    tasksContainer.addEventListener('change', async function(e) {
        if (e.target.classList.contains('task-checkbox')) {
            const taskId = e.target.dataset.taskId;
            const taskItem = e.target.closest('.task-item');
            
            setLoading(taskItem, true);
            
            try {
                const response = await apiCall(`/tasks/${taskId}/toggle`, {
                    method: 'PUT',
                });
                
                if (response.success) {
                    if (response.task.completed && !isShowingAll) {
                        // Remove from active view with animation
                        taskItem.classList.add('fade-out');
                        setTimeout(() => {
                            taskItem.remove();
                            updateTaskCount();
                            checkEmptyState();
                        }, 500);
                    } else {
                        // Update task appearance
                        updateTaskAppearance(taskItem, response.task);
                    }
                    showAlert(response.message);
                }
            } catch (error) {
                // Revert checkbox state
                e.target.checked = !e.target.checked;
                showAlert('Failed to update task', 'danger');
            } finally {
                setLoading(taskItem, false);
            }
        }
    });
    
    tasksContainer.addEventListener('click', async function(e) {
        if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const taskId = btn.dataset.taskId;
            const taskTitle = btn.dataset.taskTitle;
            const taskItem = btn.closest('.task-item');
            
            // Confirmation dialog
            if (!confirm(`Are you sure you want to delete this task?\n\n"${taskTitle}"`)) {
                return;
            }
            
            setLoading(taskItem, true);
            
            try {
                const response = await apiCall(`/tasks/${taskId}`, {
                    method: 'DELETE',
                });
                
                if (response.success) {
                    taskItem.classList.add('fade-out');
                    setTimeout(() => {
                        taskItem.remove();
                        updateTaskCount();
                        checkEmptyState();
                    }, 500);
                    showAlert(response.message);
                }
            } catch (error) {
                showAlert('Failed to delete task', 'danger');
            } finally {
                setLoading(taskItem, false);
            }
        }
    });
    
    // Helper Functions
    function setButtonLoading(button, loading) {
        const text = button.querySelector('.btn-text');
        const spinner = button.querySelector('.btn-spinner');
        
        if (loading) {
            text.classList.add('d-none');
            spinner.classList.remove('d-none');
            button.disabled = true;
        } else {
            text.classList.remove('d-none');
            spinner.classList.add('d-none');
            button.disabled = false;
        }
    }
    
    function showLoadingSpinner(show) {
        if (show) {
            loadingSpinner.classList.remove('d-none');
        } else {
            loadingSpinner.classList.add('d-none');
        }
    }
    
    function addTaskToDOM(task) {
        const taskHTML = createTaskHTML(task);
        const listGroup = tasksContainer.querySelector('.list-group');
        
        if (listGroup) {
            listGroup.insertAdjacentHTML('afterbegin', taskHTML);
        } else {
            tasksContainer.innerHTML = `<div class="list-group list-group-flush">${taskHTML}</div>`;
        }
        
        // Add fade-in animation
        const newTask = tasksContainer.querySelector(`[data-task-id="${task.id}"]`);
        newTask.classList.add('fade-in');
    }
    
    function createTaskHTML(task, showCompleted = false) {
        const completedClass = task.completed ? 'task-completed' : '';
        const titleClass = task.completed ? 'text-decoration-line-through text-muted' : '';
        const checked = task.completed ? 'checked' : '';
        const dateLabel = task.completed ? 'Completed' : 'Created';
        const date = task.completed ? (task.completed_at || task.created_at) : task.created_at;
        
        return `
            <div class="list-group-item task-item border-0 rounded-3 mb-2 p-3 ${completedClass}" data-task-id="${task.id}">
                <div class="d-flex align-items-center">
                    <div class="form-check me-3">
                        <input 
                            class="form-check-input task-checkbox" 
                            type="checkbox" 
                            data-task-id="${task.id}"
                            ${checked}
                        >
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 task-title ${titleClass}">
                            ${escapeHtml(task.title)}
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            ${dateLabel} ${date}
                        </small>
                    </div>
                    <button 
                        type="button" 
                        class="btn btn-outline-danger btn-sm delete-btn"
                        data-task-id="${task.id}"
                        data-task-title="${escapeHtml(task.title)}"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }
    
    function renderActiveTasks(tasks) {
        if (tasks.length === 0) {
            showEmptyState();
            return;
        }
        
        const tasksHTML = tasks.map(task => createTaskHTML(task)).join('');
        tasksContainer.innerHTML = `<div class="list-group list-group-flush">${tasksHTML}</div>`;
        updateTaskCountElement();
    }
    
    function renderAllTasks(activeTasks, completedTasks) {
        let html = '';
        
        if (activeTasks.length > 0) {
            html += '<h6 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>Active Tasks</h6>';
            html += '<div class="list-group list-group-flush mb-4">';
            html += activeTasks.map(task => createTaskHTML(task)).join('');
            html += '</div>';
        }
        
        if (completedTasks.length > 0) {
            html += '<h6 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Completed Tasks</h6>';
            html += '<div class="list-group list-group-flush">';
            html += completedTasks.map(task => createTaskHTML(task, true)).join('');
            html += '</div>';
        }
        
        if (activeTasks.length === 0 && completedTasks.length === 0) {
            showEmptyState();
            return;
        }
        
        tasksContainer.innerHTML = html;
    }
    
    function updateTaskAppearance(taskItem, task) {
        const checkbox = taskItem.querySelector('.task-checkbox');
        const title = taskItem.querySelector('.task-title');
        
        checkbox.checked = task.completed;
        
        if (task.completed) {
            taskItem.classList.add('task-completed');
            title.classList.add('text-decoration-line-through', 'text-muted');
        } else {
            taskItem.classList.remove('task-completed');
            title.classList.remove('text-decoration-line-through', 'text-muted');
        }
    }
    
    function updateTaskCount() {
        if (!isShowingAll) {
            updateTaskCountElement();
        }
    }
    
    function updateTaskCountElement() {
        const countElement = document.getElementById('task-count');
        if (countElement) {
            const activeTasks = tasksContainer.querySelectorAll('.task-item:not(.task-completed)');
            countElement.textContent = activeTasks.length;
        }
    }
    
    function checkEmptyState() {
        const tasks = tasksContainer.querySelectorAll('.task-item');
        if (tasks.length === 0) {
            showEmptyState();
        }
    }
    
    function showEmptyState() {
        tasksContainer.innerHTML = `
            <div class="text-center py-5" id="empty-state">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No tasks yet</h5>
                <p class="text-muted">Add your first task to get started!</p>
            </div>
        `;
    }
    
    function hideEmptyState() {
        const emptyState = document.getElementById('empty-state');
        if (emptyState) {
            emptyState.remove();
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endsection