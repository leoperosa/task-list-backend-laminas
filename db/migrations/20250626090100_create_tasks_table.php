<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTasksTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('tasks', ['id' => true]) // ← isso já cria 'id' como PRIMARY KEY AUTO_INCREMENT
            ->addColumn('title', 'string')
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('status', 'string')
            ->create();
    }
}