<?php

final class DatabaseConfig
{
    public static function get(): array
    {
        return [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_PORT'] ?? '3306',
            'username' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? '',
            'database' => $_ENV['DB_NAME'] ?? 'jojolos'
        ];
    }
}
