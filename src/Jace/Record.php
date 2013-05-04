<?php

namespace Jace;

abstract class Record
{
    protected static $_db = null;
    protected $_dummydb = null;
    protected $_tableName = 'table';
    protected $_data = [];

    public function __construct(\PDO $db)
    {
        static::$_db = $db;
        $this->_dummydb = new \Jace\Db($db);
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_data)) {
            $this->_data[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        } else {
            throw new \Exception("Specified column \"$name\" is not in the \"" . get_called_class() . "\"");
        }
    }

    /**
     *
     * @return int 主鍵
     */
    public function save()
    {
        if (!isset($this->_data['id'])) {
            return $this->_doInsert();
        } else {
            return $this->_doUpdate();
        }
    }

    public function quoteIdentifier($column)
    {
        return '`' . $column . '`';
    }

    protected function _doInsert()
    {
        $this->_data['id'] = $this->_dummydb->doInsert($this->_tableName, $this->_data);
    }

    protected function _doUpdate()
    {
        $dataID = $this->_data['id'];
        $this->_dummydb->doUpdate($this->_tableName, $this->_data, $dataID);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM ";
        $sql .= $this->quoteIdentifier($this->_tableName);
        $sql .= " WHERE id = ?";
        $stmt = static::$_db->prepare($sql);
        $stmt->execute([$id]);

        $this->_data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $this;
    }

    public function truncate()
    {
        static::$_db->exec('TRUNCATE TABLE ' . $this->quoteIdentifier($this->_tableName));
    }
}