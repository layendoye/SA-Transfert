<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'jms_serializer.expression_evaluator' shared service.

include_once $this->targetDirs[3].'/vendor/jms/serializer/src/Expression/CompilableExpressionEvaluatorInterface.php';
include_once $this->targetDirs[3].'/vendor/jms/serializer/src/Expression/ExpressionEvaluatorInterface.php';
include_once $this->targetDirs[3].'/vendor/jms/serializer/src/Expression/ExpressionEvaluator.php';

return $this->privates['jms_serializer.expression_evaluator'] = new \JMS\Serializer\Expression\ExpressionEvaluator(($this->privates['jms_serializer.expression_language'] ?? $this->load('getJmsSerializer_ExpressionLanguageService.php')), ['container' => $this]);
