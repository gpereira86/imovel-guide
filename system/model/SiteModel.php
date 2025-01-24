<?php

namespace system\Model;

use system\Core\Model;

/**
 * Class SiteModel
 *
 * Represents the model layer for the "corretores" table, providing database interaction
 * functionalities inherited from the base Model class.
 *
 * @package system\Model
 */
class SiteModel extends Model
{
    /**
     * SiteModel constructor.
     *
     * Initializes the model with the table name "corretores" by invoking the parent constructor.
     */
    public function __construct()
    {
        parent::__construct('corretores');
    }
}
