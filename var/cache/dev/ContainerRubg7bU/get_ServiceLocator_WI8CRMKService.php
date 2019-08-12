<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.wI8CRMK' shared service.

return $this->privates['.service_locator.wI8CRMK'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'entreprise' => ['privates', '.errored..service_locator.wI8CRMK.App\\Entity\\Entreprise', NULL, 'Cannot autowire service ".service_locator.wI8CRMK": it references class "App\\Entity\\Entreprise" but no such service exists.'],
    'manager' => ['services', 'doctrine.orm.default_entity_manager', 'getDoctrine_Orm_DefaultEntityManagerService', false],
    'validator' => ['services', 'validator', 'getValidatorService', false],
], [
    'entreprise' => 'App\\Entity\\Entreprise',
    'manager' => '?',
    'validator' => '?',
]);
