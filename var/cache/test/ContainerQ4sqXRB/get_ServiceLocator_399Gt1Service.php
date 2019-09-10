<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator._399Gt1' shared service.

return $this->privates['.service_locator._399Gt1'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'repoTrans' => ['privates', 'App\\Repository\\TransactionRepository', 'getTransactionRepositoryService.php', true],
    'serializer' => ['services', 'serializer', 'getSerializerService', false],
    'user' => ['privates', '.errored..service_locator._399Gt1.App\\Entity\\Utilisateur', NULL, 'Cannot autowire service ".service_locator._399Gt1": it references class "App\\Entity\\Utilisateur" but no such service exists.'],
    'userConnecte' => ['privates', '.errored..service_locator._399Gt1.Symfony\\Component\\Security\\Core\\User\\UserInterface', NULL, 'Cannot autowire service ".service_locator._399Gt1": it references interface "Symfony\\Component\\Security\\Core\\User\\UserInterface" but no such service exists. Did you create a class that implements this interface?'],
], [
    'repoTrans' => 'App\\Repository\\TransactionRepository',
    'serializer' => '?',
    'user' => 'App\\Entity\\Utilisateur',
    'userConnecte' => 'Symfony\\Component\\Security\\Core\\User\\UserInterface',
]);