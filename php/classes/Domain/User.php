<?php
/**
 * User domain class
 */
class Domain_User extends Domain_Abstract
{

    /**
    * @access private
    * @var string
    */
    private $_username;

    /**
    * @access private
    * @var string
    */
    private $_email;

    /**
    * @access private
    * @var timestamp
    */
    private $_created;

    /**
    * Setter for username
    *
    * @param string $username The name we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setUsername($username)
    {
        $this->_username = $username;
    }

    /**
    * Getter for username
    *
    * @access public
    *
    * @return string
    */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
    * Setter for emailaddress
    *
    * @param string $emailaddress The name we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setEmailAddress($emailAdddress)
    {
        $this->_emailAdddress = $emailAdddress;
    }

    /**
    * Getter for emailaddress
    *
    * @access public
    *
    * @return string
    */
    public function getEmailAddress()
    {
        return $this->_emailAdddress;
    }

    /**
    * Setter for created
    *
    * @param string $created The timestamp we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setCreated($created)
    {
        $this->_created = $created;
    }

    /**
    * Getter for created
    *
    * @access public
    *
    * @return string
    */
    public function getCreated()
    {
        return $this->_created;
    }

}