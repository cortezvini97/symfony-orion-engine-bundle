<?php

namespace Orion\OrionEngine\Controllers;

use Orion\OrionEngine\Services\OrionEngineService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class RenderOrionView extends AbstractController{
    

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel) {
        $this->kernel = $kernel;
    }

    public function view(string $view, $params = []):Response{
        $extension = pathinfo($view, PATHINFO_EXTENSION);
        if($extension === "twig" && $this->container->has("twig"))
        {
            return $this->render($view, $params);
        }

        
        if(!$this->container->has("orion_engine_symfony"))
        {
            throw new \LogicException('You cannot use the "stream" method if the cortez97/symfony-orion-engine-bundle is not available.');
        }
        $this->container->get("orion_engine_symfony")->setServiceLocator($this->container);
        $result = $this->container->get("orion_engine_symfony")->view($view, $params, $this->getAllContainerServices());
        $response = new Response();
        $response->setContent($result);
        return $response;
    }

    public static function getSubscribedServices(): array
    {
        $current_services = parent::getSubscribedServices();
        $symfonyOrionServices = [
            "orion_engine_symfony"=>'?'.OrionEngineService::class
        ];
        $services = array_merge($current_services, $symfonyOrionServices);
        return $services;
    }

    private function getAllContainerServices():array
    {
        $services = [];


        if($this->container->has("orion_engine_symfony")){
            $services["orion_engine_symfony"] = $this->container->get("orion_engine_symfony");
        }
        
        if($this->container->has("router"))
        {
            $services["router"] = $this->container->get("router");
        }

        if($this->container->has("request_stack"))
        {
            $services["request_stack"] = $this->container->get("request_stack");
        }

        if($this->container->has("http_kernel"))
        {
            $services["http_kernel"] = $this->container->get("http_kernel");
        }

        if($this->container->has("serializer"))
        {
            $services["serializer"] = $this->container->get("serializer");
        }


        if($this->container->has("security.authorization_checker"))
        {
            $services["security.authorization_checker"] = $this->container->get("security.authorization_checker");
        }

        if($this->container->has("twig"))
        {
            $services["twig"] = $this->container->get("twig");
        }

        if($this->container->has("form.factory"))
        {
            $services["form.factory"] = $this->container->get("form.factory");
        }

        if($this->container->has("security.token_storage"))
        {
            $services["security.token_storage"] = $this->container->get("security.token_storage");
        }

        if($this->container->has("security.token_storage"))
        {
            $services["security.token_storage"] = $this->container->get("security.token_storage");
        }

        if($this->container->has("security.csrf.token_manager"))
        {
            $services["security.csrf.token_manager"] = $this->container->get("security.csrf.token_manager");
        }

        if($this->container->has("parameter_bag"))
        {
            $services["parameter_bag"] = $this->container->get("parameter_bag");
        }

        if($this->container->has('web_link.http_header_serializer'))
        {
            $services["web_link.http_header_serializer"] = $this->container->get("web_link.http_header_serializer");
        }

        
        $services["kernel"] = $this->kernel;

        
        return $services;
    }
}