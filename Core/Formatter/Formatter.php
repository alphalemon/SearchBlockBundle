<?php

namespace AlphaLemon\Block\SearchBlockBundle\Core\Formatter;

/**
 * Description of Formatter
 *
 * @author alphalemon
 */
class Formatter
{
    protected $index;
    protected $numberOfWords = 30;
            
    public function __construct($index)
    {
        $this->index = $index;
    }
    
    public function getFormattedResult($siteUrl, $search)
    {
        try 
        {
            $elasticResult = $this->index->search($search);
            $pages = $elasticResult->getResults();
            if (count($pages) == 0) {
                return "Any result found for this search";
            }

            $result = array();
            foreach ($pages as $page) {
                $data = $page->getData();            
                $result[] = sprintf('<a href="%s">%s</a><br />%s<br />', $data['url'], $siteUrl . $data['url'], $this->formatDescription($search, $data['contents']));
            }

            return implode("<br />", $result);
        }
        catch(\Elastica_Exception_Client $ex) {
            return "Search server is down at the moment. Sorry for any inconvenience caused.";
        }
        catch(\Exception $ex) {
            return "An unespected error has occoured during processinig your request. Sorry for any inconvenience caused.";
        }
    }
    
    public function setNumberOfWords($v)
    {
        $this->numberOfWords = $v;
    }
    
    protected function formatDescription($search, $content)
    {
        $words = 0;
        $description = "";
        $tokens = explode(" ", $content);
        foreach ($tokens as $token) {
            if ($words > 0) {
                $description .= $token . " ";
                if ($words == $this->numberOfWords) {
                    break;
                }
                $words++;
            }

            if ($token == $search) {
                $description .= "<b>" . $token . "</b> ";
                $words++;
            }
        }

        if ($this->numberOfWords == 30) {
            $description .= "...";
        }
        
        return $description;
    }
}