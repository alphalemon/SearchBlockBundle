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

use AlphaLemon\Block\SearchBlockBundle\Core\Form\Search\Search;
use AlphaLemon\Block\SearchBlockBundle\Core\Form\Search\SearchForm;
use AlphaLemon\ThemeEngineBundle\Core\Rendering\SlotContent\AlSlotContent;
use AlphaLemon\Block\SearchBlockBundle\Core\Formatter\Formatter;

abstract class AlSearchResultsRenderingListener extends AlSearchBaseListener
{
    protected function renderSlotContents()
    {
        $this->requiredOptions = array(
            'slot' => '',
        );
        
        $options = $this->validateOptions();
        $destinationSlot = $options["slot"];
        
        $content = "";
        $search = new Search();
        $request = $this->container->get('request');
        $form = $this->container->get('form.factory')->create(new SearchForm(), $search);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            $content = '';
            if ($form->isValid()) {
                $data = $form->getData();
                $formatter = new Formatter($this->container->get('foq_elastica.index.website.search'));
                $content = $formatter->getFormattedResult($request->getScheme().'://'.$request->getHttpHost(), $data->getSearchText());
            }
        }

        $slotContent = new AlSlotContent();
        $slotContent->setContent($content)
                    ->setSlotName($destinationSlot)
                    ->replace();

        return array($slotContent);
    }
}
