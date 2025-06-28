<?php

namespace TaskList\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;

class TaskTable
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchAllWithFilters(array $filters = [], int $limit = 10, int $offset = 0)
    {
        $select = $this->tableGateway->getSql()->select();

        if (!empty($filters['status'])) {
            $select->where(['status' => $filters['status']]);
        }

        if (!empty($filters['title'])) {
            $select->where->like('title', '%' . $filters['title'] . '%');
        }

        $select->limit($limit)->offset($offset);

        $resultSet = $this->tableGateway->selectWith($select);
        return iterator_to_array($resultSet);
    }

    public function getTask($id): ?Task
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id]);
        $row = $rowset->current();
        return $row ?: null;
    }

    public function saveTask(Task $task)
    {
        $data = $task->getArrayCopy();
        unset($data['id'], $data['created_at']);

        if ($task->id) {
            $this->tableGateway->update($data, ['id' => $task->id]);
        } else {
            $this->tableGateway->insert($data);
        }
    }

    public function deleteTask($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
