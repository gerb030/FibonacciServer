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
    private $_starttime;


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
    * Setter for starttime
    *
    * @param string $starttime The starttime we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setStarttime($starttime)
    {
        $this->_starttime = $starttime;
    }

    /**
    * Getter for starttime
    *
    * @access public
    *
    * @return string
    */
    public function getStarttime()
    {
        return $this->_starttime;
    }

}