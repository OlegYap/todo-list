<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $apiToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->apiToken = $this->user->generateApiToken();
    }

    public function test_user_can_get_all_tasks(): void
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/tasks?api_token=' . $this->apiToken);

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_get_specific_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Task',
            'text' => 'Task Description'
        ]);

        $response = $this->getJson('/api/tasks/' . $task->id . '?api_token=' . $this->apiToken);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => 'Test Task',
                'text' => 'Task Description'
            ]);
    }

    public function test_user_cannot_get_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->getJson('/api/tasks/' . $task->id . '?api_token=' . $this->apiToken);

        $response->assertStatus(404);
    }

    public function test_user_can_create_task(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->postJson('/api/tasks?api_token=' . $this->apiToken, [
            'title' => 'New Task',
            'text' => 'This is a new task',
            'tags' => [$tag->id]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'New Task',
                'text' => 'This is a new task'
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'text' => 'This is a new task',
            'user_id' => $this->user->id
        ]);

        $this->assertDatabaseHas('task_tag', [
            'task_id' => $response->json('id'),
            'tag_id' => $tag->id
        ]);
    }

    public function test_task_validation_rules(): void
    {
        $response = $this->postJson('/api/tasks?api_token=' . $this->apiToken, [
            'title' => 'A',
            'text' => 'Description'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        $response = $this->postJson('/api/tasks?api_token=' . $this->apiToken, [
            'title' => 'This title is too long and should fail validation',
            'text' => 'Description'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'text' => 'Original Text'
        ]);

        $tag = Tag::factory()->create();

        $response = $this->putJson('/api/tasks/' . $task->id . '?api_token=' . $this->apiToken, [
            'title' => 'Updated Title',
            'text' => 'Updated Text',
            'tags' => [$tag->id]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => 'Updated Title',
                'text' => 'Updated Text'
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'text' => 'Updated Text'
        ]);

        $this->assertDatabaseHas('task_tag', [
            'task_id' => $task->id,
            'tag_id' => $tag->id
        ]);
    }

    public function test_user_cannot_update_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->putJson('/api/tasks/' . $task->id . '?api_token=' . $this->apiToken, [
            'title' => 'Updated Title',
            'text' => 'Updated Text'
        ]);

        $response->assertStatus(404);
    }

    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->deleteJson('/api/tasks/' . $task->id . '?api_token=' . $this->apiToken);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->deleteJson('/api/tasks/' . $task->id . '?api_token=' . $this->apiToken);

        $response->assertStatus(404);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_unauthorized_access_is_rejected(): void
    {
        $response = $this->getJson('/api/tasks?api_token=invalid_token');
        $response->assertStatus(401);
    }

    public function test_user_can_reorder_tasks(): void
    {
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'order' => 0
        ]);

        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'order' => 1
        ]);

        $response = $this->postJson('/api/tasks/reorder?api_token=' . $this->apiToken, [
            'tasks' => [
                ['id' => $task1->id, 'order' => 1],
                ['id' => $task2->id, 'order' => 0]
            ]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task1->id,
            'order' => 1
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task2->id,
            'order' => 0
        ]);
    }
}
