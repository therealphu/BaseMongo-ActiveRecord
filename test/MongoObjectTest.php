<?php

require_once __DIR__ . '/bootstrap.php';

use BaseMongo\BaseMongoConnection;
use BaseMongo\BaseMongoObject;
use BaseMongo\BaseMongoQuery;
use Test\Model\Test;
use Test\Model\TestQuery;

class MongoObjectTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    BaseMongoConnection::initialize(array(
      'database'  => 'Test',
      'host'      => 'localhost'
    ));
    
    // clear test data
    $objects = TestQuery::create()->find();
    
    foreach ($objects AS $object)
    {
      $this->assertInstanceOf('Test\Model\Test', $object);
      $object->delete();
    }
  }
  
  public function testMongo()
  {
    // setup new Test object
    $object = new Test;
    
    // test collection name and class name
    $this->assertEquals('test', $object->getCollectionName());
    $this->assertEquals('Test', $object->getClassName());
    
    // test db instance
    $this->assertInstanceOf('\MongoDB', $object->getConnection());
    
    // setup getters
    $object->setName('Test User');
    $object->setUserId(1);
    $object->setAge(25);
    
    // test if key has been underscore correctly
    $this->assertArrayHasKey('name', $object->toArray());
    $this->assertArrayHasKey('user_id', $object->toArray());
    
    // test if setter are working
    $this->assertEquals('Test User', $object->getName());
    $this->assertEquals(1, $object->getUserId());
    
    // test fromArray method
    $object->fromArray(array('sex' => 'male', 'mailAddress' => '1234 Fake St'));
    
    // test if key are set correctly from fromArray
    $this->assertArrayHasKey('sex', $object->toArray());
    $this->assertArrayHasKey('mail_address', $object->toArray());
    
    // save object
    $this->assertEquals(true, $object->save());
    
    // test validation
    $object->setAge('test');
    
    $this->assertEquals(false, $object->save());
    
    $this->assertArrayHasKey('age', iterator_to_array($object->errors()));
    
    // change age to 29
    $object->setAge(29);
    
    // update object
    $this->assertEquals(true, $object->save());
    
    // test if object has been saved
    $this->assertEquals(1, TestQuery::create()->count());
    
    // check if age gets update correctly
    $this->assertEquals(29, TestQuery::create()->findOne()->getAge());
    
    // delete object
    $this->assertEquals(true, $object->delete());
    
    // test if object has been deleted
    $this->assertEquals(0, TestQuery::create()->count());
  }
}