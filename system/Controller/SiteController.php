<?php

namespace system\Controller;

use system\Model\RecaptchaValidator;
use system\Core\Helpers;
use system\Core\Controller;
use system\Model\SiteModel;

/**
 * Class SiteController
 *
 * Handles the logic for managing site-related operations such as displaying records,
 * saving, updating, deleting, and validating registers. It renders views using the
 * specified template system.
 *
 * @package system\Controller
 */
class SiteController extends Controller
{
    /**
     * SiteController constructor.
     *
     * Initializes the controller and sets the template directory.
     * Calls the parent constructor to set up the template system.
     */
    public function __construct()
    {
        parent::__construct('templates/site/views');
    }

    /**
     * Displays the index page with all registers.
     *
     * Fetches all registers from the SiteModel and renders the 'index.html' view.
     *
     * @return void
     */
    public function index(): void
    {
        $registers = (new SiteModel())->search();

        echo $this->template->toRender('index.html', [
            'registers' => $registers->result(true),
        ]);
    }

    /**
     * Saves a new register.
     *
     * Receives input data, validates it, and saves a new register into the database.
     * If the register is successfully saved, a success message is flashed; otherwise, an error message is displayed.
     *
     * @return void
     */
    public function saveRegister(): void
    {

        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);


        if (isset($data)) {

            if ($this->dataValidation($data)) {

                $register = new SiteModel();

                $register->cpf = Helpers::clearNumber($data['cpf']);
                $register->creci = $data['creci'];
                $register->name = $data['name'];

                if ($register->save()) {
                    $this->mensagem->success("Corretor '{$data['name']}' cadastrado com sucesso!")->flash();
                    Helpers::redirect();
                } else {
                    $this->mensagem->messageError(' | Erro ' . $register->error())->flash();
                }
            }
        }
        echo $this->template->toRender('index.html', [
            'registers' => (new SiteModel())->search()->result(true),
            'formData' => $data
        ]);
    }

    /**
     * Selects a register to update.
     *
     * Fetches the register by its ID. If the register does not exist, an error message is displayed.
     * Otherwise, it renders the 'index.html' view with the selected register's data.
     *
     * @param int $id The ID of the register to be updated.
     *
     * @return void
     */
    public function selectRegisterToUpdate(int $id): void
    {
        $model = new SiteModel();

        if(!$model->search("id = '{$id}'")->result()){
            $this->mensagem->messageError("Registro de id {$id} não existe.")->flash();
            Helpers::redirect();
        }

        echo $this->template->toRender('index.html', [
            'registers' => $model->search()->result(true),
            'formData' => $model->search("id = '{$id}'")->result(),
        ]);
    }

    /**
     * Updates an existing register.
     *
     * Validates the input data and updates the register in the database.
     * Displays appropriate success or error messages after the update.
     *
     * @param int $id The ID of the register to be updated.
     *
     * @return void
     */
    public function updateRegister(int $id): void
    {
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $data["id"] = $id;

        if (isset($data)) {

            if ($this->dataValidation($data)) {

                $register = new SiteModel();

                $register->id = $data["id"];
                $register->cpf = Helpers::clearNumber($data['cpf']);
                $register->creci = $data['creci'];
                $register->name = $data['name'];

                if ($register->save()) {
                    $this->mensagem->success("Corretor '{$data['name']}' atualizado com sucesso!")->flash();
                    Helpers::redirect();
                } else {
                    $this->mensagem->messageError(' | Erro ' . $register->error())->flash();
                }
            }
        }

        echo $this->template->toRender('index.html', [
            'registers' => (new SiteModel())->search()->result(true),
            'formData' => $data
        ]);
    }

    /**
     * Deletes a register.
     *
     * Deletes a register by its ID. If the register is not found, an error message is displayed.
     * If the deletion is successful, a success message is shown.
     *
     * @param int $id The ID of the register to be deleted.
     *
     * @return void
     */
    public function deleteRegister(int $id): void
    {
        $mensagem = $this->mensagem;

        $register = (new SiteModel())->search("id = {$id}")->result();
        $name = $register->name;

        if (!$register) {
            $mensagem->messageAlert('O corretor que você está tentando deletar não existe!')->flash();
        } elseif ((new SiteModel())->delete("id = {$id}")) {
            $mensagem->success("Registro do corretor {$name} deletado com sucesso!")->flash();
        } else {
            $mensagem->messageError('Erro ao deletar o registro.')->flash();
        }

        Helpers::redirect();
    }

    /**
     * Validates the input data for CPF, CRECI, and Name.
     *
     * Ensures that the provided data for CPF, CRECI, and Name meets the required format and constraints.
     * If validation fails, appropriate error messages are flashed.
     *
     * @param array $data The input data to be validated.
     *
     * @return bool Returns true if the data is valid, false otherwise.
     */
    public function dataValidation(array $data): bool
    {
        foreach (['cpf', 'creci', 'name'] as $field) {
            switch ($field) {
                case 'cpf':
                    if (empty($data['cpf'])) {
                        $this->mensagem->messageAlert('O CPF precisa ser informado!')->flash();
                        return false;
                    }
                    $cpf = Helpers::clearNumber($data['cpf']);
                    if (strlen($cpf) !== 11) {
                        $this->mensagem->messageAlert('O CPF deve ter exatamente 11 caracteres.')->flash();
                        return false;
                    }
                    if (isset($data['id'])){
                        if (null !== (new SiteModel())->search(terms: "cpf = '{$cpf}' AND id != '{$data['id']}'")->result(true)) {
                            $this->mensagem->messageAlert('O CPF informado pertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    } else {
                        if (null !== (new SiteModel())->search(terms: "cpf = '{$cpf}'")->result()) {
                            $this->mensagem->messageAlert('O CPF informado pertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    }
                    break;

                case 'creci':
                    if (empty($data['creci'])) {
                        $this->mensagem->messageAlert('O CRECI precisa ser informado!')->flash();
                        return false;
                    }
                    if (strlen($data['creci']) < 2 || strlen($data['creci']) > 15) {
                        $this->mensagem->messageAlert('O CRECI deve ter entre 2 e 15 caracteres.')->flash();
                        return false;
                    }

                    if (isset($data['id'])){
                        if (null !== (new SiteModel())->search(terms: "creci = '{$data['creci']}' AND id != '{$data['id']}'")->result(true)) {
                            $this->mensagem->messageAlert('O CRECI informado pertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    } else {
                        if (null !==(new SiteModel())->search(terms: "creci = '{$data['creci']}'")->result()) {
                            $this->mensagem->messageAlert('O CRECI informado pertence a outro registro, verifique o número e tente novamente.')->flash();
                            return false;
                        }
                    }

                    break;

                case 'name':
                    if (empty($data['name'])) {
                        $this->mensagem->messageAlert('O NOME precisa ser informado!')->flash();
                        return false;
                    }
                    if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
                        $this->mensagem->messageAlert('O nome deve ter entre 2 e 100 caracteres.')->flash();
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    /**
     * Renders a 404 error page.
     *
     * Displays a '404.html' view when a requested page is not found.
     *
     * @return void
     */
    public function error404(): void
    {
        echo $this->template->toRender('404.html', []);
    }
}
