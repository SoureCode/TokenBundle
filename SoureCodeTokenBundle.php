<?php

namespace SoureCode\Bundle\Token;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SoureCodeTokenBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->addRegisterMappingsPass($container);
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        $modelDirectory = __DIR__.'/Resources/config/doctrine';

        $mappings = [
            $modelDirectory => 'SoureCode\Bundle\Token\Domain',
        ];

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                $mappings,
                ['sourecode.token.model_manager_name'],
                'sourecode.token.backend_type_orm',
                ['SoureCodeTokenBundle' => 'SoureCode\Bundle\Token\Domain']
            )
        );
    }
}
