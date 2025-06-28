<?php
use PHPUnit\Framework\TestCase;
use TaskList\Model\Task;

class TaskTest extends TestCase
{
    public function testShouldPopulatePropertiesUsingConstructor()
    {
        $data = [
            'id' => 1,
            'title' => 'Test Task',
            'description' => 'Test description',
            'created_at' => '2025-06-27',
            'status' => 'pending'
        ];

        $task = new Task($data);

        $this->assertEquals(1, $task->id);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Test description', $task->description);
        $this->assertEquals('2025-06-27', $task->created_at);
        $this->assertEquals('pending', $task->status);
    }

    public function testShouldUpdatePropertiesUsingExchangeArray()
    {
        $task = new Task();

        $task->exchangeArray([
            'id' => 2,
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'created_at' => '2025-06-26',
            'status' => 'in_progress'
        ]);

        $this->assertEquals(2, $task->id);
        $this->assertEquals('Updated Task', $task->title);
        $this->assertEquals('Updated description', $task->description);
        $this->assertEquals('2025-06-26', $task->created_at);
        $this->assertEquals('in_progress', $task->status);
    }

    public function testShouldReturnPropertiesAsArray()
    {
        $task = new Task([
            'id' => 3,
            'title' => 'Export Task',
            'description' => 'To array',
            'created_at' => '2025-06-25',
            'status' => 'closed'
        ]);

        $array = $task->getArrayCopy();

        $this->assertIsArray($array);
        $this->assertEquals(3, $array['id']);
        $this->assertEquals('Export Task', $array['title']);
        $this->assertEquals('To array', $array['description']);
        $this->assertEquals('2025-06-25', $array['created_at']);
        $this->assertEquals('closed', $array['status']);
    }
}
