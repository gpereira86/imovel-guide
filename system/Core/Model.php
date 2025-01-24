<?php

namespace system\Core;

use system\Core\Connection;
use system\Core\Message;


/**
 * Class Model
 *
 * Abstract class for interacting with the database.
 * Provides methods for CRUD operations and data handling.
 * Implements common patterns for filtering, querying, and managing data in a database.
 */
abstract class Model
{
    /**
     * @var \stdClass Dataset of the data.
     */
    protected $dataSet;

    /**
     * @var string SQL query string.
     */
    protected $query;

    /**
     * @var string|int Error code from the last operation.
     */
    protected $error;

    /**
     * @var array Query parameters.
     */
    protected $params;

    /**
     * @var string Table name.
     */
    protected $table;

    /**
     * @var string|null SQL ORDER BY clause.
     */
    protected $order;

    /**
     * @var string|null SQL LIMIT clause.
     */
    protected $limit;

    /**
     * @var string|null SQL OFFSET clause.
     */
    protected $offset;

    /**
     * @var Message Object for handling error or success messages.
     */
    protected $message;

    /**
     * Model constructor.
     *
     * Initializes the table name and the message object.
     *
     * @param string $table Table name.
     */
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->message = new Message();
    }

    /**
     * Sets the SQL ORDER BY clause for the query.
     *
     * @param string $order The ORDER BY clause.
     * @return $this The current instance of the class.
     */
    public function order(string $order)
    {
        $this->order = " ORDER BY {$order}";
        return $this;
    }

    /**
     * Sets the SQL LIMIT clause for the query.
     *
     * @param string $limit The LIMIT clause.
     * @return $this The current instance of the class.
     */
    public function limit(string $limit)
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * Sets the SQL OFFSET clause for the query.
     *
     * @param string $offset The OFFSET clause.
     * @return $this The current instance of the class.
     */
    public function offset(string $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * Retrieves the error code from the last operation.
     *
     * @return string|int The error code.
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Retrieves the message object containing error or success messages.
     *
     * @return Message The message object.
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Retrieves the dataset stored in the object.
     *
     * @return \stdClass The dataset.
     */
    public function data()
    {
        return $this->dataSet;
    }

    /**
     * Sets a property in the data set.
     *
     * @param string $name The property name.
     * @param mixed $value The value to set.
     */
    public function __set($name, $value)
    {
        if (empty($this->dataSet)) {
            $this->dataSet = new \stdClass();
        }

        $this->dataSet->$name = $value;
    }

    /**
     * Checks if a property exists in the data set.
     *
     * @param string $name The property name.
     * @return bool Returns true if the property exists.
     */
    public function __isset($name)
    {
        return isset($this->dataSet->$name);
    }

    /**
     * Retrieves a property from the data set.
     *
     * @param string $name The property name.
     * @return mixed The value of the property, or null if it does not exist.
     */
    public function __get($name)
    {
        return ($this->dataSet->$name ?? null);
    }

    /**
     * Performs a search in the database, with optional filtering.
     *
     * @param string|null $terms Filtering terms.
     * @param string|null $params Query parameters.
     * @param string $columns The columns to select.
     * @return $this The current instance of the class.
     */
    public function search(?string $terms = null, ?string $params = null, string $columns = '*')
    {
        if ($terms) {
            $this->query = "SELECT {$columns} FROM " . $this->table . " WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM " . $this->table;
        return $this;
    }

    /**
     * Retrieves the results of the query.
     *
     * @param bool $all If true, returns all results.
     * @return mixed Returns the results or null if no results found.
     */
    public function result(bool $all = false)
    {
        try {
            $stmt = Connection::getInstance()->prepare($this->query . $this->order . $this->limit . $this->offset);
            $stmt->execute($this->params);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (\PDOException $ex) {
            $this->error = $ex;
            return null;
        }
    }

    /**
     * Registers a new record in the database.
     *
     * @param array $dataSet Data to insert into the database.
     * @return int|null The ID of the new record, or null if an error occurred.
     */
    protected function register(array $dataSet)
    {
        try {
            $colunas = implode(',', array_keys($dataSet));
            $valores = ':' . implode(',:', array_keys($dataSet));
            $query = "INSERT INTO " . $this->table . "({$colunas}) VALUES ({$valores})";

            $stmt = Connection::getInstance()->prepare($query);
            $stmt->execute($this->dataFilter($dataSet));

            return Connection::getInstance()->lastInsertId();
        } catch (\PDOException $ex) {
            $this->error = $ex->getCode();
            return null;
        }
    }

    /**
     * Updates a record in the database.
     *
     * @param array $dataSet Data to update in the database.
     * @param string $terms Filtering terms for the update.
     * @return int|null The number of affected rows, or null if an error occurred.
     */
    protected function update(array $dataSet, string $terms)
    {
        try {
            $set = [];

            foreach ($dataSet as $key => $value) {
                $set[] = "{$key} =:{$key}";
            }

            $set = implode(', ', $set);

            $query = "UPDATE " . $this->table . " SET {$set} WHERE {$terms}";

            $stmt = Connection::getInstance()->prepare($query);
            $stmt->execute($this->dataFilter($dataSet));

            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $ex) {
            $this->error = $ex->getCode();
            return null;
        }
    }

    /**
     * Filters the data to prevent invalid values.
     *
     * @param array $dataSet Data to filter.
     * @return array The filtered data.
     */
    private function dataFilter(array $dataSet)
    {
        $filtered = [];

        foreach ($dataSet as $key => $value) {
            $filtered[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }

        return $filtered;
    }

    /**
     * Returns the data stored in the object as an array.
     *
     * @return array The data from the object.
     */
    protected function storage()
    {
        $dataSet = (array) $this->dataSet;

        return $dataSet;
    }

    /**
     * Searches for a record by its ID.
     *
     * @param int $id The ID of the record to search for.
     * @return $this The current instance of the class.
     */
    public function searchById(int $id)
    {
        $search = $this->search("id = {$id}");
        return $search->result();
    }

    /**
     * Deletes a record from the database.
     *
     * @param string $termos Filtering terms for the deletion.
     * @return bool|null Returns true if deletion was successful, or null if an error occurred.
     */
    public function delete(string $termos)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE {$termos}";

            $stmt = Connection::getInstance()->prepare($query);
            $stmt->execute();

            return true;
        } catch (\PDOException $ex) {
            $this->error = $ex->getCode();
            return null;
        }
    }

    /**
     * Returns the number of records found by the query.
     *
     * @return int The number of records found.
     */
    public function amount(): int
    {
        $stmt = Connection::getInstance()->prepare($this->query);
        $stmt->execute($this->params);

        return $stmt->rowCount();
    }

    /**
     * Saves the record in the database. Performs insert or update depending on the presence of an ID.
     *
     * @return bool Returns true if the operation was successful, or false if an error occurred.
     */
    public function save(): bool
    {
        if (empty($this->id)) {
            $id = $this->register($this->storage());
            if ($this->error) {
                $this->message->messageError('System error while trying to register data');
                return false;
            }
        }

        if (!empty($this->id)) {
            $id = $this->id;
            $this->update($this->storage(), "id = {$id}");
            if ($this->error) {
                $this->message->messageError('System error while trying to update data');
                return false;
            }
        }

        $this->dataSet = $this->searchById($id)->data();
        return true;
    }

    /**
     * Returns the next available ID for insertion.
     *
     * @return int The next available ID.
     */
    private function lastId(): int
    {
        return Connection::getInstance()->query("SELECT MAX(id) FROM " . $this->table)->fetchColumn() + 1;
    }
}