<?php

namespace AlphaLemon\Block\SearchBlockBundle\Core\Listener;

use AlphaLemon\AlphaLemonCmsBundle\Core\Event\Deploy\AfterDeployEvent;

/**
 * Description of DeployListener
 *
 * @author alphalemon
 */
class DeployListener
{
    private $blocksManagerFactory;
    
    public function __construct($blocksManagerFactory)
    {
        $this->blocksManagerFactory = $blocksManagerFactory;
    }

    public function onAfterDeploy(AfterDeployEvent $event)
    {
        $deployer = $event->getDeployer();
        $pagePath = $deployer->getDeployBundleRealPath() . '/Resources/search_data';
        if ( ! is_dir($pagePath)) {
            mkdir($pagePath);
        }
        
        $pageTreeCollection = $deployer->getPageTreeCollection();
        foreach ($pageTreeCollection as $pageTree) {            
            $pageContents = "";
            $blocks = $pageTree->getPageBlocks()->getBlocks();
            foreach ($blocks as $slotBlocks) {
                foreach ($slotBlocks as $block) {
                    $blockManager = $this->blocksManagerFactory->createBlockManager($block);
                    $pageContents .= trim(strip_tags($blockManager->getHtmlCmsActive())) . "\n";
                }
            }
            
            $pageFile = $pagePath . '/_' . str_replace('-', '_', $pageTree->getAlLanguage()->getLanguageName()) . '_' . str_replace('-', '_', $pageTree->getAlPage()->getPageName());
            file_put_contents($pageFile, $pageContents);
        }exit;
    }
}