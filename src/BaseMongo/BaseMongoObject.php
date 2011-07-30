<?php

/**
 * BaseMongoObject
 *
 * @author Phu Ha <pha@atlassoftwaregroup.com>
 * @version 1.0
 * @copyright Atlas Software Group 22 April, 2011
 * @package BaseMongo
 **/

namespace BaseMongo;

use BaseMongo\BaseMongo;

abstract class BaseMongoObject extends BaseMongo
{
  protected
    $id,
    $document,
    $errors,
    $field,
    $mongo,
    $isModified,
    $isDeleted,
    $isNew;
  
  public function __construct(\ArrayIterator $document = null)
  {
    $this->errors     = new \ArrayIterator(array());
    $this->field     = new \ArrayIterator(array());
    $this->isNew      = true;
    $this->isModified = false;
    $this->document   = $document;
    
    if (isset($this->document['_id']))
    {
      $this->isNew  = false;
      $this->id     = $this->document['_id'];
      $this->field = clone $this->document;
    }
    
    if ($this->isNew)
    {
      $this->afterNew();
    }
  }
  
  public function setDocumentId(\MongoId $id)
  {
    $this->id = $id;
  }
  
  public function getDocumentId()
  {
    return $this->id;
  }
  
  public function isNew()
  {
    return $this->isNew;
  }
  
  public function toArray()
  {
    if (isset($this->field['_id']))
    {
      unset($this->field['_id']);
    }
    
    $array = array();
    
    foreach ($this->field AS $field => $value)
    {
      $array[$field] = call_user_func(array($this, $this->getMethodName('get', $field)));
    }
    
    return $array;
  }
  
  public function fromArray(array $array)
  {
    if (isset($array['_id']))
    {
      unset($array['_id']);
    }
    
    foreach ($array AS $field => $value)
    {
      call_user_func(array($this, $this->getMethodName('set', $field)), $value);
    }
  }
  
  public function isModified()
  {
    return $this->isModified;
  }
  
  protected function beforeSave() { return true; }
  protected function afterSave() {}
  protected function beforeValidation() { return true; }
  protected function afterValidation() {}
  protected function beforeDelete() { return true; }
  protected function afterNew() {}
  
  public function errors()
  {
    return $this->errors;
  }
  
  protected function isValid()
  {
    $this->errors = new \ArrayIterator(array());
    
    if (!$this->beforeValidation())
    {
      return false;
    }
    
    $methods  = get_class_methods(get_called_class());
    
    foreach ($methods AS $method)
    {
      $action = substr($method, 0, 8);
      
      if ($action == 'validate')
      {
        $field  = substr($method, 8);
        $getter = sprintf('get%s', $field);
        $value  = call_user_func(array($this, $getter));
        
        try 
        {
          call_user_func(array($this, $method), $value);
        } 
        catch (\Exception $e) 
        {
          $this->errors[$this->underscore($field)] = $e->getMessage();
        }
      }
    }
    
    if (!$this->errors)
    {
      $this->afterValidation();
    }
    
    return (count($this->errors) == 0);
  }
  
  public function save()
  {
    if (null !== $this->getDocumentId())
    {
      $this->field['_id'] = $this->getDocumentId();
    }
    
    if (!$this->isValid())
    {
      return false;
    }
    
    if ($this->beforeSave())
    {
      if ($this->isNew)
      {
        $this->getCollection()->insert($this->field);
        
        $this->setDocumentId($this->field['_id']);
        
        unset($this->field['_id']);
      }
      else
      {
        $this->getCollection()->save($this->field);
      }
      
      $this->isNew = false;
      
      $this->afterSave();
    }
    
    return true;
  }
  
  public function delete()
  {
    if ($this->isNew)
    {
      throw new \Exception('This record is new and can not be deleted.');
    }
    
    if ($this->isDeleted)
    {
      throw new \Exception('This object has already been deleted.');
    }
    
    if ($this->beforeDelete())
    {
      $this->getCollection()->remove(array('_id' => $this->id));
      
      $this->isDeleted = true;
    }
    
    return $this->isDeleted;
  }
  
  public function __call($name, array $args)
  {
    $prefix   = strtolower(substr($name, 0, 3));
    $field    = substr($name, 3);
    $key      = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
    $key      = $this->underscore($field);
    
    if ($prefix == 'get')
    {
      return (isset($this->field[$key]) ? $this->field[$key] : null);
    }
    else if ($prefix == 'set')
    {
      $this->field[$key] = (isset($args[0]) ? $args[0] : null);
      
      $this->isModified = true;
    }
    else
    {
      throw new \Exception('Call to undefined method: ' . $name); 
    }
  }
}