<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//arquivo de configaração do system
//define fuso horário
date_default_timezone_set('America/Sao_Paulo');

//Informações do system
define('SITE_NAME', 'Imóvel Guide: Teste de Programação Glauco Pereira');
define('SITE_DESCRIPTION', 'Teste técnico Imóvel Guide');

define('SITE_URL', '/imovel-guide/');


//Urls do system
define('URL_PRODUTION', 'https://imovelguide.glaucopereira.com');
define('URL_DEVELOPMENT', 'http://localhost/imovel-guide');


define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'imovel_guide');
define('DB_USER', 'root');
define('DB_PASSWORD', '');


//define('DB_HOST', $_ENV['DB_HOST']);
//define('DB_PORT', $_ENV['DB_PORT']);
//define('DB_NAME', $_ENV['DB_NAME']);
//define('DB_USER', $_ENV['DB_USER']);
//define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
//
//define('SITE_URL', '/');