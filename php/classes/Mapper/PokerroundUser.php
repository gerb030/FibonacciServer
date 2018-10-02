<?php
/**
 * Player
 */
class Mapper_PokerroundUser extends Mapper_Abstract
{
    protected $_db;

    /**
    * Construct method
    *
    * @param PDO $db The db we will use
    *
    * @access public
    *
    * @return void
    */
    public function __construct($db)
    {
        $this->_db = $db;
    }

    /**
    * Find method
    *
    * @param Array $params The params we want to base our search on
    *
    * @access public
    *
    * @return Domain|bool
    */
    public function find($params = array())
    {
        $values = array();
        if (isset($params['username'])) {
            $select = $this->_db->prepare('select id, pokerround_id, user_id, voted, user.username from pokerround_user INNER JOIN user ON user.id = pokerround_user.user_id WHERE user.username = :username');
            $values[':username'] = $params['username'];
        } else if (isset($params['pokerround_id'])) {
            $select = $this->_db->prepare('select id, pokerround_id, user_id, voted, user.username from pokerround_user INNER JOIN user ON user.id = pokerround_user.user_id WHERE pokerround_user.pokerround_id = :pokerround_id');
            $values[':pokerround_id'] = $params['pokerround_id'];
        } else if (isset($params['id'])) {
            $select = $this->_db->prepare('select id, pokerround_id, user_id, voted, user.username from pokerround_user INNER JOIN user ON user.id = pokerround_user.user_id WHERE pokerround_user.id = :id');
            $values[':id'] = $params['id'];
        } else {
            throw new Exception_Http('Incorrect parameters given for retrieving pokerround votes: '.print_r($params, 1), 400);
        }
        $select->execute($values);
        $results = array();
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $data  = array();
                $data['id'] = $row['id'];
                $data['pokerround_id'] = $row['pokerround_id'];
                $data['user_id'] = $row['user_id'];
                $data['username'] = $row['username'];
                $data['voted'] = $row['voted'];
            $results[] = $this->create($data);
        }
        if (count($results) == 1) {
            return current($results);
        }
        return $results;
    }

    /**
    * Populate object with data
    *
    * @param Domain $obj  The object type we will populate
    * @param Array  $data Data used for populating
    *
    * @access public
    *
    * @return PokerroundUser
    */
    public function populate(Domain_Abstract $obj, array $data)
    {
            $obj->setId($data['id']);
            $obj->setPokerrroundId($data['pokerround_id']);
            $obj->setUsername($data['user_id']);
            $obj->setVoted($data['voted']);
            $obj->setUsername($data['username']);
        return $obj;
    }

    /**
    * Save the DomainObject
    *
    * Store the DomainObject in persistent storage. Either insert
    * or update the store as required.
    *
    * @param Domain $obj The object we're trying to save
    *
    * @return void
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
    * Create instance of domain object
    *
    * @access protected
    *
    * @return Domain_PokerroundUser
    */
    protected function _create()
    {
        return new Domain_PokerroundUser();
    }

    /**
    * Insert method for domain object
    *
    * @param Domain $obj The object we're trying to insert
    *
    * @access protected
    *
    * @return void
    */
    protected function _insert(Domain_Abstract $obj)
    {
        $values = array();
            $values[':pokerround_id'] = $obj->getPokerroundId();
            $values[':user_id'] = $obj->getUserId();

        $stmt = $this->_db->prepare(
            "INSERT INTO pokerround_user (pokerround_id, user_id, voted) VALUES (:pokerround_id, :user_id, NULL)"
        );
        $result = $stmt->execute($values);
        if ($result) {
            $obj->setId($this->_db->lastInsertId());
        }
    }

    /**
    * Update object by id
    *
    * @param Domain $obj The object we're trying to update
    *
    * @access protected
    *
    * @return void
    */
    protected function _update(Domain_Abstract $obj)
    {
        $values = array();
        $values[':voted'] = $obj->getVoted();

        $stmt = $this->_db->prepare(
            "UPDATE pokerround_user SET voted = :voted  WHERE id = " . $obj->getId()
        );
        $stmt->execute($values);
    }

    /**
    * Delete domain object by id
    *
    * @param Domain $obj The object we're trying to save
    *
    * @access protected
    *
    * @return void
    */
    protected function _delete(Domain_Abstract $obj)
    {
        $stmt = $this->_db->prepare(
            'DELETE FROM pokerround_user WHERE id = ' . $obj->getId()
        );
        $stmt->execute(array());
    }

}
