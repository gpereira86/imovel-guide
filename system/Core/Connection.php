<?php

namespace system\Core;

use PDO;
use PDOException;

/**
 * Class Connection
 *
 * Provides a singleton implementation for creating and managing a PDO database connection.
 * Ensures a single instance of the database connection is reused throughout the application.
 *
 * @package system\Core
 */
class Connection
{
    /**
     * @var PDO|null $instance
     * Holds the singleton instance of the PDO connection.
     */
    private static $instance;

    /**
     * Retrieves the singleton instance of the PDO connection.
     *
     * If the instance does not exist, it initializes the connection using the specified
     * database configuration constants: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD`.
     *
     * @return PDO The PDO instance representing the database connection.
     *
     * @throws PDOException If there is an error establishing the connection.
     */
    public static function getInstance(): PDO
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new PDO(
                    'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
                    DB_USER,
                    DB_PASSWORD,
                    [
                        PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES utf8",
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    ]
                );
            } catch (PDOException $ex) {
                die("Connection Error >>> " . $ex->getMessage());
            }
        }
        return self::$instance;
    }

    /**
     * Protected constructor to prevent instantiation.
     *
     * This ensures the singleton pattern is respected and the class cannot
     * be instantiated directly.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning.
     *
     * This ensures the singleton pattern is respected and the instance cannot
     * be duplicated.
     */
    private function __clone(): void
    {
    }
}