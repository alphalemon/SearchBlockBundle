<?php
/**
 * An AlphaLemonCms Block
 */

namespace AlphaLemon\Block\SearchBlockBundle\Core\Block;

use AlphaLemon\AlphaLemonCmsBundle\Core\Content\Block\JsonBlock\AlBlockManagerJsonBlockContainer;

/**
 * Description of AlBlockManagerSearchBlock
 */
class AlBlockManagerSearchBlock extends AlBlockManagerJsonBlockContainer
{
    public function getDefaultValue()
    {
        $defaultValue = '{
            "template": "SearchBlockBundle:Block:search_form.html.twig",
            "options": {"search_route": "_home"}
        }';
        
        return array('Content' => $defaultValue);
    }
    
    public function getHtml()
    {
        $values = $this->decodeJsonContent($this->alBlock->getContent());
        
        return $this->container->get('templating')->render($values['template'], $values['options']);
    }
}