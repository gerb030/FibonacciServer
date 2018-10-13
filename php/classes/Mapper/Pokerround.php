<?php
/**
 * Player
 */
class Mapper_Pokerround extends Mapper_Abstract
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
        if (isset($params['session'])) {
            $select = $this->_db->prepare('select id, session, ownerusername, starttime from pokerround where session = :session');
            $values[':session'] = $params['session'];
        } else if (isset($params['id'])) {
            $select = $this->_db->prepare('select id, session, ownerusername, starttime from pokerround where id = :id');
            $values[':id'] = $params['id'];
        } else {
            throw new Exception_Http('Incorrect parameters given for retrieving pokerround: '.print_r($params, 1), 400);
        }
        $select->execute($values);
        $results = array();
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $data  = array();
            $data['id'] = $row['id'];
            $data['session'] = $row['session'];
            $data['ownerusername'] = $row['ownerusername'];
            $data['starttime'] = $row['starttime'];                
            $results[] = $this->create($data);
        }
        foreach($results as $index => $round) {
            $pokerroundUserMapper = new Mapper_PokerroundUser($this->_db);
            $users = $pokerroundUserMapper->find(array('pokerround_id' => $round->getId()));
            $results[$index]->setPokerroundUsers($users);
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
    * @return User
    */
    public function populate(Domain_Abstract $obj, array $data)
    {
        $obj->setId($data['id']);
        $obj->setSession($data['session']);
        $obj->setOwnerusername($data['ownerusername']);
        $obj->setStarttime($data['starttime']);
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
    * @return Pokerround
    */
    protected function _create()
    {
        return new Domain_Pokerround();
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
        $values[':session'] = $obj->getSession();
        $values[':ownerusername'] = $obj->getOwnerusername();

        $stmt = $this->_db->prepare(
            "INSERT INTO pokerround (`session`, `ownerusername`, starttime) VALUES (:session, :ownerusername, NOW())"
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
        throw new Exception_Http('Update operation on a pokerround is not allowed.', 400);
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
            'DELETE FROM pokerround WHERE id = ' . $obj->getId()
        );
        $stmt->execute(array());
    }

}
