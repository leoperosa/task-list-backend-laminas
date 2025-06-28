<?php

namespace TaskList\Model;

class Task
{
    public $id;
    public $title;
    public $description;
    public $created_at;
    public $status;

    public function __construct(array $data = [])
    {
        $this->exchangeArray($data);
    }

    public function exchangeArray(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->status = $data['status'] ?? null;
    }

    public function getArrayCopy(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'status' => $this->status,
        ];
    }
}
