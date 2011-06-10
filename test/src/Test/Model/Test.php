<?php

namespace Test\Model;

use BaseMongo\BaseMongoObject;

class Test extends BaseMongoObject
{
  protected function validateAge($value)
  {
    if (!is_numeric($value))
    {
      throw new \Exception('age must be a numeric value');
    }
  }
}