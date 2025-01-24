<?php

namespace system\Core;

use system\Core\Sessao;
use Exception;

/**
 * A classe de funções gerais de tratamento e preparação de dados
 * 
 * @author Glauco Pereira <eu@glaucopereira.com>
 * @copyright Copyright (c) 2024, Glauco Pereira
 */
class Helpers
{

    /**
     * Validação de senha com parâmetros de qauntidade
     * 
     * @param string $senha
     * @return bool
     */
    public static function validarSenha(string $senha): bool
    {
        if (mb_strlen($senha) >= 6 && mb_strlen($senha) <= 50) {
            return true;
        }

        return false;
    }

    /**
     * Gerar senha hash 
     * 
     * @param string $senha
     * @return string
     */
    public static function gerarSenha(string $senha): string
    {
        $options = [
            'cost' => 10,
        ];
        return password_hash($senha, PASSWORD_DEFAULT, $options);
    }

    /**
     * Comparar senha hash com senha digitada
     * 
     * @param string $senha
     * @param string $hash
     * @return bool
     */
    public static function verificarSenha(string $senha, string $hash): bool
    {
        return password_verify($senha, $hash);
    }

    /**
     * construtor de mensagem flash
     * 
     * @return string|null
     */
    public static function flash(): ?string
    {
        $sessao = new Sessao();

        if ($flash = $sessao->flash()) {
            echo $flash;
        }

        return null;
    }

    /**
     * Redirecionamento com url amigável
     * 
     * @param string $url
     * @return void
     */
    public static function redirecionar(string $url = null): void
    {
        header('HTTP/1.1 302 found');

        $local = ($url ? self::url($url) : self::url());

        header("location: {$local}");
        exit();
    }

    /**
     * Valida número de CPF
     * 
     * @param string $cpf
     * @return bool
     */
    public static function validarCpf(string $cpf): bool
    {
        $cpf = self::limparNumero($cpf);

        if (mb_strlen($cpf) != 11 or preg_match('/(\d)\1{10}/', $cpf)) {
            throw new Exception('O CPF precisa ter 11 dígitos');
        }
        for ($t = 9;
                $t < 11;
                $t++) {
            for ($d = 0, $c = 0;
                    $c < $t;
                    $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                throw new Exception('CPF Inválido');
            } else {
                return true;
            }
        }
        return true;
    }

    /**
     * Limpar string substituindo os números por nada
     * 
     * @param string $numero
     * @return string
     */
    public static function limparNumero(string $numero): string
    {
        return preg_replace('/[^0-9]/', '', $numero);
    }

    /**
     * Gera URL amigável
     * 
     * @param string $string
     * @return string
     */
    public static function slug(string $string): string
    {
        $mapa['a'] = "ÁáãÃÉéÍíÓóÚúÀàÈèÌìÒòÙùÂâÊêÎîÔôÛûÄäËëÏïÖöÜüÇçÑñÝý!@#$%&!*_-+=:;,.?/|'~^°¨ªº´";
        $mapa['b'] = 'AaaAEeIiOoUuAaEeIiOoUuAaEeIiOoUuAaEeIiOoUuCcNnYy___________________________';

        $slug = strtr(utf8_decode($string), utf8_decode($mapa['a']), $mapa['b']);
        $slug = strip_tags(trim($slug));
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace(['-----', '----', '--', '-'], '-', $slug);

        return strtolower(utf8_decode($slug));
    }

    /**
     * Retorna a data no formato do windows
     * 
     * @return string
     */
    public static function dataAtual(): string
    {
        $diaMes = date('d');
        $diaSemana = date('w');
        $mes = date('n') - 1;
        $ano = date('Y');

        $nomesDiasDaSemana = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];

        $nomesDosMeses = [
            'janeiro',
            'fevereiro',
            'março',
            'abril',
            'maio',
            'junho',
            'julho',
            'agosto',
            'setembro',
            'outubro',
            'novembro',
            'dezembro'
        ];

        $dataFormatada = $nomesDiasDaSemana[$diaSemana] . ', ' . $diaMes . ' de ' . $nomesDosMeses[$mes] . ' de ' . $ano;

