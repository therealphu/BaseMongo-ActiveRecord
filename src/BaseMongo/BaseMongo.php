<?php

/**
 * BaseMongo
 *
 * @author Phu Ha <pha@atlassoftwaregroup.com>
 * @version 1.0
 * @copyright Atlas Software Group, 22 April, 2011
 * @package BaseMongo
 **/

namespace BaseMongo;

abstract class BaseMongo
{
 /**
  * @var  MongoCollection   $collection     A MongoCollection object
  * @var  string            $collectionName The current model collection name
  */
  protected
    $collection,
    $collectionName;
  
 /**
  * Retrieve the current monogo connection
  *
  * @return Mongo A Mongo object
  */
  public function getConnection()
  {
    $con = MongoConnection::getConnection();
    
    if (null === $con)
    {
      throw new \Exception('unable to find a connection');
    }
    
    return $con;
  }
  
 /**
  * Retrieve the current mongo collection object
  * 
  * @return MongoCollection A MongoCollection object
  */
  public function getCollection()
  {
    if (null === $this->collection)
    {
      $this->collection = $this->getConnection()->selectCollection($this->getCollectionName());
    }
    
    return $this->collection;
  }
  
 /**
  * Retrieve the called class
  *
  * @return string  A string
  */
  public function getClassName()
  {
    $class      = get_called_class();
    $pos        = strrpos($class, '\\');
    $className  = substr($class, $pos + 1);
    
    return $className;
  }
 
 /**
  * Transform the class name by underscoring the camelized string.
  * 
  * @return string  A string
  */
  public function getCollectionName()
  {
    if (null === $this->collectionName)
    {
      $this->collectionName = $this->underscore($this->getClassName());
    }
    
    return $this->collectionName;
  }
  
 /**
  * Underscore a camelized string
  *
  * @return string  A string
  */
  public function underscore($string)
  {
    $string = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $string);
    $string = strtolower($string);
    
    return $string;
  }
  
 /**
  * Camelize a string
  * 
  * @return string  A string
  */
  public function camelize($string)
  {
    $exp = explode('_', $string);
    $exp = array_map('ucfirst', $exp);
    
    return implode('', $exp);
  }
  
 /**
  * Transform a string into camelized method
  * 
  * @return string  A string
  */
  public function getMethodName($prefix, $string)
  {
    $string = $this->underscore($string);
    $string = sprintf('%s%s', $prefix, $this->camelize($string));
    
    return $string;
  }
}
