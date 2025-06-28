<?php

namespace TaskList\Service;

use Psr\Container\ContainerInterface;
use Laminas\Mail\Transport\Sendmail;
use TaskList\Model\TaskTable;

class PendingTaskNotifierFactory
{
    public function __invoke(ContainerInterface $container): PendingTaskNotifier
    {
        $taskTable = $container->get(TaskTable::class);
        $mailer = new Sendmail();
        $recipient = 'email@domain.com'; 

        return new PendingTaskNotifier($taskTable, $mailer, $recipient);
    }
}