        return $dataFormatada;
    }

    /**
     * Monta URL de acordo com Ambiente desenvolvimento ou produção
     * 
     * @param string $url
     * @return string
     */
    public static function url(string $url = null): string
    {
        $servidor = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $ambiente = ($servidor == 'localhost' ? URL_DEVELOPMENT : URL_PRODUTION);

        if (str_starts_with($url, '/')) {
            return$ambiente . $url;
        }

        return$ambiente . '/' . $url;
    }

    /**
     * Retorna Verdadeiro ou falso para ambiente localhost
     * 
     * @return bool
     */
    public static function localhost(): bool
    {
        $servidor = filter_input(INPUT_SERVER, 'SERVER_NAME');

        if ($servidor == 'localhost') {
            return true;
        }
        return false;
    }

    /**
     * Valida URL
     * 
     * @param string $url
     * @return bool
     */
    public static function validarUrl(string $url): bool
    {
        if (mb_strlen($url) < 10) {
            return false;
        }
        if (!str_contains($url, '.')) {
            return false;
        }
        if (str_contains($url, 'http://') or str_contains($url, 'https://')) {
            return true;
        }
        return false;
    }

    /**
     * Validar url com Filtro
     * 
     * @param string $url
     * @return bool
     */
    public static function validarUrlComFiltro(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Valida endereço de e-mail
     * 
     * @param string $email
     * @return bool
     */
    public static function validarEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Conta o tempo decorrido de uma data
     * 
     * @param string $data
     * @return string
     */
    public static function contarTempo(string $data): string
    {
        $agora = strtotime(date('Y-m-d H:i:s'));
        $tempo = strtotime($data);
        $diferenca = $agora - $tempo;

        $segundos = $diferenca;
        $minutos = round($diferenca / 60);
        $horas = round($diferenca / 3600);
        $dias = round($diferenca / 86400);
        $semanas = round($diferenca / 604800);
        $meses = round($diferenca / 2419200);
        $anos = round($diferenca / 29030400);

        if ($segundos <= 60) {
            return 'agora';
        } elseif ($minutos <= 60) {
            return $minutos == 1 ? 'há 1 minuto' : 'há ' . $minutos . ' minutos';
        } elseif ($horas <= 24) {
            return $horas == 1 ? 'há 1 hora' : 'há ' . $horas . ' horas';
        } elseif ($dias <= 7) {
            return $dias == 1 ? 'ontem' : 'há ' . $dias . ' dias';
        } elseif ($semanas <= 4) {
            return $semanas == 1 ? 'há 1 semana' : 'há ' . $semanas . ' semanas';
        } elseif ($meses <= 12) {
            return $meses == 1 ? 'há 1 mês' : 'há ' . $meses . ' meses';
        } else {
            return $anos == 1 ? 'há 1 ano' : 'há ' . $anos . ' anos';
        }
    }

    /**
     * Formata um valor com ponto e com virgula
     * 
     * @param float $valor
     * @return string
     */
    public static function formatarValor(float $valor = null): string
    {
        return number_format(($valor ? $valor : 0), 2, ',', '.');
    }

    /**
     * Formata o número com pontos
     * 
     * @param string $numero
     * @return string
     */
    public static function formatarNumero(int $numero = null): string
    {
        return number_format(($numero ? $numero : 0), 0, '.', '.');
    }

    /**
     * Gera saldação de acordo com a hora
     * 
     * @return string
     */
    public static function saudacao(): string
    {
        $hora = date('H');

//    if ($hora >= 0 AND $hora <= 5) {
//        $saudacao = 'boa madrugada';
//    } elseif ($hora >= 6 AND $hora <= 12) {
//        $saudacao = 'bom dia';
//    } elseif ($hora >= 13 AND $hora <= 18) {
//        $saudacao = 'boa tarde';
//    } else {
//        $saudacao = 'boa noite';
//    }
//    switch ($hora) {
//        case $hora >= 0 AND $hora <= 5:
//            $saudacao = 'boa madrugada';
//            break;
//        case $hora >= 6 AND $hora <= 12:
//            $saudacao = 'bom dia';
//            break;
//        case $hora >= 13 AND $hora <= 18:
//            $saudacao = 'boa tarde';
//            break;
//        default:
//            $saudacao = 'boa noite';
//    }

        $saudacao = match (true) {
            $hora >= 0 and $hora <= 5 => 'boa madrugada',
            $hora >= 6 and $hora <= 12 => 'bom dia',
            $hora >= 13 and $hora <= 18 => 'boa tarde',
            default => 'boa noite'
        };

        return $saudacao;
    }

    /**
     * Resume um texto com limite definido em quantidade de caracteres
     * 
     * @param string $texto texto para resumir
     * @param int $limite quantidade de caracteres
     * @param string $continue opcional - o que deve ser exibido ao final do resumo
     * @return string texto resumido
     */
    public static function resumirTexto(string $texto, int $limite, string $continue = '...'): string
    {

        $textoLimpo = trim(strip_tags($texto));
        if (mb_strlen($textoLimpo) <= $limite) {
            return $textoLimpo;
        }

        $resumirTexto = mb_substr($textoLimpo, 0, mb_strrpos(mb_substr($textoLimpo, 0, $limite), ''));

        return $resumirTexto . $continue;
    }

    /**
     * Gera data em formato pt-br
     * 
     * @param string $data
     * @return string
     */
    public static function dataBr(string $data): string
    {
        return date('d/m/Y H:i:s', strtotime($data));
    }

    public static function validarAcao(string $acao): bool
    {
        $sessao = new Sessao();

        // Verifica se o limite de ações foi atingido para a ação específica
        if ($sessao->limiteAcoesAtingido($acao)) {
            return false;
        } else {
            // Incrementa o contador de ações para a ação específica
            $sessao->incrementarAcao($acao);
                        
            return true;
        }
    }
    
    /**
     * Obtém o contador de ações executadas por sessão.
     * 
     * Chamada:  contadorAcao($acao, $tipo)
     * 
     * Tipos:   'num' = retorna um inteiro com o contador da ação
     *          'msg' = retorna uma string com a mensagem de limite atingido ou quantas foram realizadas e quantas são permitidas
     * 
     * @param string $acao
     * @param string $tipo
     * @return int|string
     */
    public static function contadorAcao(string $acao, string $tipo='num'):int|string
    {
        $acao = $acao;
        $sessao = new Sessao();
        $contador = $sessao->obterContadorAcao($acao);
        $qtdPermitida = QTDE_PERMITIDA;
     
        if($tipo == 'num'){
            return $contador;
        }elseif($tipo == 'msg'){
            return $mensagem =($contador >= $qtdPermitida) ? 'Limite de '.$qtdPermitida.' inclusão/edição/exclusão atingido' : $contador . 'ª Inclusão/edição/exclusão dos(as) '.$qtdPermitida.' permitidos(as).';
        }else{
            return 'Erro interno: TIPO "INVÁLIDO", informe ao administrador do system.';
        }
        
    }
    
    public static function decrementarAcao(string $acao):int
    {
        $acao = $acao;
        $sessao = new Sessao();
        
        $sessao->decrementarAcao($acao);
        
        return $sessao->obterContadorAcao($acao);
    }
    
}
