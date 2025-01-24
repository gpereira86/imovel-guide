<?php

namespace system\Model;

use system\Core\Modelo;


class SiteModel extends Modelo
{
    public function __construct()
    {
        parent::__construct('corretores');
    }

    public function salvar():bool
    {
        return parent::salvar();
    }
    
}
