<?php

namespace system\Core;

use system\Core\Session;
use Exception;

/**
 * Class Helpers
 *
 * A utility class providing a variety of helper methods for common operations,
 * including validation, string manipulation, URL management, and environment checks.
 *
 * @package system\Core
 */
class Helpers
{
    /**
     * Checks if the application is running on a localhost server.
     *
     * @return bool Returns true if the server is localhost, false otherwise.
     */
    public static function localhost(): bool
    {
        $servidor = filter_input(INPUT_SERVER, 'SERVER_NAME');

        return $servidor === 'localhost';
    }

    /**
     * Retrieves and displays a flash message from the session, if available.
     *
     * @return string|null The flash message content or null if no message is found.
     */
    public static function flash(): ?string
    {
        $sessao = new Session();

        if ($flash = $sessao->flash()) {
            echo $flash;
        }

        return null;
    }

    /**
     * Redirects the user to a specified URL or the default application URL.
     *
     * @param string|null $url The target URL. Defaults to the application's base URL.
     * @return void
     */
    public static function redirect(string $url = null): void
    {
        header('HTTP/1.1 302 Found');

        $local = $url ? self::url($url) : self::url();

        header("Location: {$local}");
        exit();
    }

    /**
     * Removes all non-numeric characters from a string.
     *
     * @param string $numero The input string containing numbers and other characters.
     * @return string The sanitized string containing only numeric characters.
     */
    public static function clearNumber(string $numero): string
    {
        return preg_replace('/[^0-9]/', '', $numero);
    }

    /**
     * Generates a full URL based on the environment (development or production).
     *
     * @param string|null $url The relative URL path. Defaults to the base URL.
     * @return string The full URL.
     */
    public static function url(string $url = null): string
    {
        $servidor = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $ambiente = $servidor === 'localhost' ? URL_DEVELOPEMENT : URL_PRODUTION;

        if (str_starts_with($url, '/')) {
            return $ambiente . $url;
        }

        return $ambiente . '/' . $url;
    }

    /**
     * Applies a CPF mask to a numeric string.
     *
     * @param string $cpf The numeric string representing a CPF (Brazilian tax ID).
     * @return string The formatted CPF with the mask applied.
     */
    public static function maskCPF(string $cpf): string
    {
        $cpf = self::clearNumber($cpf);

        if (strlen($cpf) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }

        return $cpf;
    }

    /**
     * Validates a CPF (Brazilian tax ID) for format and integrity.
     *
     * @param string $cpf The CPF string to validate.
     * @throws Exception If the CPF is invalid or improperly formatted.
     * @return bool True if the CPF is valid.
     */
    public static function cpfValidation(string $cpf): bool
    {
        $cpf = self::clearNumber($cpf);

        if (mb_strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            throw new Exception('The CPF must have 11 digits.');
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                throw new Exception('Invalid CPF.');
            }
        }

        return true;
    }
}