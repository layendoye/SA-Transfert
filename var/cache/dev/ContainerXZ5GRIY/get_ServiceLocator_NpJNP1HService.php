<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.NpJNP1H' shared service.

return $this->privates['.service_locator.NpJNP1H'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'encoder' => ['services', 'security.password_encoder', 'getSecurity_PasswordEncoderService.php', true],
    'manager' => ['services', 'doctrine.orm.default_entity_manager', 'getDoctrine_Orm_DefaultEntityManagerService', false],
    'validator' => ['services', 'validator', 'getValidatorService', false],
], [
    'encoder' => '?',
    'manager' => '?',
    'validator' => '?',
]);