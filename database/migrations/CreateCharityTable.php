<?php

namespace database\migrations;

class CreateDonationTable
{
    public static function up(\PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS donations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                donor_name VARCHAR(255) NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                charity_id INT NOT NULL,
                date_time DATETIME NOT NULL,
                FOREIGN KEY (charity_id) REFERENCES charities(id)
            )
        ");
    }

    public static function down(\PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS donations");
    }
}
