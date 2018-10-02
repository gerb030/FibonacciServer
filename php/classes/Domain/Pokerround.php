<?php
/**
 * Pokerround domain class
 */
class Domain_Pokerround extends Domain_Abstract
{


    /**
    * @access private
    * @var string
    */
    private $_session;

    /**
    * @access private
    * @var string
    */
    private $_ownerusername;

    /**
    * @access private
    * @var timestamp
    */
    private $_starttime;




    /**
    * Setter for session
    *
    * @param string $session The name we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setSession($session)
    {
        $this->_session = $session;
    }

    /**
    * Getter for session
    *
    * @access public
    *
    * @return string
    */
    public function getSession()
    {
        return $this->_session;
    }

    /**
    * Setter for ownerusername
    *
    * @param string $ownerusername The name we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setOwnerusername($ownerusername)
    {
        $this->_ownerusername = $ownerusername;
    }

    /**
    * Getter for ownerusername
    *
    * @access public
    *
    * @return string
    */
    public function getOwnerusername()
    {
        return $this->_ownerusername;
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