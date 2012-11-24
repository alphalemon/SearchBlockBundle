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
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use AlphaLemon\ThemeEngineBundle\Core\Rendering\SlotContent\AlSlotContent;

abstract class AlSearchFormRenderingListener extends AlSearchBaseListener
{
    protected function renderSlotContents()
    {
        $this->requiredOptions = array(
            'page' => '', 
            'slot' => '',
        );
        $options = $this->validateOptions();
        
        $request = $this->container->get('request');
        $destinationSlot = $options["slot"];
        $routeName = sprintf('_%s_%s', $request->getLocale(), $options["page"]);
        
        $search = new Search();
        $form = $this->container->get('form.factory')->create(new SearchForm(), $search);

        try
        {
            $route = $this->container->get('router')->generate($routeName);
        }
        catch(RouteNotFoundException $ex)
        {
            $route = "";
        }

        $content = $this->container->get('templating')->render('SearchBlockBundle:Block:search_form.html.twig', array('search_form' => $form->createView(), 'search_route' => $route));

        $slotContent = new AlSlotContent();
        $slotContent->setContent($content)
                    ->setSlotName($destinationSlot)
                    ->replace();

        return array($slotContent);
    }
}
