<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.3GSHdrB' shared service.

return $this->privates['.service_locator.3GSHdrB'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'manager' => ['services', 'doctrine.orm.default_entity_manager', 'getDoctrine_Orm_DefaultEntityManagerService', false],
    'repoTrans' => ['privates', 'App\\Repository\\TransactionRepository', 'getTransactionRepositoryService.php', true],
    'repoUserComp' => ['privates', 'App\\Repository\\UserCompteActuelRepository', 'getUserCompteActuelRepositoryService.php', true],
    'userConnecte' => ['privates', '.errored..service_locator.3GSHdrB.Symfony\\Component\\Security\\Core\\User\\UserInterface', NULL, 'Cannot autowire service ".service_locator.3GSHdrB": it references interface "Symfony\\Component\\Security\\Core\\User\\UserInterface" but no such service exists. Did you create a class that implements this interface?'],
    'validator' => ['services', 'validator', 'getValidatorService', false],
], [
    'manager' => '?',
    'repoTrans' => 'App\\Repository\\TransactionRepository',
    'repoUserComp' => 'App\\Repository\\UserCompteActuelRepository',
    'userConnecte' => 'Symfony\\Component\\Security\\Core\\User\\UserInterface',
    'validator' => '?',
]);