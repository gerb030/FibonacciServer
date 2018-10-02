<?php

abstract class Domain_Abstract
{
    /**
     * @var integer
     */
    protected $_id;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param integer $id
     * @throws InvalidArgumentException when attempting to overwrite a non-null value.
     */
    public function setId($id)
    {
        if (!is_null($this->_id)) {
            throw new InvalidArgumentException('ID is immutable');
        }
        $this->_id = $id;
    }
}
