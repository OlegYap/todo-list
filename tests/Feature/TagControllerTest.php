<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
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

    public function test_user_can_get_all_tags(): void
    {
        // Create some tags
        Tag::factory()->count(3)->create();

        $response = $this->getJson('/api/tags?api_token=' . $this->apiToken);


        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_get_specific_tag(): void
    {
        $tag = Tag::factory()->create([
            'title' => 'Important'
        ]);

        $response = $this->getJson('/api/tags/' . $tag->id . '?api_token=' . $this->apiToken);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $tag->id,
                'title' => 'Important'
            ]);
    }

    public function test_user_can_create_tag(): void
    {
        $response = $this->postJson('/api/tags?api_token=' . $this->apiToken, [
            'title' => 'New Tag'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'New Tag'
            ]);

        $this->assertDatabaseHas('tags', [
            'title' => 'New Tag'
        ]);
    }

    public function test_tag_validation_rules(): void
    {
        $response = $this->postJson('/api/tags?api_token=' . $this->apiToken, [
            'title' => 'Ab' // Too short
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        $response = $this->postJson('/api/tags?api_token=' . $this->apiToken, [
            'title' => 'This tag title is too long and should fail validation'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_user_can_update_tag(): void
    {
        $tag = Tag::factory()->create([
            'title' => 'Original Title'
        ]);

        $response = $this->putJson('/api/tags/' . $tag->id . '?api_token=' . $this->apiToken, [
            'title' => 'Updated Title'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $tag->id,
                'title' => 'Updated Title'
            ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_user_can_delete_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->deleteJson('/api/tags/' . $tag->id . '?api_token=' . $this->apiToken);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_unauthorized_access_is_rejected(): void
    {
        $response = $this->getJson('/api/tags?api_token=invalid_token');
        $response->assertStatus(401);
    }
}
