<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.pOqsW2f' shared service.

return $this->privates['.service_locator.pOqsW2f'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'entreprise' => ['privates', '.errored..service_locator.pOqsW2f.App\\Entity\\Entreprise', NULL, 'Cannot autowire service ".service_locator.pOqsW2f": it references class "App\\Entity\\Entreprise" but no such service exists.'],
    'repoTrans' => ['privates', 'App\\Repository\\TransactionRepository', 'getTransactionRepositoryService.php', true],
    'serializer' => ['services', 'serializer', 'getSerializerService', false],
    'userConnecte' => ['privates', '.errored..service_locator.pOqsW2f.Symfony\\Component\\Security\\Core\\User\\UserInterface', NULL, 'Cannot autowire service ".service_locator.pOqsW2f": it references interface "Symfony\\Component\\Security\\Core\\User\\UserInterface" but no such service exists. Did you create a class that implements this interface?'],
], [
    'entreprise' => 'App\\Entity\\Entreprise',
    'repoTrans' => 'App\\Repository\\TransactionRepository',
    'serializer' => '?',
    'userConnecte' => 'Symfony\\Component\\Security\\Core\\User\\UserInterface',
]);