<?php

namespace Orion\OrionEngine\Utils;

use Orion\Orion;
use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OrionSymfony extends Orion
{

    private ServiceLocator $serviceLocator;

    public function __construct($configs){
        parent::__construct($configs);
    }

    public function setServiceLocator(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getUser(): ?UserInterface
    {
        if($this->serviceLocator == null){
            throw new \LogicException('Symfony Container undefined.');
        }

        if (!$this->serviceLocator->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (null === $token = $this->serviceLocator->get('security.token_storage')->getToken()) {
            return null;
        }

        return $token->getUser();
    }

    public function getFlashes(string $name):array{

        if($this->serviceLocator == null){
            throw new \LogicException('Symfony Container undefined.');
        }

        try {
            $session = $this->serviceLocator->get('request_stack')->getSession();
        } catch (SessionNotFoundException $e) {
            throw new \LogicException('You cannot use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".', 0, $e);
        }

        if (!$session instanceof FlashBagAwareSessionInterface) {
            throw new \LogicException(sprintf('You cannot use the addFlash method because class "%s" doesn\'t implement "%s".', get_debug_type($session), FlashBagAwareSessionInterface::class));
        }

        $flshes = $session->getFlashBag()->get($name);

        return $flshes;
    }

    protected function loadCustoms()
    {
        $custom_functions_file = dirname(__DIR__, 2).DIRECTORY_SEPARATOR."custom_functions.php";
        if(file_exists($custom_functions_file)){
            require_once $custom_functions_file;
        }
        parent::loadCustoms();
    }

}
