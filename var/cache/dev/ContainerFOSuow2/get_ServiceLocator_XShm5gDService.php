<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.xShm5gD' shared service.

return $this->privates['.service_locator.xShm5gD'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'caissier' => ['privates', '.errored..service_locator.xShm5gD.App\\Entity\\Utilisateur', NULL, 'Cannot autowire service ".service_locator.xShm5gD": it references class "App\\Entity\\Utilisateur" but no such service exists.'],
    'repoDepot' => ['privates', 'App\\Repository\\DepotRepository', 'getDepotRepositoryService.php', true],
    'serializer' => ['services', 'serializer', 'getSerializerService', false],
], [
    'caissier' => 'App\\Entity\\Utilisateur',
    'repoDepot' => 'App\\Repository\\DepotRepository',
    'serializer' => '?',
]);