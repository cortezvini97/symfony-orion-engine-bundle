<?php


namespace Orion\OrionEngine\Services;

use Orion\OrionEngine\Utils\OrionApp;
use Orion\OrionEngine\Utils\OrionSymfony;
use Symfony\Component\DependencyInjection\Argument\ServiceLocator;

class OrionEngineService
{
    private OrionSymfony $orion;
    private array $configs;

    public function __construct(array $configs)
    {
        $dir_view = $configs['dir_views'];
        $dir_cache = $configs['cache_dir'];
        $dir_directives = $configs["directives_dir"];
        $functions_dir = $configs["functions_dir"];
        $debug = $configs["debug"];
        $deleteFile = $configs["deleteFile"];

        $this->orion = new OrionSymfony([
            "viewsPath"=>$dir_view,
            "functionsPath"=>$functions_dir,
            "directivesPath"=>$dir_directives,
            "compiledPath"=>$dir_cache,
            "debug"=>$debug,
            "deleteFile"=>$deleteFile
        ]);
        $this->configs = $configs;
        $this->orion->setApp();
    }


    public function getOrion():OrionSymfony
    {
        return $this->orion;
    }

    public function setOrion(OrionSymfony $orion)
    {
        $this->orion = $orion;
    }

    public function getConfigs():array
    {
        return $this->configs;
    }


    public function setServiceLocator(ServiceLocator $serviceLocator)
    {
        $this->orion->setServiceLocator($serviceLocator);
    }

    public function view(string $view, array $params = [], array $services = []):string
    {
        require_once dirname(__DIR__, 2).DIRECTORY_SEPARATOR."autoload.php";
        autoload($services);
        $custom_directives_file = dirname(__DIR__, 2).DIRECTORY_SEPARATOR."custom_directives.php";
        if(file_exists($custom_directives_file)){
            $this->orion->setCustomDirectives($custom_directives_file);
        }
        return $this->orion->render($view, $params, $services);
    }



}