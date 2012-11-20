<?php

namespace AlphaLemon\Block\SearchBlockBundle\Core\Form\Search;

use Symfony\Component\Validator\Validator;
use Symfony\Component\DependencyInjection\Exception;

class Search
{
    protected $searchText;
    
    public function getSearchText()
    {
        return $this->searchText;
    }

    public function setSearchText($v)
    {
        $this->searchText = $v;
    }
}