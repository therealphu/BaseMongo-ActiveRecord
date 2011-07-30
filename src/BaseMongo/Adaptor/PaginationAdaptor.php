<?php

namespace BaseMongo\Adaptor;

use BaseMongo\BaseMongoQuery;

class PaginationAdaptor implements \Zend_Paginator_Adapter_Interface
{
    protected 
        $count,
        $criteria,
        $options,
        $query;
        
    public function __construct(BaseMongoQuery $query, array $criteria, $options = array())
    {
        $this->query    = $query;
        $this->criteria = $criteria;
        $this->options  = $options;
    }
    
    public function count()
    {
        if (null === $this->count)
        {
            $this->count = $this->query->count($this->criteria);
        }
        
        return (int) $this->count;
    }
    
    public function getItems($offset, $itemCountPerPage)
    {
        $options = array('offset' => $offset, 'limit' => $itemCountPerPage);
        
        if (isset($this->options['sort']))
        {
            $options['sort'] = $this->options['sort'];
        }
        
        return $this->query->find(
                    $this->criteria, 
                    $options
                );
    }
}