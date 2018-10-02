<?php

abstract class Mapper_Abstract
{
    /**
     * @param array|null $data
     * @return Domain_Abstract
     */
    public function create(array $data = null) 
    {
        $obj = $this->_create();
        if ($data) {
            $obj = $this->populate($obj, $data);
        }
        return $obj;
    }

    /**
     * @param Domain_Abstract $obj
     */
    public function save(Domain_Abstract $obj)
    {
        if (is_null($obj->getId())) {
            $this->_insert($obj);
        } else {
            $this->_update($obj);
        }
    }

    /**
     * @param Domain_Abstract $obj
     */
    public function delete(Domain_Abstract $obj)
    {
        $this->_delete($obj);
    }

    /**
     * @abstract
     * @param Domain_Abstract $obj
     * @param array $data
     * @return Domain_Abstract
     */
    abstract public function populate(Domain_Abstract $obj, array $data);

    /**
     * @abstract
     * @return Domain_Abstract
     */
    abstract protected function _create();

    /**
     * @param Domain_Abstract $obj
     * @throws Exception_Http
     */
    protected function _update(Domain_Abstract $obj)
    {
        throw new Exception_Http('Not implemented', 500);
    }

    /**
     * @param Domain_Abstract $obj
     * @throws Exception_Http
     */
    protected function _insert(Domain_Abstract $obj)
    {
        throw new Exception_Http('Not implemented', 500);
    }

    /**
     * @param Domain_Abstract $obj
     * @throws Exception_Http
     */
    protected function _delete(Domain_Abstract $obj)
    {
        throw new Exception_Http('Not implemented', 500);
    }

    public static function escapeQueryParameter($variable)
    {
        return str_replace(array("%"), array(''), $variable);
    }

    public static function stripTags($variable)
    {
        return htmlspecialchars(strip_tags($variable), ENT_QUOTES);
    }
}
    