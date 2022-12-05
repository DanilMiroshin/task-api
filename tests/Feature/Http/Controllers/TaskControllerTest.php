<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\Statuses;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Throwable;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected array $jsonStructure = [
        'id',
        'name',
        'status',
        'created_at',
        'updated_at',
    ];


    /**
     * @throws Throwable
     */
    public function test_index(): void
    {
        $response = $this->json('GET', route('tasks.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'tasks' => [
                    '*' => $this->jsonStructure
                ]
            ]);

        $data = $response->decodeResponseJson();
        $this->assertEquals(1000, $data['total_tasks']);
    }

    /**
     * @throws Throwable
     */
    public function test_store_and_update(): void
    {
        // Без статуса
        $response = $this->postJson(route('tasks.store'), [
            'name' => 'Test new task',
        ])
            ->assertStatus(200);

        $task = $response->json('task');

        // Обновление
        $name = 'Updated task';
        $response = $this->put(route('tasks.update', $task['id']), [
            'name' => $name,
            'status' => 2,
        ]);

        $updatedTask = $response->json('task');

        $this->assertEquals($name, $updatedTask['name']);
        $this->assertEquals(Statuses::COMPLETED->value, $updatedTask['status']);
    }

    /**
     * @throws Throwable
     */
    public function test_store_and_destroy(): void
    {
        // Без статуса
        $response = $this->postJson(route('tasks.store'), [
            'name' => 'Test new task',
        ])
            ->assertStatus(200);

        $taskWithNewStatus = $response->json('task');

        $this->assertEquals(Statuses::NEW->value, $taskWithNewStatus['status']);

        // Со статусом
        $response = $this->postJson(route('tasks.store'), [
            'name' => 'Test completed task',
            'status' => 2,
        ])
            ->assertStatus(200);

        $taskWithCompletedStatus = $response->json('task');

        $this->assertEquals(Statuses::COMPLETED->value, $taskWithCompletedStatus['status']);

        // Удаление
        $this->delete(route('tasks.destroy', $taskWithNewStatus['id']))
            ->assertStatus(204);

        $this->delete(route('tasks.destroy', $taskWithCompletedStatus['id']))
            ->assertStatus(204);
    }
}
