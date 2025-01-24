<?php

namespace system\Support;

use Twig\Lexer;
use Twig\TwigFunction;
use system\Core\Helpers;

/**
 * Class Template
 *
 * Provides integration with the Twig templating engine, allowing rendering of templates
 * and the addition of custom helper functions for use within templates.
 *
 * @package system\Support
 */
class Template
{
    /**
     * @var \Twig\Environment The instance of the Twig environment.
     */
    private \Twig\Environment $twig;

    /**
     * Template constructor.
     *
     * Sets up the Twig environment, assigns the template directory, and registers custom helpers.
     *
     * @param string $diretorio Path to the directory containing Twig template files.
     */
    public function __construct(string $diretorio)
    {
        $loader = new \Twig\Loader\FilesystemLoader($diretorio);
        $this->twig = new \Twig\Environment($loader);

        $this->addHelpers();

        $lexer = new Lexer($this->twig);
        $this->twig->setLexer($lexer);
    }

    /**
     * Renders a template with the given data.
     *
     * @param string $view  The template file name to render.
     * @param array  $dataSet An associative array of data to be passed to the template.
     * @return string The rendered template content as a string.
     *
     * @throws \Twig\Error\LoaderError  If the template cannot be found.
     * @throws \Twig\Error\RuntimeError If an error occurs during template rendering.
     * @throws \Twig\Error\SyntaxError  If there is a syntax error in the template.
     */
    public function toRender(string $view, array $dataSet): string
    {
        return $this->twig->render($view, $dataSet);
    }

    /**
     * Registers custom helper functions for use within Twig templates.
     *
     * The following helpers are available:
     * - `url(string|null $url)`: Generates a complete URL using Helpers::url().
     * - `flash()`: Retrieves and displays flash messages using Helpers::flash().
     * - `maskCpf(string $cpf)`: Formats a CPF string using Helpers::maskCPF().
     * - `timeLoading()`: Calculates the script execution time in seconds.
     *
     * @return void
     */
    private function addHelpers(): void
    {
        $functions = [
            new TwigFunction('url', fn(string $url = null) => Helpers::url($url)),
            new TwigFunction('flash', fn() => Helpers::flash()),
            new TwigFunction('maskCpf', fn(string $cpf) => Helpers::maskCPF($cpf)),
            new TwigFunction('timeLoading', function () {
                $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
                return number_format($time, 4);
            }),
        ];

        foreach ($functions as $function) {
            $this->twig->addFunction($function);
        }
    }
}
