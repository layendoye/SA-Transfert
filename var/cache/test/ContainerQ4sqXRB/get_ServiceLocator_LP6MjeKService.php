<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private '.service_locator.LP6MjeK' shared service.

return $this->privates['.service_locator.LP6MjeK'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, [
    'repoTrans' => ['privates', 'App\\Repository\\TransactionRepository', 'getTransactionRepositoryService.php', true],
], [
    'repoTrans' => 'App\\Repository\\TransactionRepository',
]);
