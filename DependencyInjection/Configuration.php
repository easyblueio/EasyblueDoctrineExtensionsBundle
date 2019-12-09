<?php

declare(strict_types = 1);

/*
 * This file is part of the EasyblueDoctrineExtensionsBundle project.
 * (c) Easyblue <support@easyblue.io>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Easyblue\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('easyblue_doctrine_extensions');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->beforeNormalization()
            ->always(function (array $config) {
                if (empty($config)) {
                    return [
                        'sluggable'     => true,
                        'softdeletable' => true,
                        'timestampable' => true,
                    ];
                }

                return $config;
            })
            ->end()
            ->children()
            ->booleanNode('sluggable')->defaultFalse()->treatNullLike(false)->end()
            ->booleanNode('softdeletable')->defaultFalse()->treatNullLike(false)->end()
            ->booleanNode('timestampable')->defaultFalse()->treatNullLike(false)->end()
            ->end();

        return $treeBuilder;
    }
}
