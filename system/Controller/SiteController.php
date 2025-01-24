<?php

namespace system\Controller;

use system\Core\Helpers;
use system\Core\Controlador;
use system\Model\SiteModel;


class SiteController extends Controlador
{
    public function __construct()
    {
        parent::__construct('templates/site/views');
    }

    public function index(): void
    {
        $registers = (new SiteModel())->busca();

        echo $this->template->renderizar('index.html', [
            'registers' => $registers->resultado(true),
        ]);

    }

    public function saveRegister(): void
    {
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($data)) {

            if ($this->dataValidation($data)) {

                $register = new siteModel();

                $register->cpf = Helpers::limparNumero($data['cpf']);
                $register->creci = $data['creci'];
                $register->name = $data['name'];

                if ($register->salvar()) {
                    $this->mensagem->sucesso("Corretor '{$data['name']}' cadastrado com sucesso!")->flash();
                    Helpers::redirecionar();
                } else {
                    $this->mensagem->erro(' | Erro ' . $register->erro())->flash();
                }
            }
        }
        echo $this->template->renderizar('index.html', [
            'registers' => (new SiteModel())->busca()->resultado(true),
            'formData' => $data
        ]);
    }



    public function selectRegisterToUpdate(int $id): void
    {
        $model = new SiteModel();
        echo $this->template->renderizar('index.html', [
            'registers' => $model->busca()->resultado(true),
            'formData' => $model->busca("id = '{$id}'")->resultado(),
        ]);

    }

    public function updateRegister(int $id): void
    {
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $data["id"] = $id;

        if (isset($data)) {

            if ($this->dataValidation($data)) {

                $register = new siteModel();

                $register->id = $data["id"];
                $register->cpf = Helpers::limparNumero($data['cpf']);
                $register->creci = $data['creci'];
                $register->name = $data['name'];

                if ($register->salvar()) {
                    $this->mensagem->sucesso("Corretor '{$data['name']}' cadastrado com sucesso!")->flash();
                    Helpers::redirecionar();
                } else {
                    $this->mensagem->erro(' | Erro ' . $register->erro())->flash();
                }
            }
        }
        echo $this->template->renderizar('index.html', [
            'registers' => (new SiteModel())->busca()->resultado(true),
            'formData' => $data
        ]);
    }

    public function deleteRegister(int $id): void
    {
        $mensagem = $this->mensagem;

        $register = (new SiteModel())->busca($id);
        if (!$register) {
            $mensagem->alerta('O corretor que você está tentando deletar não existe!')->flash();
        } elseif ((new SiteModel())->apagar("id = {$id}")) {
            $mensagem->sucesso("Registro do corretor {$register->name} deletado com sucesso!")->flash();
        } else {
            $mensagem->erro('Erro ao deletar o registro.')->flash();
        }

        Helpers::redirecionar();
    }



    public function dataValidation(array $data): bool{

        foreach (['cpf', 'creci', 'name'] as $field) {
            switch ($field) {
                case 'cpf':
                    if (empty($data['cpf'])) {
                        $this->mensagem->alerta('O CPF precisa ser informado!')->flash();
                        return false;
                    }
                    $cpf = Helpers::limparNumero($data['cpf']);
                    if (strlen($cpf) !== 11) {
                        $this->mensagem->alerta('O CPF deve ter exatamente 11 caracteres.')->flash();
                        return false;
                    }
                    if ($data['id']){

                        var_dump($data);
                        if (null !== (new SiteModel())->busca(termos: "cpf = '{$cpf}' AND id != '{$data['id']}'")->resultado(true)) {
                            $this->mensagem->alerta('O CPF informado ppertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    } else {
                        if (null !== (new SiteModel())->busca(termos: "cpf = '{$cpf}'")->resultado()) {
                            $this->mensagem->alerta('O CPF informado ppertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    }
                    break;

                case 'creci':
                    if (empty($data['creci'])) {
                        $this->mensagem->alerta('O CRECI precisa ser informado!')->flash();
                        return false;
                    }
                    if (strlen($data['creci']) < 2 || strlen($data['creci']) > 15) {
                        $this->mensagem->alerta('O CRECI deve ter entre 2 e 15 caracteres.')->flash();
                        return false;
                    }

                    if ($data['id']){
                        r();
                        if (null !== (new SiteModel())->busca(termos: "creci = '{$data['creci']}' AND id != '{$data['id']}'")->resultado(true)) {
                            $this->mensagem->alerta('O CRECI informado ppertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    } else {
                        if (null !==(new SiteModel())->busca(termos: "creci = '{$data['creci']}'")->resultado()) {
                            $this->mensagem->alerta('O CRECI informado pertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    }

                    break;

                case 'name':
                    if (empty($data['name'])) {
                        $this->mensagem->alerta('O NOME precisa ser informado!')->flash();
                        return false;
                    }
                    if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
                        $this->mensagem->alerta('O nome deve ter entre 2 e 100 caracteres.')->flash();
                        return false;
                    }
                    break;
            }
        }

        return true;

    }

    public function error404(): void
    {
        echo $this->template->renderizar('404.html', []);
    }

}
