<?php

namespace system\Core;

/**
 * Class Message
 *
 * This class is responsible for creating and rendering styled messages, such as success, error, warning,
 * and notifications, using Bootstrap classes. It provides methods for filtering and sanitizing message content
 * and includes support for flash messaging through session storage.
 */
class Message
{
    /**
     * @var string The text content of the message.
     */
    private $text;

    /**
     * @var string The CSS classes used to style the message.
     */
    private $css;

    /**
     * Converts the Message object to a string representation.
     *
     * @return string The rendered message HTML.
     */
    public function __toString()
    {
        return $this->messageRender();
    }

    /**
     * Sets the message as a success message.
     *
     * @param string $message The success message text.
     * @return $this The current Message instance for method chaining.
     */
    public function success(string $message): Message
    {
        $this->css = 'alert alert-success';
        $this->text = $this->messageFilter($message);
        return $this;
    }

    /**
     * Sets the message as an error message.
     *
     * @param string $message The error message text.
     * @return $this The current Message instance for method chaining.
     */
    public function messageError(string $message): Message
    {
        $this->css = 'alert alert-danger';
        $this->text = $this->messageFilter($message);
        return $this;
    }

    /**
     * Sets the message as a warning message.
     *
     * @param string $message The warning message text.
     * @return $this The current Message instance for method chaining.
     */
    public function messageAlert(string $message): Message
    {
        $this->css = 'alert alert-warning';
        $this->text = $this->messageFilter($message);
        return $this;
    }

    /**
     * Sets the message as a notification message.
     *
     * @param string $message The notification message text.
     * @return $this The current Message instance for method chaining.
     */
    public function messageNotify(string $message): Message
    {
        $this->css = 'container alert alert-primary';
        $this->text = $this->messageFilter($message);
        return $this;
    }

    /**
     * Renders the message as an HTML string.
     *
     * Includes a dismiss button for messages styled with Bootstrap.
     *
     * @return string The rendered HTML for the message.
     */
    public function messageRender(): string
    {
        $button = '<div class="col-auto"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

        return "<div class='{$this->css}'><div class='row d-flex justify-content-between align-items-center'><div class='col'>{$this->text}</div> $button</div></div>";
    }

    /**
     * Filters and sanitizes the message text to prevent XSS attacks.
     *
     * @param string $message The raw message text.
     * @return string The sanitized message text.
     */
    private function messageFilter(string $message): string
    {
        return filter_var($message, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Stores the current message in the session as a flash message.
     *
     * Flash messages are temporary and meant to be displayed once.
     *
     * @return void
     */
    public function flash(): void
    {
        (new Session())->create('flash', $this);
    }
}