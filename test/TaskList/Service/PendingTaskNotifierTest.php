<?php
namespace TaskListTest\Service;

use PHPUnit\Framework\TestCase;
use TaskList\Service\PendingTaskNotifier;
use TaskList\Model\TaskTable;
use Laminas\Mail\Transport\TransportInterface;

class PendingTaskNotifierTest extends TestCase
{
    public function testShouldReturnEmailBodyWithPendingTasks()
    {
        $taskTable = $this->createMock(TaskTable::class);
        $taskTable->method('fetchAllWithFilters')->willReturn([
            (object)['title' => 'Test Task', 'description' => 'Do it']
        ]);
        $mailer = $this->createMock(TransportInterface::class);

        $notifier = new PendingTaskNotifier($taskTable, $mailer, 'test@domain.com');
        $result = $notifier->notify();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('body', $result);
        $this->assertStringContainsString('Test Task', $result['body']);
    }

    public function testShouldReturnEmptyArrayWhenNoPendingTasks()
    {
        $taskTable = $this->createMock(TaskTable::class);
        $taskTable->method('fetchAllWithFilters')->willReturn([]); // Nenhuma tarefa

        $mailer = $this->createMock(TransportInterface::class);

        $notifier = new PendingTaskNotifier($taskTable, $mailer, 'test@domain.com');
        $result = $notifier->notify();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

}
