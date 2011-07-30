BaseMongo 1.0 README

Sample Usage:

I highly recommend using an autoloader such as symfony autoload component, zend framework autoloader, spl autoload, etc.

First, we will need to create a model.

User.php

<?php

namespace Model;

use BaseMongo\BaseMongoObject;

class User extends BaseMongoObject
{
  
  // a custom setter
  // you don't need to define a setter or getter for all your fields
  public function setAge($value)
  {
    $this->field['age'] = (int) $value;
  }
  
  public function getAge()
  {
    return (isset($this->field['age']) ? $this->field['age'] : 0);
  }
  
  // validates name field
  public function validateName($value)
  {
    if (null === $value)
    {
      throw new \Exception('missing name');
    }
  }
}

Secondly, we need to create a query class. The query class will be use to retrieve records from MongoDB.

UserQuery.php

<?php

namespace Model;

use BaseMongo\BaseMongoQuery;

class UserQuery extends BaseMongoQuery
{
  // a custom query method
  public function findByAge($age)
  {
    return $this->find(array('age' => $age));
  }
  
  public function findTestUser()
  {
    return $this->findOne(array('name' => 'Test User'));
  }
}


Putting everything together:

index.php

<?php

use BaseMongo\MongoConnection;
use Model\User;
use Model\UserQuery;

// setup connection
// username and password are optional parameters
BaseMongoConnection::setConnection(array(
  'database'  => 'Test',
  'host'      => 'localhost',
  'username'  => null,
  'password'  => null
));

// save user to MongoDB
$user = new User;

$user->setName('Test User');
$user->setAge(29);
$user->setSex('male');

// insert new record
$user->save();

$user->setAge(30);

// update record
$user->save();

// delete record
// $user->delete();

// retrieving documents
$query = new UserQuery;

// options [ sort, limit, offset ]
$query->find($criteria = array(), $options = array());

// calling custom query method
$query->findByAge(29);

Example Collection:

$users = $query->findByAge(29);

foreach ($users AS $user)
{
  echo $user->getName();
}

Example Single Record:
$user = $query->findOne(array('name' => 'Test User'));

echo $user->getName();

The End.
