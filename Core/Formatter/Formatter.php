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
    protected $maxChars = 300;
            
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
                $result[] = sprintf('<a href="%s">%s</a><br />%s<br />', $data['url'], $siteUrl . $data['url'], $this->renderDescription($data['contents'], array($search)));
            }

            return '<style>.match{color: #FF6600;font-weight:bold;}</style>' . implode("<br />", $result);
        }
        catch(\Elastica_Exception_Client $ex) {
            return "Search server is down at the moment. Sorry for any inconvenience caused.";
        }
        catch(\Exception $ex) {
        echo $ex->getMessage();exit;
            return "An unespected error has occoured during processinig your request. Sorry for any inconvenience caused.";
        }
    }
    
    public function setNumberOfWords($v)
    {
        $this->maxChars = $v;
    }
    
    /**
     * Renders the description proving a snippet of the content which contains the 
     * keyword
     * 
     * Credits for this snippet goes to Harmen Janssen http://whatstyle.net
     * 
     * @param type $content
     * @param type $searchTerms
     * @return type
     */
    protected function renderDescription ($content ,$searchTerms)
    {
        $chunk = '';
        foreach ($searchTerms as $searchTerm) {
            if (preg_match("/$searchTerm/",$content)) {
                $pos = strpos ($content,$searchTerm);
                if (($pos - ($this->maxChars/2)) < 0) {
                        $startPos = 0;
                }
                else {
                        $startPos = ($pos - ($this->maxChars/2));
                        $chunk .= '...';
                }
                
                $chunk .= substr($content,$startPos,$this->maxChars);
                
                if (($pos + ($this->maxChars/2)) < strlen($content)) {
                        $chunk .= '...';
                }
                break;
            }
        }
        if ($chunk == '') {
            $chunk = substr($content,0,$this->maxChars).'...';
        }
        
        foreach ($searchTerms as $term) {
            $chunk = str_replace ($term,"<span class=\"match\">$term</span>",$chunk);
        }
        
        return $chunk;
    }    
}