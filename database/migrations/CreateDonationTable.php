<?php

namespace database\migrations;

class CreateCharityTable
{
    public function up(\PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS charities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                representative_email VARCHAR(255) NOT NULL
            )
        ");
    }

    public function down(\PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS charities");
    }
}
