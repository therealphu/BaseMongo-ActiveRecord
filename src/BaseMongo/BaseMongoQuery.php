<?php

/**
 * BaseMongoQuery
 *
 * @author Phu Ha <pha@atlassoftwaregroup.com>
 * @version 1.0
 * @copyright Atlas Software Group, 22 April, 2011
 * @package BaseMongo
 **/

namespace BaseMongo;

use BaseMongo\BaseMongo;
use BaseMongo\BaseMongoObject;

abstract class BaseMongoQuery extends BaseMongo
{
  protected
    $query;
  
  public function __construct()
  {
  }
  
  static public function create()
  {
    $class = get_called_class();
    
    return new $class();
  }
  
  public function getCollectionName()
  {
    $class = preg_replace('/Query$/i', '', $this->getClassName());
    $class = $this->underscore($class);
    
    return $class;
  }
  
  protected function populateObject(\MongoCursor $documents)
  {
    $class      = preg_replace('/Query$/i', '', get_called_class());
    $collection = new BaseMongoCollection;
    
    foreach($documents AS $document)
    {
      $document = new \ArrayIterator($document);
      
      $collection->append(new $class($document));
    }
    
    return $collection;
  }
  
  public function find($query = array(), $options = array())
  {
    $documents = $this->getCollection()->find($query);
    
    if (isset($options['sort']))
    {
      $documents->sort($options['sort']);
    }
    
    if (isset($options['offset']))
    {
      $documents->skip($options['offset']);
    }
    
    if (isset($options['limit']))
    {
      $documents->limit($options['limit']);
    }
    
    return $this->populateObject($documents);
  }
  
  public function findOne($query = array(), $options = array())
  {
    $options['limit'] = 1;
    
    $documents        = $this->find($query, $options);
    $document         = current($documents);
    
    return ($document ? $document : null);
  }
  
  public function count($query = array())
  {
    return $this->getCollection()->count($query);
  }
}