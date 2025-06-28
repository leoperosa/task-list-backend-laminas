<?php

namespace TaskList\Service;
use DomainException;

use TaskList\Model\Task;
use DateTime;

class TaskRulesService
{
    public function canCreateTask(): void
    {
        $day = (int) date('N'); 

        if ($day >= 6) {
            throw new DomainException('Tasks can only be created on weekdays.');
        }
    }

    public function canModifyTask(Task $task): void
    { 
        if ($task->status != 'pending') {
            throw new DomainException('Only pending tasks can be modified.');
        }
    }

    public function canModifyStatusTask(Task $task): void
    { 
        $allowedStatuses = ['pending', 'in_progress', 'closed'];
        if (!in_array($task->status, $allowedStatuses, true)) {
            throw new DomainException('Invalid status value.');
        }        
    }

    public function canDeleteTask(Task $task): void
    {
        if ($task->status !== 'pending') {
            throw new DomainException('Only pending tasks can be deleted.');
        }

        $createdAt = new \DateTime($task->created_at);
        $diff = $createdAt->diff(new \DateTime());

        if ($diff->days < 5) {
            throw new DomainException('Only tasks older than 5 days can be deleted.');
        }
    }    
}
