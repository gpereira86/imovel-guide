<?php

namespace system\Core;

use system\Support\Template;

/**
 * Class Controller
 *
 * Represents a base controller in the application, providing access to common resources
 * such as the template rendering engine and message handling.
 *
 * @package system\Core
 */
class Controller
{
    /**
     * @var Template $template
     * The template rendering engine used for managing views.
     */
    protected Template $template;

    /**
     * @var Message $mensagem
     * The message handler used for displaying and managing system messages.
     */
    protected Message $mensagem;

    /**
     * Controller constructor.
     *
     * Initializes the base controller with a specified template directory
     * and prepares the message handling system.
     *
     * @param string $diretorio The directory where the template files are located.
     */
    public function __construct(string $diretorio)
    {
        $this->template = new Template($diretorio);
        $this->mensagem = new Message();
    }
}
