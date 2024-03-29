<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.aTNbF6n' shared service.

return $this->privates['.service_locator.aTNbF6n'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'repo' => ['privates', 'App\\Repository\\UserCompteActuelRepository', 'getUserCompteActuelRepositoryService.php', true],
    'serializer' => ['services', 'serializer', 'getSerializerService', false],
    'userConnecte' => ['privates', '.errored..service_locator.aTNbF6n.Symfony\\Component\\Security\\Core\\User\\UserInterface', NULL, 'Cannot autowire service ".service_locator.aTNbF6n": it references interface "Symfony\\Component\\Security\\Core\\User\\UserInterface" but no such service exists. Did you create a class that implements this interface?'],
], [
    'repo' => 'App\\Repository\\UserCompteActuelRepository',
    'serializer' => '?',
    'userConnecte' => 'Symfony\\Component\\Security\\Core\\User\\UserInterface',
]);
