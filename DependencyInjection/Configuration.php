<?php

namespace SoureCode\Bundle\Token\DependencyInjection;

use DateInterval;
use Exception;
use SoureCode\Bundle\Token\Exception\LogicException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('soure_code_token');

        /**
         * @var ArrayNodeDefinition $rootNode
         */
        $rootNode = $treeBuilder->getRootNode();

        // @formatter:off
        $rootNode
            ->children()
            ->scalarNode('model_manager_name')
            ->defaultNull()
        ;

        $this->addTokenNode($rootNode);
        // @formatter:on

        return $treeBuilder;
    }

    protected function addTokenNode(ArrayNodeDefinition $rootNode): void
    {
        // @formatter:off
        $tokenNode = $rootNode
            ->fixXmlConfig('token', 'tokens')
            ->children()
            ->arrayNode('tokens')
            ->defaultValue([])
            ->useAttributeAsKey('type')
            ->arrayPrototype();

        $tokenNode
            ->children()
                ->scalarNode('expiration')
                ->isRequired()
                ->validate()
                    ->ifTrue(function (string $value) {
                        try {
                            new DateInterval($value);

                            return false;
                        } catch (Exception $exception) {
                            throw new LogicException('Invalid date interval.', -1, $exception);
                        }
                    })
                    ->thenInvalid('Invalid token duration.');

        $tokenNode
            ->children()
                ->scalarNode('length')
                ->isRequired()
                ->validate()
                ->ifTrue(function (int $value) {
                    return $value < 4;
                })
                ->thenInvalid('Invalid token length.');
        // @formatter:on
    }
}
