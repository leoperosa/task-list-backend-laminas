<?php

namespace TaskList\Model;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Psr\Container\ContainerInterface;

class TaskTableFactory
{
    public function __invoke(ContainerInterface $container): TaskTable
    {
        $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Task());

        $tableGateway = new TableGateway('tasks', $dbAdapter, null, $resultSetPrototype);
        return new TaskTable($tableGateway);
    }
}