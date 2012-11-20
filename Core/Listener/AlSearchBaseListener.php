<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestListener
 *
 * @author giansimon
 */

namespace AlphaLemon\Block\SearchBlockBundle\Core\Listener;

use AlphaLemon\ThemeEngineBundle\Core\Rendering\Listener\BasePageRenderingListener;

abstract class AlSearchBaseListener extends BasePageRenderingListener
{
    protected $requiredOptions = null;
    
    abstract protected function configure();
    
    protected function validateOptions()
    {
        $options = $this->configure();
        
        if ($this->container->has('alpha_lemon_cms.parameters_validator') && null !== $this->requiredOptions) {
            $validator = $this->container->get('alpha_lemon_cms.parameters_validator');
            $validator->checkRequiredParamsExists($this->requiredOptions, $options);
        }
        
        return $options;
    }
}
