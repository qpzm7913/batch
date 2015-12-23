<?php

class Teamlab_Batch_Filter implements Teamlab_Batch_Filter_Interface
{
    protected $_filters = array();

    const CHAIN_APPEND  = 'append';
    const CHAIN_PREPEND = 'prepend';

    public function addFilter(Teamlab_Batch_Filter_Interface $filter, $placement = self::CHAIN_APPEND)
    {
        if($placement === self::CHAIN_PREPEND) {
            array_unshift($this->_filters, $filter);
        }else{
            $this->_filters[] = $filter;
        }
        return $this;
    }

    public function appendFilter(Teamlab_Batch_Filter_Interface $filter)
    {
        return $this->addFilter($filter, self::CHAIN_APPEND);
    }

    public function prependFilter(Teamlab_Batch_Filter_Interface $filter)
    {
        return $this->addFilter($filter, self::CHAIN_PREPEND);
    }

    public function getFilters()
    {
        return $this->_filters;
    }

    public function filter($value)
    {
        $valueFiltered = $value;

        /** @var Teamlab_Batch_Filter_Interface $filter */
        foreach($this->_filters as $filter) {
            $valueFiltered = $filter->filter($valueFiltered);
        }
        return $valueFiltered;
    }
}