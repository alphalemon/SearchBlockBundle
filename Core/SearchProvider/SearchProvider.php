<?php
namespace AlphaLemon\Block\SearchBlockBundle\Core\SearchProvider;

use FOQ\ElasticaBundle\Provider\ProviderInterface;
use Elastica_Type;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpKernel\KernelInterface;
use AlphaLemon\ThemeEngineBundle\Core\Asset\AlAsset;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\Client;

class SearchProvider implements ProviderInterface
{
    protected $userType;
    protected $kernel;
    protected $router;
    protected $deployBundle;

    public function __construct(Elastica_Type $userType, KernelInterface $kernel, Router $router, $deployBundle)
    {
        $this->userType = $userType;
        $this->kernel = $kernel;
        $this->router = $router;
        $this->deployBundle = $deployBundle;
    }

    /**
     * Insert the repository objects in the type index
     *
     * @param Closure $loggerClosure
     */
    public function populate(\Closure $loggerClosure = null)
    {
        if ($loggerClosure) {
            $loggerClosure('Indexing website');
        }
        
        $deployBundle = new AlAsset($this->kernel, $this->deployBundle);
        $deployBundlePath = $deployBundle->getRealPath();
        
        $i = 0;
        $documents = array();      
        $routesPath = $deployBundlePath . '/Resources/config/site_routing.yml';
        $routes = Yaml::parse($routesPath);
        if (null === $routes) {
            $loggerClosure('Any route defined. Please deploy the website then run this command again');
            
            return;
        }
        
        $routes = array_keys($routes);
        foreach ($routes as $route) {
            $link = $this->router->generate($route);
            
            $client = new Client($this->kernel);
            $client->request('GET', $link);            
            $content = $client->getCrawler()->text();
            $content = preg_replace('/\/\*\<\!\[CDATA\[\*\/(.*?)\/\*\]\]\>\*\//s', '', $content);            
            $page = array(
                "contents" => $content,
                "url" => $link,
            );
            
            $document = new \Elastica_Document($i);
            $document->setData($page);
            $documents[] = $document;
            
            $i++;
        }
        
        try {
            $this->userType->addDocuments($documents);
        }
        catch (\Elastica_Exception_BulkResponse $ex) {
            print_r($ex->getFailures());
        }
        catch (\Exception $ex) {echo "U";
            $ex->getMessage();
        }
    }
}