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
    private $_lastUpdated;

    /**
    * @access private
    * @var timestamp
    */
    private $_starttime;

    /**
    * @access private
    * @var timestamp
    */
    private $_closed;

    /**
    * @access private
    * @var pokerroundUsers
    */
    private $_pokerroundUsers;

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

    /**
    * Setter for lastUpdated
    *
    * @param string $lastUpdated The timestamp we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setLastUpdated($lastUpdated)
    {
        $this->_lastUpdated = $lastUpdated;
    }

    /**
    * Getter for lastUpdated
    *
    * @access public
    *
    * @return string
    */
    public function getLastUpdated()
    {
        return $this->_lastUpdated;
    }

    /**
    * Setter for closed
    *
    * @param string $closed
    *
    * @access public
    *
    * @return void
    */
    public function setClosed($closed)
    {
        $this->_closed = $closed;
    }

    /**
    * Getter for closed
    *
    * @access public
    *
    * @return string
    */
    public function getClosed()
    {
        return $this->_closed;
    }    

    /**
    * Setter for users
    *
    * @access public
    *
    * @return string
    */
    public function setPokerroundUsers(array $pokerroundUsers) {
        $this->_pokerroundUsers = $pokerroundUsers;
    }

    /**
    * Getter for users
    *
    * @access public
    *
    * @return string
    */
    public function getPokerroundUsers()
    {
        return $this->_pokerroundUsers;
    }

    public function addUser(Domain_PokerroundUser $pokerroundUsers) {
        array_push($this->_pokerroundUsers, $pokerroundUsers);
    }

    public function toArray() {
        $response = array();
        // id doesn't need to be exposed externally
        //$response['id'] = $this->getId();
        $response['session'] = $this->getSession();
        $response['ownerusername'] = $this->getOwnerusername();
        $response['starttime'] = $this->getStarttime();
        $response['lastupdated'] = $this->getLastUpdated();
        $response['closed'] = $this->getClosed();
        $response['pokerroundusers'] = array();
        foreach ($this->_pokerroundUsers as $pokerroundUser) {
            array_push($response['pokerroundusers'], $pokerroundUser->toArray());
        }
        return $response;
    }

}