<?php

namespace TaskList\Controller;

use TaskList\Controller\BaseApiController;
use Laminas\View\Model\JsonModel;
use TaskList\Model\Task;
use TaskList\Model\TaskTable;
use TaskList\Validator\TaskInputFilter;
use TaskList\Service\TaskRulesService;

class ApiController extends BaseApiController
{
    private TaskTable $table;
    private TaskRulesService $rules;

    public function __construct(TaskTable $table, TaskRulesService $rules)
    {
        $this->table = $table;
        $this->rules = $rules;
    }

    public function getList()
    {
        $queryParams = $this->params()->fromQuery();
        $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
        $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'status' => $queryParams['status'] ?? null,
            'title' => $queryParams['title'] ?? null,
        ];

        $tasks = $this->table->fetchAllWithFilters($filters, $limit, $offset);

        return new JsonModel([
            'page' => $page,
            'limit' => $limit,
            'tasks' => array_map(fn($task) => $task->getArrayCopy(), $tasks),
        ]);
    }    

    public function get($id)
    {
        $task = $this->table->getTask($id);
        return new JsonModel($task ? $task->getArrayCopy() : []);
    }

    public function create($data)
    {
        try {
            
            $data['status'] = 'pending';
            $inputFilter = new TaskInputFilter();
            $inputFilter->setData($data);
    
            if (!$inputFilter->isValid()) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(['messages' => $inputFilter->getMessages()]);
            }
    
            $this->rules->canCreateTask();
    
            $task = new Task();
            $task->exchangeArray($inputFilter->getValues());
            $this->table->saveTask($task);
    
            return new JsonModel(['message' => 'Task created successfully.']);
        } catch (\DomainException $e) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['message' => $e->getMessage()]);
        }

    }

    public function update($id, $data)
    {
        try {
            $task = $this->table->getTask($id);
            if (!$task) {
                $this->getResponse()->setStatusCode(404);
                return new JsonModel(['message' => 'Task not found.']);
            }
    
            $this->rules->canModifyTask($task);
    
            $inputFilter = new TaskInputFilter();
            $inputFilter->setData($data);
    
            if (!$inputFilter->isValid()) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(['messages' => $inputFilter->getMessages()]);
            }
    
            $task->exchangeArray(array_merge($task->getArrayCopy(), $inputFilter->getValues()));
            $this->table->saveTask($task);
    
            return new JsonModel(['message' => 'Task updated successfully.']);
        } catch (\DomainException $e) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['message' => $e->getMessage()]);
        }

    }

    public function updateStatusAction()
    {
        try {

            $id = (int) $this->params()->fromRoute('id');
            $data = json_decode($this->getRequest()->getContent(), true);

            $task = $this->table->getTask($id);
            if (!$task) {
                $this->getResponse()->setStatusCode(404);
                return new JsonModel(['message' => 'Task not found.']);
            }
    
            $this->rules->canModifyStatusTask($task);
        
            $task->status = $data['status'];
            $this->table->saveTask($task);
    
            return new JsonModel(['message' => 'Task updated successfully.']);
        } catch (\DomainException $e) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['message' => $e->getMessage()]);
        }

    }

    public function delete($id)
    {
        try {
            $task = $this->table->getTask($id);
            if (!$task) {
                $this->getResponse()->setStatusCode(404);
                return new JsonModel(['message' => 'Task not found.']);
            }

            $this->rules->canDeleteTask($task);

            $this->table->deleteTask($id);
            return new JsonModel(['message' => 'Task deleted successfully.']);

        } catch (\DomainException $e) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['message' => $e->getMessage()]);
        }
    }

    public function loginAction()
    {
        try {

            $data = json_decode($this->getRequest()->getContent(), true);
            if ($data['user'] == 'user' && $data['password'] == '1234') {
                return new JsonModel(['message' => 'Authenticated']);
            } else {
                $this->getResponse()->setStatusCode(401);
                return new JsonModel(['message' => 'Wrong username or password']);   
            }
    
        } catch (\DomainException $e) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['message' => $e->getMessage()]);
        }

    }
}
