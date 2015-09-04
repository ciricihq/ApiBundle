<?php
/**
 * SimpleDoctrineMapping for Symfony2
 */
namespace Cirici\ApiBundle\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass;

/**
 * Class MappingCompilerPass
 */
class MappingCompilerPass extends AbstractMappingCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this
            ->addEntityMapping(
                $container,
                'api_bundle.entity.user.entity_manager',
                'api_bundle.entity.user.class',
                'api_bundle.entity.user.mapping_file_path',
                'api_bundle.entity.user.enable'
            )
            ->addEntityMapping(
                $container,
                'default',
                'Cirici\ApiBundle\Entity\Client',
                '@CiriciApiBundle/Resources/config/doctrine/Client.orm.yml',
                true
            )
            ->addEntityMapping(
                $container,
                'default',
                'Cirici\ApiBundle\Entity\AccessToken',
                '@CiriciApiBundle/Resources/config/doctrine/AccessToken.orm.yml',
                true
            )
            ->addEntityMapping(
                $container,
                'default',
                'Cirici\ApiBundle\Entity\RefreshToken',
                '@CiriciApiBundle/Resources/config/doctrine/RefreshToken.orm.yml',
                true
            )
            ->addEntityMapping(
                $container,
                'default',
                'Cirici\ApiBundle\Entity\AuthCode',
                '@CiriciApiBundle/Resources/config/doctrine/AuthCode.orm.yml',
                true
            )
        ;
    }
}
