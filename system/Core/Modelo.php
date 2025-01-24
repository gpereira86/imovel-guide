<?php

namespace system\Core;

use system\Core\Connection;
use system\Core\Mensagem;

/**
 * Classe abstrata responsável por manipular operações com banco de dados através de modelos.
 *
 * Esta classe abstrata fornece métodos genéricos para realizar operações comuns de CRUD (Create, Read, Update, Delete)
 * em um banco de dados. Ela utiliza uma conexão com o banco de dados através da classe Connection e pode gerenciar mensagens
 * de erro através da classe Mensagem.
 *
 * @author Glauco Pereira <eu@glaucopereira.com>
 * @copyright Copyright (c) 2024, Glauco Pereira
 */
abstract class Modelo
{

    protected $dados;       // Armazena os dados do modelo
    protected $query;       // Armazena a query SQL construída
    protected $erro;        // Armazena erros ocorridos durante operações
    protected $parametros;  // Parâmetros para consultas preparadas
    protected $tabela;      // Nome da tabela do banco de dados
    protected $ordem;       // Cláusula de ordenação da query
    protected $limite;      // Cláusula de limite da query
    protected $offset;      // Cláusula de offset da query
    protected $mensagem;    // Instância da classe Mensagem para gerenciar mensagens

    /**
     * Construtor da classe Modelo.
     *
     * @param string $tabela O nome da tabela do banco de dados que este modelo manipula.
     */
    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;
        $this->mensagem = new Mensagem();
    }

    /**
     * Define a cláusula de ordenação da query.
     *
     * @param string $ordem A cláusula de ordenação da query.
     * @return Modelo Retorna a própria instância da classe Modelo para encadeamento de métodos.
     */
    public function ordem(string $ordem)
    {
        $this->ordem = " ORDER BY {$ordem}";
        return $this;
    }

    /**
     * Define a cláusula de limite da query.
     *
     * @param string $limite A cláusula de limite da query.
     * @return Modelo Retorna a própria instância da classe Modelo para encadeamento de métodos.
     */
    public function limite(string $limite)
    {
        $this->limite = " LIMIT {$limite}";
        return $this;
    }

    /**
     * Define a cláusula de offset da query.
     *
     * @param string $offset A cláusula de offset da query.
     * @return Modelo Retorna a própria instância da classe Modelo para encadeamento de métodos.
     */
    public function offset(string $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * Retorna o erro ocorrido durante uma operação.
     *
     * @return mixed Retorna o erro ocorrido durante uma operação.
     */
    public function erro()
    {
        return $this->erro;
    }

    /**
     * Retorna a instância da classe Mensagem para gerenciamento de mensagens.
     *
     * @return Mensagem Retorna a instância da classe Mensagem.
     */
    public function mensagem()
    {
        return $this->mensagem;
    }

    /**
     * Retorna os dados do modelo.
     *
     * @return mixed Retorna os dados do modelo.
     */
    public function dados()
    {
        return $this->dados;
    }

    /**
     * Define dinamicamente um atributo do modelo.
     *
     * @param string $nome O nome do atributo a ser definido.
     * @param mixed $valor O valor a ser atribuído ao atributo.
     */
    public function __set($nome, $valor)
    {
        if (empty($this->dados)) {
            $this->dados = new \stdClass();
        }

        $this->dados->$nome = $valor;
    }

    /**
     * Verifica se um atributo do modelo está definido.
     *
     * @param string $nome O nome do atributo a ser verificado.
     * @return bool Retorna true se o atributo estiver definido, false caso contrário.
     */
    public function __isset($nome)
    {
        return isset($this->dados->$nome);
    }

    /**
     * Retorna o valor de um atributo do modelo.
     *
     * @param string $nome O nome do atributo cujo valor será retornado.
     * @return mixed|null Retorna o valor do atributo se existir, ou null caso contrário.
     */
    public function __get($nome)
    {
        return ($this->dados->$nome ?? null);
    }

    /**
     * Inicia uma consulta no banco de dados com base nos parâmetros fornecidos.
     *
     * @param string|null $termos Os termos da consulta SQL WHERE.
     * @param string|null $parametros Os parâmetros da consulta preparada.
     * @param string $colunas As colunas a serem selecionadas na consulta.
     * @return Modelo Retorna a própria instância da classe Modelo para encadeamento de métodos.
     */
    public function busca(?string $termos = null, ?string $parametros = null, string $colunas = '*')
    {
        if ($termos) {
            $this->query = "SELECT {$colunas} FROM " . $this->tabela . " WHERE {$termos}";
            parse_str($parametros, $this->parametros);
            return $this;
        }

        $this->query = "SELECT {$colunas} FROM " . $this->tabela;
        return $this;
    }

    /**
     * Executa a query construída e retorna o resultado.
     *
     * @param bool $todos Indica se deve retornar todos os resultados (fetchAll) ou apenas um (fetchObject).
     * @return mixed|null Retorna o resultado da query conforme especificado, ou null em caso de erro.
     */
    public function resultado(bool $todos = false)
    {
        try {
            $stmt = Connection::getInstancia()->prepare($this->query . $this->ordem . $this->limite . $this->offset);
            $stmt->execute($this->parametros);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($todos) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (\PDOException $ex) {
            $this->erro = $ex;
            return null;
        }
    }

    /**
     * Método protegido para cadastrar novos registros no banco de dados.
     *
     * @param array $dados Os dados a serem cadastrados.
     * @return mixed|null Retorna o ID do registro inserido, ou null em caso de erro.
     */
    protected function cadastrar(array $dados)
    {
        try {
            $colunas = implode(',', array_keys($dados));
            $valores = ':' . implode(',:', array_keys($dados));
            $query = "INSERT INTO " . $this->tabela . "({$colunas}) VALUES ({$valores})";

            $stmt = Connection::getInstancia()->prepare($query);
            $stmt->execute($this->filtro($dados));

            return Connection::getInstancia()->lastInsertId();
        } catch (\PDOException $ex) {
            $this->erro = $ex->getCode();
            return null;
        }
    }

    /**
     * Método protegido para atualizar registros no banco de dados com base nos dados fornecidos e nos termos da atualização.
     *
     * @param array $dados Os dados a serem atualizados.
     * @param string $termos Os termos da atualização (cláusula WHERE).
     * @return int|null Retorna o número de linhas afetadas pela operação, ou null em caso de erro.
     */
    protected function atualizar(array $dados, string $termos)
    {
        try {
            $set = [];

            foreach ($dados as $chave => $valor) {
                $set[] = "{$chave} =:{$chave}";
            }

            $set = implode(', ', $set);

            $query = "UPDATE " . $this->tabela . " SET {$set} WHERE {$termos}";

            $stmt = Connection::getInstancia()->prepare($query);
            $stmt->execute($this->filtro($dados));

            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $ex) {
            $this->erro = $ex->getCode();
            return null;
        }
    }

    /**
     * Método privado para aplicar filtro de sanitização aos dados antes de serem inseridos no banco de dados.
     *
     * @param array $dados Os dados a serem filtrados.
     * @return array Retorna os dados filtrados.
     */
    private function filtro(array $dados)
    {
        $filtro = [];

        foreach ($dados as $chave => $valor) {
            $filtro[$chave] = (is_null($valor) ? null : filter_var($valor, FILTER_DEFAULT));
        }

        return $filtro;
    }

    /**
     * Método protegido para obter os dados do modelo em formato de array.
     *
     * @return array Retorna os dados do modelo em formato de array.
     */
    protected function armazenar()
    {
        $dados = (array) $this->dados;

        return $dados;
    }

    /**
     * Busca um registro no banco de dados pelo seu ID.
     *
     * @param int $id O ID do registro a ser buscado.
     * @return mixed|null Retorna o registro encontrado, ou null em caso de erro ou não encontrado.
     */
    public function buscaPorId(int $id)
    {
        $busca = $this->busca("id = {$id}");
        return $busca->resultado();
    }

    /**
     * Busca um registro no banco de dados pelo seu slug.
     *
     * @param string $slug O slug do registro a ser buscado.
     * @return mixed|null Retorna o registro encontrado, ou null em caso de erro ou não encontrado.
     */
    public function buscaPorSlug(string $slug)
    {
        $busca = $this->busca("slug = :s", "s={$slug}");
        return $busca->resultado();
    }

    /**
     * Apaga registros do banco de dados com base nos termos fornecidos.
     *
     * @param string $termos Os termos da exclusão (cláusula WHERE).
     * @return bool|null Retorna true se a operação for bem-sucedida, ou null em caso de erro.
     */
    public function apagar(string $termos)
    {
        try {
            $query = "DELETE FROM " . $this->tabela . " WHERE {$termos}";

            $stmt = Connection::getInstancia()->prepare($query);
            $stmt->execute();

            return true;
        } catch (\PDOException $ex) {
            Helpers::decrementarAcao($acao);
            $this->erro = $ex->getCode();
            return null;
        }
    }

    /**
     * Deleta o registro atual do modelo do banco de dados.
     *
     * @return bool Retorna true se o registro foi deletado com sucesso, false caso contrário.
     */
    public function deletar()
    {
        if (empty($this->id)) {
            return false;
        }

        $deletar = $this->apagar("id = {$this->id}");
        return $deletar;
    }

    /**
     * Retorna o número total de registros retornados pela última consulta.
     *
     * @return int Retorna o número total de registros.
     */
    public function total(): int
    {
        $stmt = Connection::getInstancia()->prepare($this->query);
        $stmt->execute($this->parametros);

        return $stmt->rowCount();
    }

    /**
     * Salvar os dados do modelo no banco de dados.
     *
     * Realiza a operação de cadastro se o ID do modelo estiver vazio, ou atualiza os dados se o ID estiver preenchido.
     *
     * @return bool Retorna true se os dados foram salvos com sucesso, false caso contrário.
     */
    public function salvar(): bool
{
        // CADASTRAR
        if (empty($this->id)) {
            $id = $this->cadastrar($this->armazenar());
            if ($this->erro) {
                $this->mensagem->erro('Erro de system ao tentar cadastrar os dados');
                return false;
            }
        }

        // ATUALIZAR
        if (!empty($this->id)) {
            $id = $this->id;
            $this->atualizar($this->armazenar(), "id = {$id}");
            if ($this->erro) {
                $this->mensagem->erro('Erro de system ao tentar atualizar os dados');
                return false;
            }
        }

        $this->dados = $this->buscaPorId($id)->dados();
        return true;

    }

    /**
     * Retorna o próximo ID disponível na tabela para ser utilizado como slug.
     *
     * @return int Retorna o próximo ID disponível.
     */
    private function ultimoId(): int
    {
        return Connection::getInstancia()->query("SELECT MAX(id) as maximo FROM {$this->tabela}")->fetch()->maximo + 1;
    }

    /**
     * Gera um slug único para o modelo, baseado no slug atual e no próximo ID disponível.
     */
    protected function slug()
    {
        $chegarSlug = $this->busca("slug = :s AND id != :id", "s={$this->slug}&id={$this->id}");

        if ($chegarSlug->total()) {
            $this->slug = "{$this->slug}-{$this->ultimoId()}";
        }
    }

    /**
     * Incrementa o contador de visitas e salva os dados do modelo.
     */
    public function salvarVisitas()
    {
        $this->visitas += 1;
        $this->ultima_visita_em = date('Y-m-d H:i:s');
        $this->salvar();
    }
}
