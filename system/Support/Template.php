<?php

namespace system\Support;

use Twig\Lexer;
use Twig\TwigFunction;
use system\Core\Helpers;
use system\Controller\UsuarioControlador;

/**
 * Classe Template para gerenciamento do Twig
 *
 * @author Glauco Pereira <eu@glaucopereira.com>
 * @copyright Copyright (c) 2024, Glauco Pereira
 */
class Template
{
    private \Twig\Environment $twig;

    /**
     * Construtor padrão para inicializar o Twig Template
     *
     * @param string $diretorio Caminho dos templates
     */
    public function __construct(string $diretorio)
    {
        $loader = new \Twig\Loader\FilesystemLoader($diretorio);
        $this->twig = new \Twig\Environment($loader);

        // Adiciona funções personalizadas
        $this->addHelpers();

        // Configuração opcional de Lexer (se necessário)
        $lexer = new Lexer($this->twig);
        $this->twig->setLexer($lexer);
    }

    /**
     * Renderiza uma view com os dados fornecidos
     *
     * @param string $view Nome da view
     * @param array $dados Dados para renderização
     * @return string HTML renderizado
     */
    public function renderizar(string $view, array $dados): string
    {
        return $this->twig->render($view, $dados);
    }

    /**
     * Adiciona funções personalizadas ao ambiente Twig
     *
     * @return void
     */
    private function addHelpers(): void
    {
        $functions = [
            new TwigFunction('url', fn(string $url = null) => Helpers::url($url)),
            new TwigFunction('saudacao', fn() => Helpers::saudacao()),
            new TwigFunction('flash', fn() => Helpers::flash()),
            new TwigFunction('tempoCarregamento', function () {
                $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
                return number_format($time, 4);
            }),
        ];

        foreach ($functions as $function) {
            $this->twig->addFunction($function);
        }
    }
}
