<?php

namespace system\Core;

/**
 * Class Session
 *
 * Provides session management functionalities including session creation,
 * retrieval, checking, clearing, and handling user IP and flash messages.
 *
 * @package system\Core
 */
class Session
{
    /**
     * Session constructor.
     *
     * Initializes the session and ensures the user's IP address is stored
     * if not already present in the session.
     */
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }

        if (!$this->check('ip')) {
            $this->create('ip', $this->getUserIp());
        }
    }

    /**
     * Creates or updates a session variable.
     *
     * @param string $key   The name of the session variable.
     * @param mixed  $value The value to store in the session variable.
     * @return Session Returns the current session instance for method chaining.
     */
    public function create(string $key, mixed $value): Session
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * Checks if a session variable exists.
     *
     * @param string $key The name of the session variable to check.
     * @return bool Returns true if the session variable exists, false otherwise.
     */
    public function check(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Clears a specific session variable.
     *
     * @param string $key The name of the session variable to clear.
     * @return Session Returns the current session instance for method chaining.
     */
    public function clear(string $key): Session
    {
        unset($_SESSION[$key]);
        return $this;
    }

    /**
     * Destroys the current session, clearing all session data.
     *
     * @return Session Returns the current session instance for method chaining.
     */
    public function delete(): Session
    {
        session_destroy();
        return $this;
    }

    /**
     * Magic method to retrieve a session variable's value.
     *
     * @param string $attribute The name of the session variable.
     * @return mixed|null Returns the value of the session variable or null if not set.
     */
    public function __get($attribute)
    {
        return $this->check($attribute) ? $_SESSION[$attribute] : null;
    }

    /**
     * Retrieves and clears the flash message stored in the session.
     *
     * @return Message|null Returns the flash message if it exists, or null if not.
     */
    public function flash(): ?Message
    {
        if ($this->check('flash')) {
            $flash = $this->flash;
            $this->clear('flash');
            return $flash;
        }
        return null;
    }

    /**
     * Retrieves the user's IP address.
     *
     * @return string Returns the user's IP address.
     */
    public function getUserIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
     * Retrieves the IP address stored in the session.
     *
     * @return string|null Returns the IP address from the session, or null if not set.
     */
    public function getIpSession(): ?string
    {
        return $this->check('ip') ? $_SESSION['ip'] : null;
    }
}
