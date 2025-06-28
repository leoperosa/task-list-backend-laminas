<?php
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use TaskList\Model\TaskTableFactory;
use TaskList\Model\TaskTable;

class TaskTableFactoryTest extends TestCase
{
    public function testShouldCreateTaskTableInstance()
    {
        $adapterMock = $this->createMock(AdapterInterface::class);
        $containerMock = $this->createMock(ContainerInterface::class);

        $containerMock->expects($this->once())
            ->method('get')
            ->with('Laminas\Db\Adapter\Adapter')
            ->willReturn($adapterMock);

        $factory = new TaskTableFactory();
        $taskTable = $factory($containerMock);

        $this->assertInstanceOf(TaskTable::class, $taskTable);
    }
}
