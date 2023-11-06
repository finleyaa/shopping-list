<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * Test that the index route returns a list of items.
     */
    public function testGetItems(): void
    {
        $this->user->items()->createMany([
            [
                'name' => 'Test Item 1',
                'price' => 100,
                'order' => 0
            ],
            [
                'name' => 'Test Item 2',
                'price' => 200,
                'order' => 1
            ],
            [
                'name' => 'Test Item 3',
                'price' => 300,
                'order' => 2
            ]
        ]);
        $secondUser = User::factory()->create();
        $secondUser->items()->createMany([
            [
                'name' => 'Test Item 4',
                'price' => 400,
                'order' => 0
            ],
            [
                'name' => 'Test Item 5',
                'price' => 500,
                'order' => 1
            ],
            [
                'name' => 'Test Item 6',
                'price' => 600,
                'order' => 2
            ]
        ]);

        $response = $this->actingAs($this->user)->get('/api/items');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'order',
                ]
            ],
            'message'
        ]);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test that the post items route creates a new item.
     */
    public function testAddItemCreatesItem(): void
    {
        $response = $this->actingAs($this->user)->post('/api/items', [
            'name' => 'Test Item',
            'price' => 100,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item',
            'price' => 100,
            'order' => 0
        ]);
    }

    /**
     * Test that the post items route assigns the item to the user.
     */
    public function testAddItemAssignsItemToUser(): void
    {
        $response = $this->actingAs($this->user)->post('/api/items', [
            'name' => 'Test User Item',
            'price' => 100,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('items', [
            'name' => 'Test User Item',
            'price' => 100,
            'order' => 0,
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test that the delete item route deletes an item.
     */
    public function testDeleteItemDeletesItem(): void
    {
        $item = $this->user->items()->create([
            'name' => 'Test Delete Item',
            'price' => 100,
            'order' => 0
        ]);

        $response = $this->actingAs($this->user)->delete('/api/items/' . $item->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('items', [
            'name' => 'Test Delete Item',
            'price' => 100,
            'order' => 0
        ]);
    }

    /**
     * Test that the patch item route updates an item and reorders.
     */
    public function testPatchItemUpdatesItemAndReorders(): void
    {
        $firstItem = $this->user->items()->create([
            'name' => 'Test Item 1',
            'price' => 100,
            'order' => 0
        ]);
        $this->user->items()->createMany([
            [
                'name' => 'Test Item 2',
                'price' => 200,
                'order' => 1
            ],
            [
                'name' => 'Test Item 3',
                'price' => 300,
                'order' => 2
            ]
        ]);

        $response = $this->actingAs($this->user)->patch('/api/items/' . $firstItem->id, [
            'name' => 'Test Item 1',
            'price' => 1000,
            'order' => 2,
            'purchased' => true
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item 1',
            'price' => 1000,
            'order' => 2,
            'purchased' => true
        ]);
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item 2',
            'price' => 200,
            'order' => 0
        ]);
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item 3',
            'price' => 300,
            'order' => 1
        ]);
    }
}
