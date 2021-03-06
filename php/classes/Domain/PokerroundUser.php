<?php
/**
 * PokerroundUser domain class
 */
class Domain_PokerroundUser extends Domain_Abstract
{


    /**
    * @access private
    * @var Integer
    */
    private $_pokerroundId;

    /**
    * @access private
    * @var Integer
    */
    private $_userId;

    /**
    * @access private
    * @var string nullable
    */
    private $_voted;

    /**
    * @access private
    * @var String
    */
    private $_userName;


    /**
    * Setter for pokerroundId
    *
    * @param string $pokerroundId The name we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setPokerroundId($pokerroundId)
    {
        $this->_pokerroundId = $pokerroundId;
    }

    /**
    * Getter for pokerroundId
    *
    * @access public
    *
    * @return string
    */
    public function getPokerroundId()
    {
        return $this->_pokerroundId;
    }

    /**
    * Setter for userId
    *
    * @param string $userId The name we want to set to
    *
    * @access public
    *
    * @return void
    */
    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    /**
    * Getter for ownerusername
    *
    * @access public
    *
    * @return string
    */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
    * Setter for voteed
    *
    * @param string $voted what whas the vote?
    *
    * @access public
    *
    * @return void
    */
    public function setVoted($voted)
    {
        $this->_voted = $voted;
    }

    /**
    * Getter for voted
    *
    * @access public
    *
    * @return string
    */
    public function getVoted()
    {
        return $this->_voted;
    }

    public function setUsername($userName) {
        $this->_userName = $userName;
    }

    public function getUsername() {
        return $this->_userName;
    }

    public function toArray() {
        $response = array();
        // id doesn't need to be exposed externally
        //$response['id'] = $this->getId();
        $response['username'] = $this->getUsername();
        $response['voted'] = $this->getVoted();
        return $response;
    }

}