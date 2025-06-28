<?php

namespace TaskList\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use TaskList\Service\NotifierInterface;

class NotifyController extends AbstractActionController
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function sendAction()
    {
        $result = $this->notifier->notify();
        return new JsonModel(['message' => 'Notification sent', 'result' => $result]);
    }
}