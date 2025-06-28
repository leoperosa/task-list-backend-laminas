<?php
use PHPUnit\Framework\TestCase;
use TaskList\Service\TaskRulesService;
use TaskList\Model\Task;

class TaskRulesServiceTest extends TestCase
{
    private TaskRulesService $service;

    protected function setUp(): void
    {
        $this->service = new TaskRulesService();
    }

    public function testShouldAllowTaskCreationDuringWeekdays()
    {
        $this->expectNotToPerformAssertions();
        $day = (int) date('N');
        if ($day >= 6) {
            $this->markTestSkipped('Test only runs on weekdays.');
        }

        $this->service->canCreateTask();
    }

    public function testShouldAllowModificationWhenTaskStatusIsPending()
    {
        $task = new Task();
        $task->status = 'pending';

        $this->expectNotToPerformAssertions();
        $this->service->canModifyTask($task);
    }

    public function testShouldDenyModificationWhenTaskStatusIsNotPending()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Only pending tasks can be modified.');

        $task = new Task();
        $task->status = 'closed';

        $this->service->canModifyTask($task);
    }

    public function testShouldAllowStatusChangeWhenStatusIsValid()
    {
        $task = new Task();
        $task->status = 'in_progress';

        $this->expectNotToPerformAssertions();
        $this->service->canModifyStatusTask($task);
    }

    public function testShouldDenyStatusChangeWhenStatusIsInvalid()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid status value.');

        $task = new Task();
        $task->status = 'paused';

        $this->service->canModifyStatusTask($task);
    }

    public function testShouldAllowTaskDeletionWhenPendingAndOlderThanFiveDays()
    {
        $task = new Task();
        $task->status = 'pending';
        $task->created_at = (new DateTime('-6 days'))->format('Y-m-d');

        $this->expectNotToPerformAssertions();
        $this->service->canDeleteTask($task);
    }

    public function testShouldDenyTaskDeletionWhenStatusIsNotPending()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Only pending tasks can be deleted.');

        $task = new Task();
        $task->status = 'closed';
        $task->created_at = (new DateTime('-10 days'))->format('Y-m-d');

        $this->service->canDeleteTask($task);
    }

    public function testShouldDenyTaskDeletionWhenTaskIsYoungerThanFiveDays()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Only tasks older than 5 days can be deleted.');

        $task = new Task();
        $task->status = 'pending';
        $task->created_at = (new DateTime('-2 days'))->format('Y-m-d');

        $this->service->canDeleteTask($task);
    }
}
