<?php

class Model
{
    protected $_error;
    protected $_db;

    public function getError()
    {
        return $this->_error;
    }

    public function setDb(DBEngine $db)
    {
        $this->_db = $db;
    }
}
