<?php
namespace TaskList\Service;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;
use TaskList\Model\TaskTable;

class PendingTaskNotifier implements NotifierInterface
{
    private TaskTable $taskTable;
    private TransportInterface $mailer;
    private string $recipient;

    public function __construct(TaskTable $taskTable, TransportInterface $mailer)
    {
        $this->taskTable = $taskTable;
        $this->mailer = $mailer;
    }

    public function notify(): array
    {
        $tasks = $this->taskTable->fetchAllWithFilters(['status' => 'pending']);

        if (empty($tasks)) {
            return [];
        }

        $body = "Pending Tasks:\n\n";
        foreach ($tasks as $task) {
            $body .= "- {$task->title}: {$task->description}\n";
        }

        $message = new Message();
        $message->addTo('email@domain.com') 
            ->addFrom('no-reply@example.com')
            ->setSubject('Daily Pending Tasks Report')
            ->setBody($body);

        //$this->mailer->send($message);
        return ['body' => $body];
    }
}
