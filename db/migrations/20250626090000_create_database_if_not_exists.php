<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateDatabaseIfNotExists extends AbstractMigration
{
    public function up(): void
    {
        $this->execute("CREATE DATABASE IF NOT EXISTS taskdb_leonardo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    }

    public function down(): void
    {
        $this->execute("DROP DATABASE IF EXISTS taskdb_leonardo;");
    }
}
