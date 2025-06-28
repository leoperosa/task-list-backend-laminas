<?php
use PHPUnit\Framework\TestCase;
use TaskList\Controller\ApiController;
use TaskList\Model\Task;
use TaskList\Model\TaskTable;
use TaskList\Service\TaskRulesService;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\View\Model\JsonModel;

class ApiControllerTest extends TestCase
{
    private $tableMock;
    private $rulesMock;
    private $controller;

    protected function setUp(): void
    {
        $this->tableMock = $this->createMock(TaskTable::class);
        $this->rulesMock = $this->createMock(TaskRulesService::class);
        $this->controller = new ApiController($this->tableMock, $this->rulesMock);
    }

    public function testShouldReturnTaskById()
    {
        $task = new Task();
        $task->id = 2;
        $task->title = 'Task 2';

        $this->tableMock->method('getTask')->with(2)->willReturn($task);

        $result = $this->controller->get(2);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('Task 2', $result->getVariable('title'));
    }

    public function testShouldCreateTaskSuccessfully()
    {
        $this->rulesMock->expects($this->once())->method('canCreateTask');

        $data = [
            'title' => 'New Task',
            'description' => 'New description',
        ];

        $this->tableMock->expects($this->once())->method('saveTask');

        $result = $this->controller->create($data);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('Task created successfully.', $result->getVariable('message'));
    }

    public function testShouldReturnDomainErrorWhenCreatingTask()
    {
        $this->rulesMock->method('canCreateTask')->willThrowException(new DomainException("Invalid"));

        $data = [
            'title' => 'Tarefa',
            'description' => 'Descrição'
        ];

        $result = $this->controller->create($data);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('Invalid', $result->getVariable('message'));
    }

    public function testShouldUpdateTaskSuccessfully()
    {
        $task = new Task();
        $task->id = 1;

        $this->tableMock->method('getTask')->with(1)->willReturn($task);
        $this->rulesMock->expects($this->once())->method('canModifyTask');

        $this->tableMock->expects($this->once())->method('saveTask');

        $data = ['title' => 'Updated Title', 'description' => 'Some description','status' => 'pending'];

        $result = $this->controller->update(1, $data);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('Task updated successfully.', $result->getVariable('message'));
    }

    public function testShouldReturn404WhenUpdatingNonexistentTask()
    {
        $this->tableMock->method('getTask')->with(99)->willReturn(null);

        $result = $this->controller->update(99, ['title' => 'Anything']);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('Task not found.', $result->getVariable('message'));
    }

    public function testShouldDeleteTaskSuccessfully()
    {
        $task = new Task();
        $task->id = 1;

        $this->tableMock->method('getTask')->with(1)->willReturn($task);
        $this->rulesMock->expects($this->once())->method('canDeleteTask');
        $this->tableMock->expects($this->once())->method('deleteTask')->with(1);

        $result = $this->controller->delete(1);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('Task deleted successfully.', $result->getVariable('message'));
    }

    public function testShouldReturn404WhenDeletingNonexistentTask()
    {
        $this->tableMock->method('getTask')->with(404)->willReturn(null);

        $result = $this->controller->delete(404);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('Task not found.', $result->getVariable('message'));
    }
}
