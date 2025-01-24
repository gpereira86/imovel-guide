<?php

namespace system\Core;

use PDO;
use PDOException;

/**
 * Conexão com o Banco de Dados
 *
 * @author Glauco Pereira <eu@glaucopereira.com>
 * @copyright Copyright (c) 2024, Glauco Pereira
 */
class Connection
{

    private static $instancia;
    
    /**
     * Conexão PDO Banco MySql usando constantes de system do arquivo de configurações
     * 
     * @return PDO
     */
    public static function getInstancia(): PDO
    {
        if (empty(self::$instancia)) {

            try {

                self::$instancia = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD, [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL
                ]);
            } catch (PDOException $ex) {

                die("Erro de Conexão >>> " . $ex->getMessage());
            }
        }
        return self::$instancia;
    }

    /**
     * Contrutor (proteção)
     */
    protected function __construct()
    {
        
    }

    /**
     * Clone (proteção)
     */
    private function __clone(): void
    {
        
    }
}
