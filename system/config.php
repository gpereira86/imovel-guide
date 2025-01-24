<?php

use Dotenv\Dotenv;
use system\Core\Helpers;

/**
 * Loads environment variables from a .env file.
 *
 * Uses the Dotenv library to create an immutable instance and load variables.
 */
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Sets the default timezone for the application.
 */
date_default_timezone_set('America/Sao_Paulo');

/**
 * Defines constants for the site name and description.
 */
define('SITE_NAME', 'Imóvel Guide: Teste de Programação Glauco Pereira');
define('SITE_DESCRIPTION', 'Teste técnico Imóvel Guide');

/**
 * Defines constants for production and development URLs.
 */
define('URL_PRODUTION', 'https://imovelguide.glaucopereira.com');
define('URL_DEVELOPEMENT', 'http://localhost/imovel-guide');

/**
 * Configures database and site URL constants based on the environment.
 *
 * If the application is running locally (as determined by Helpers::localhost()),
 * local database credentials and development site URL are set.
 * Otherwise, credentials and site URL are fetched from environment variables.
 */
if (Helpers::localhost()) {
    /**
     * Local database configuration.
     */
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'imovel_guide');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');

    /**
     * Local site URL.
     */
    define('SITE_URL', '/imovel-guide/');
} else {
    /**
     * Production database configuration.
     */
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_PORT', $_ENV['DB_PORT']);
    define('DB_NAME', $_ENV['DB_NAME']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

    /**
     * Production site URL.
     */
    define('SITE_URL', '/');
}
