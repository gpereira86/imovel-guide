<?php

namespace system\Core;

/**
 * Classe responsável por manipular a sessão de usuário.
 *
 * Esta classe oferece métodos para gerenciar sessões de usuário no contexto de uma aplicação web.
 * Ela permite criar, carregar, checar, limpar e deletar variáveis de sessão, além de fornecer um
 * mecanismo de flash message para comunicação temporária com o usuário.
 *
 * @author Glauco Pereira <eu@glaucopereira.com>
 * @copyright Copyright (c) 2024, Glauco Pereira
 */
class Sessao
{

    protected $contadorArray;


    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }

        if (!$this->checar('ip')) {
            $this->criar('ip', $this->obterIpUsuario());
        }

        if (!$this->checar('acao_contadores')) {
            $_SESSION['acao_contadores'] = [];
        }
    }

    public function criar(string $chave, mixed $valor): Sessao
    {
        $_SESSION[$chave] = $valor;
        return $this;
    }


    public function carregar(): ?object
    {
        return (object) $_SESSION;
    }


    public function checar(string $chave): bool
    {
        return isset($_SESSION[$chave]);
    }


    public function limpar(string $chave): Sessao
    {
        unset($_SESSION[$chave]);
        return $this;
    }


    public function deletar(): Sessao
    {
        session_destroy();
        return $this;
    }


    public function __get($atributo)
    {
        return $this->checar($atributo) ? $_SESSION[$atributo] : null;
    }

    public function flash(): ?Mensagem
    {
        if ($this->checar('flash')) {
            $flash = $this->flash;
            $this->limpar('flash');
            return $flash;
        }
        return null;
    }


    public function obterIpUsuario(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }


    public function obterIpSessao(): ?string
    {
        return $this->checar('ip') ? $_SESSION['ip'] : null;
    }


    public function incrementarAcao(string $acao): void
    {
        if (!isset($_SESSION['acao_contadores'][$acao])) {
            $_SESSION['acao_contadores'][$acao] = 0;
        }

        $_SESSION['acao_contadores'][$acao]++;
    }


    public function decrementarAcao(string $acao): void
    {
        if (isset($_SESSION['acao_contadores'][$acao]) && $_SESSION['acao_contadores'][$acao] >= 1) {
            $_SESSION['acao_contadores'][$acao]--;
        } else {
            $_SESSION['acao_contadores'][$acao] = 0;
        }
    }


    public function limiteAcoesAtingido(string $acao): bool
    {
        // Verifica se a ação existe no array de contadores antes de acessá-la
        return isset($_SESSION['acao_contadores'][$acao]) && $_SESSION['acao_contadores'][$acao] >= QTDE_PERMITIDA;
    }

    public function obterContadorAcao(string $acao): int
    {
        // Retorna 0 se a ação não estiver definida no array de contadores
        return $_SESSION['acao_contadores'][$acao] ?? 0;
    }
}
