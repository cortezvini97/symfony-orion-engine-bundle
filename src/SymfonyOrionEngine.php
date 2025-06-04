<?php

namespace Orion\OrionEngine;

use Orion\OrionEngine\DependencyInjection\SymfonyOrionExtension;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SymfonyOrionEngine extends AbstractBundle{
    public function getPath():string{
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SymfonyOrionExtension();
    }

     protected function getParameter(string $name): array|bool|string|int|float|\UnitEnum|null
    {
        if (!$this->container->has('parameter_bag')) {
            throw new ServiceNotFoundException('parameter_bag.', null, null, [], sprintf('The "%s::getParameter()" method is missing a parameter bag to work properly. Did you forget to register your controller as a service subscriber? This can be fixed either by using autoconfiguration or by manually wiring a "parameter_bag" in the service locator passed to the controller.', static::class));
        }

        return $this->container->get('parameter_bag')->get($name);
    }
}