<?php
/**
 * Player
 */
class Mapper_User extends Mapper_Abstract
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
        // find by either username OR email address
        if (isset($params['username']) && isset($params['email'])) {
            $select = $this->_db->prepare('select id, username, emailaddress, created from user where username = :username or emailaddress = :emailaddress');
            $values[':username'] = $params['username'];
            $values[':emailaddress'] = $params['emailaddress'];
        } else if (isset($params['username'])) {
            $select = $this->_db->prepare('select id, username, emailaddress, created from user where username = :username');
            $values[':username'] = $params['username'];
        } else if (isset($params['id'])) {
            $select = $this->_db->prepare('select id, username, emailaddress, created  from user where id = :id');
            $values[':id'] = $params['id'];
        } else {
            throw new Exception_Http('Incorrect parameters given for retrieving user: '.print_r($params, 1), 400);
        }
        $select->execute($values);
        $results = array();
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $data  = array();
            $data['id'] = $row['id'];
            $data['username'] = $row['username'];
            $data['emailaddress'] = $row['emailaddress'];
            $data['created'] = $row['created'];
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
    * @return User
    */
    public function populate(Domain_Abstract $obj, array $data)
    {
        $obj->setId($data['id']);
        $obj->setUsername($data['username']);
        $obj->setEmailAddress($data['emailaddress']);
        $obj->setCreated($data['created']);
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
    * @return User
    */
    protected function _create()
    {
        return new Domain_User();
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
        $values[':username'] = $obj->getUsername();
        $values[':emailaddress'] = $obj->getEmailAddress();

        $stmt = $this->_db->prepare(
            "INSERT INTO user (username, emailaddress, created) VALUES (:username, :emailaddress, NOW())"
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
        $values[':username'] = $obj->getUsername();
        $values[':emailaddress'] = $obj->getEmailAddress();

        $stmt = $this->_db->prepare(
            "UPDATE user SET username = :username  WHERE id = " . $obj->getId()
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
            'DELETE FROM user WHERE id = ' . $obj->getId()
        );
        $stmt->execute(array());
    }

}
