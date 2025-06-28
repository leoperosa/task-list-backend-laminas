<?php
namespace TaskListTest\Controller;

use PHPUnit\Framework\TestCase;
use TaskList\Controller\NotifyController;
use TaskList\Service\NotifierInterface;
use Laminas\View\Model\JsonModel;

class NotifyControllerTest extends TestCase
{
    public function testSendActionReturnsJsonModel()
    {
        $notifier = $this->createMock(NotifierInterface::class);
        $notifier->method('notify')->willReturn(['status' => 'sent']);
        $controller = new NotifyController($notifier);

        $result = $controller->sendAction();
        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals(['message' => 'Notification sent', 'result' => ['status' => 'sent']], $result->getVariables());
    }
}
