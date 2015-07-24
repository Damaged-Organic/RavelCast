<?php
// Common/Compiler/DoctrineEntityListenerPass.php
namespace Common\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineEntityListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('doctrine.entity_listener_resolver');
        $services   = $container->findTaggedServiceIds('doctrine.entity_listener');

        foreach ($services as $service => $attributes)
        {
            $definition->addMethodCall(
                'addMapping',
                [$container->getDefinition($service)->getClass(), $service]
            );
        }
    }
}
