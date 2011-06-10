<?php

/**
 * MongoCollection
 *
 * @author Phu Ha <pha@atlassoftwaregroup.com>>
 * @version 1.0
 * @copyright Atlas Software Group, 22 April, 2011
 * @package BaseMongo
 **/

namespace BaseMongo;
 
class MongoConnection
{
  static protected
    $con;
  
  static public function getConnection()
  {
    return self::$con;
  }
  
  static public function setConnection(array $config)
  {
    $host     = (isset($config['host']) ? $config['host'] : 'localhost');
    $port     = (isset($config['port']) ? $config['port'] : 27017);
    $database = (isset($config['database']) ? $config['database'] : null);
    $username = (isset($config['username']) ? $config['username'] : null);
    $password = (isset($config['password']) ? $config['password'] : null);
    
    $dsn      = sprintf('mongodb://%s:%s', $host, $port);
    $m        = new \Mongo($dsn);
    
    if (null === $database)
    {
      throw new Exception('mongo database has not been defined');
    }
    
    self::$con = $m->selectDB($database);
    
    if ($username !== null && $password !== null)
    {
      self::$con->authenticate($username, $password);
    }
  }
}