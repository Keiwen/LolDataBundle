<?php


namespace Keiwen\LolDataBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{


    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('keiwen_loldata');

        $rootNode
            ->children()
                ->integerNode('cache_lifetime')->defaultValue(0)->end()
                ->scalarNode('cache_prefix')->defaultValue('')->end()
                ->arrayNode('wikia')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('url_champion')->defaultValue('http://leagueoflegends.wikia.com/wiki/List_of_champions')->cannotBeEmpty()->end()
                        ->integerNode('cache_lifetime')->end()
                        ->scalarNode('cache_prefix')->end()
                    ->end()
                ->end()
                ->arrayNode('lolking')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('url_champion')->defaultValue('http://www.lolking.net/champions')->cannotBeEmpty()->end()
                        ->integerNode('cache_lifetime')->end()
                        ->scalarNode('cache_prefix')->end()
                        ->arrayNode('use_stat_range')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('popularity')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode('min')->defaultValue(0)->min(0)->end()
                                        ->integerNode('max')->defaultValue(25)->max(100)->end()
                                    ->end()
                                ->end()
                                ->arrayNode('winRate')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode('min')->defaultValue(40)->min(0)->end()
                                        ->integerNode('max')->defaultValue(60)->max(100)->end()
                                    ->end()
                                ->end()
                                ->arrayNode('banRate')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode('min')->defaultValue(0)->min(0)->end()
                                        ->integerNode('max')->defaultValue(70)->max(100)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('opgg')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('url_profile')->defaultValue('http://{server}.op.gg/summoner/userName={summonerName}')->cannotBeEmpty()->end()
                        ->integerNode('cache_lifetime')->end()
                        ->scalarNode('cache_prefix')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }




}
