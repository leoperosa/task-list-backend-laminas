<?php
use PHPUnit\Framework\TestCase;
use TaskList\Model\Task;
use TaskList\Model\TaskTable;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\ResultSet\ResultSet;

class TaskTableTest extends TestCase
{
    private $tableGateway;
    private TaskTable $taskTable;

    protected function setUp(): void
    {
        $this->tableGateway = $this->createMock(TableGatewayInterface::class);
        $this->taskTable = new TaskTable($this->tableGateway);
    }

    public function testShouldFetchAllTasks()
    {
        $this->tableGateway->expects($this->once())
            ->method('select')
            ->willReturn(['task1', 'task2']);

        $result = $this->taskTable->fetchAll();

        $this->assertEquals(['task1', 'task2'], $result);
    }

    public function testShouldReturnTaskById()
    {
        $task = new Task(['id' => 1, 'title' => 'Test']);
        $resultSet = $this->createMock(ResultSet::class);
        $resultSet->method('current')->willReturn($task);

        $this->tableGateway->method('select')->with(['id' => 1])->willReturn($resultSet);

        $result = $this->taskTable->getTask(1);
        $this->assertSame($task, $result);
    }

    public function testShouldReturnNullWhenTaskNotFound()
    {
        $resultSet = $this->createMock(ResultSet::class);
        $resultSet->method('current')->willReturn(null);

        $this->tableGateway->method('select')->with(['id' => 1])->willReturn($resultSet);

        $result = $this->taskTable->getTask(1);
        $this->assertNull($result);
    }

    public function testShouldInsertNewTaskWhenIdIsNull()
    {
        $task = new Task(['title' => 'New', 'description' => '...', 'status' => 'pending']);
        $this->tableGateway->expects($this->once())
            ->method('insert')
            ->with($this->callback(fn($data) => $data['title'] === 'New'));

        $this->taskTable->saveTask($task);
    }

    public function testShouldUpdateTaskWhenIdExists()
    {
        $task = new Task(['id' => 3, 'title' => 'Update', 'description' => '...', 'status' => 'pending']);
        $this->tableGateway->expects($this->once())
            ->method('update')
            ->with($this->arrayHasKey('title'), ['id' => 3]);

        $this->taskTable->saveTask($task);
    }

    public function testShouldDeleteTaskById()
    {
        $this->tableGateway->expects($this->once())
            ->method('delete')
            ->with(['id' => 5]);

        $this->taskTable->deleteTask(5);
    }
}
