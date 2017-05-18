<?php

namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\Services\RememberMeOverride;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface {

  public function process (ContainerBuilder $container) {
    $definition = $container->getDefinition('security.authentication.rememberme.services.simplehash');
    $definition->setClass(RememberMeOverride::class);
  }

}