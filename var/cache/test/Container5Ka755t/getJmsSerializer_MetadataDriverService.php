<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'jms_serializer.metadata_driver' shared service.

include_once $this->targetDirs[3].'/vendor/jms/metadata/src/Driver/DriverInterface.php';
include_once $this->targetDirs[3].'/vendor/jms/serializer/src/Metadata/Driver/AbstractDoctrineTypeDriver.php';
include_once $this->targetDirs[3].'/vendor/jms/serializer/src/Metadata/Driver/DoctrineTypeDriver.php';

return $this->services['jms_serializer.metadata_driver'] = new \JMS\Serializer\Metadata\Driver\DoctrineTypeDriver(($this->privates['jms_serializer.metadata.chain_driver'] ?? $this->load('getJmsSerializer_Metadata_ChainDriverService.php')), ($this->services['doctrine'] ?? $this->getDoctrineService()));
