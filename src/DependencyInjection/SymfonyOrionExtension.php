<?php

namespace Orion\OrionEngine\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonyOrionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $dir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "RESOURCES" . DIRECTORY_SEPARATOR . "config";
        $loader = new YamlFileLoader($container, new FileLocator($dir));
        $loader->load("services.yaml");

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Verifica se o bundle webpack_encore está registrado no container
        $encoreConfigs = $container->getExtensionConfig('webpack_encore');
        if (!empty($encoreConfigs)) {
            $firstEncoreConfig = $encoreConfigs[0];
            if (isset($firstEncoreConfig['output_path'])) {
                $config['encore_output_path'] = $firstEncoreConfig['output_path'];
            }
        }

        // Registra todos os parâmetros com prefixo
        foreach ($config as $key => $value) {
            $container->setParameter('symfony_orion.' . $key, $value);
        }

        // Também registra a config completa (caso precise como array no serviço)
        $container->setParameter('symfony_orion.config', $config);
    }
}
