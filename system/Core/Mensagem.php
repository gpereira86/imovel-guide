<?php

namespace system\Core;

/**
 * Classe mensagem - resposável por exibir as mensagens do system.
 * 
 * @author Glauco Pereira <eu@glaucopereira.com>
 * @copyright Copyright (c) 2024, Glauco Pereira
 */
class Mensagem
{

    private $texto;
    private $css;

    /**
     * Converte em string para renderizar mensagem
     * 
     * @return type
     */
    public function __toString()
    {
        return $this->renderizar();
    }

    /**
     * Prepara texto para mensagem de sucesso
     * 
     * @param string $mensagem
     * @return Mensagem
     */
    public function sucesso(string $mensagem): Mensagem
    {
        $this->css = 'alert alert-success';
        $this->texto = $this->filtrar($mensagem);
        return $this;
    }

    /**
     * Prepara texto para mensagem de erro
     * 
     * @param string $mensagem
     * @return Mensagem
     */
    public function erro(string $mensagem): Mensagem
    {
        $this->css = 'alert alert-danger';
        $this->texto = $this->filtrar($mensagem);
        return $this;
    }

    /**
     * Prepara texto para mensagem de alerta

     * 
     * @param string $mensagem
     * @return Mensagem
     */
    public function alerta(string $mensagem): Mensagem
    {
        $this->css = 'alert alert-warning';
        $this->texto = $this->filtrar($mensagem);
        return $this;
    }

    /**
     * Prepara texto para mensagem de iinformação
     * 
     * @param string $mensagem
     * @return Mensagem
     */
    public function informa(string $mensagem): Mensagem
    {
        $this->css = 'container alert alert-primary';
        $this->texto = $this->filtrar($mensagem);
        return $this;
    }

    /**
     * Método responsável pela renderização
     * 
     * @return string
     */
    public function renderizar(): string
    {
        $botao = '<div class="col-auto"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

        return "<div class='{$this->css}'><div class='row d-flex justify-content-between align-items-center'><div class='col'>{$this->texto}</div> $botao</div></div>";
    }

    /**
     * Sanitiza uma string, convertendo caracteres especiais em entidades HTML.
     *
     * Esta função utiliza o filtro FILTER_SANITIZE_SPECIAL_CHARS para sanitizar
     * a string $mensagem, convertendo caracteres como <, >, & em suas entidades HTML
     * correspondentes, evitando ataques XSS (Cross-Site Scripting) ao exibir conteúdo
     * de usuários em páginas web.
     * 
     * @param string $mensagem
     * @return string
     */
    private function filtrar(string $mensagem): string
    {
        return filter_var($mensagem, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Gera mensagem flash
     * 
     * @return void
     */
    public function flash(): void
    {
        (new Sessao())->criar('flash', $this);
    }
}
