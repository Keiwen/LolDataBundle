<?php

namespace Keiwen\LolDataBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class KeiwenLolDataExtension extends ConfigurableExtension
{

    const LOLKING_USESTATRANGE = 'keiwen_loldata.lolking.use_stat_range';
    const CHAMPIONGG_USESTATRANGE = 'keiwen_loldata.championgg.use_stat_range';

    const LOLKING_URL_CHAMPION = 'keiwen_loldata.lolking.url_champion';
    const WIKIA_URL_CHAMPION = 'keiwen_loldata.wikia.url_champion';
    const OPGG_URL_PROFILE = 'keiwen_loldata.opgg.url_profile';
    const CHAMPIONGG_URL_CHAMPION = 'keiwen_loldata.championgg.url_champion';
    const RIOT_URL_APIVERSIONS = 'keiwen_loldata.riot.url_apiversions';

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $container->setParameter('keiwen_loldata.cache_lifetime', $mergedConfig['cache_lifetime']);
        $container->setParameter('keiwen_loldata.cache_prefix', $mergedConfig['cache_prefix']);

        $container->setParameter(self::LOLKING_USESTATRANGE, $mergedConfig['lolking']['use_stat_range']);
        $container->setParameter(self::CHAMPIONGG_USESTATRANGE, $mergedConfig['championgg']['use_stat_range']);

        $container->setParameter(self::LOLKING_URL_CHAMPION, $mergedConfig['lolking']['url_champion']);
        $container->setParameter(self::WIKIA_URL_CHAMPION, $mergedConfig['wikia']['url_champion']);
        $container->setParameter(self::OPGG_URL_PROFILE, $mergedConfig['opgg']['url_profile']);
        $container->setParameter(self::CHAMPIONGG_URL_CHAMPION, $mergedConfig['championgg']['url_champion']);
        $container->setParameter(self::RIOT_URL_APIVERSIONS, $mergedConfig['riot']['url_apiversions']);

        $this->setCacheParameters($mergedConfig, $container, 'lolking');
        $this->setCacheParameters($mergedConfig, $container, 'wikia');
        $this->setCacheParameters($mergedConfig, $container, 'opgg');
        $this->setCacheParameters($mergedConfig, $container, 'championgg');
        $this->setCacheParameters($mergedConfig, $container, 'riot');

        $loader->load('services.yml');
    }


    /**
     * set cache lifetime and prefix in parameters container for given service
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     * @param string           $dataServiceName
     */
    public function setCacheParameters(array $mergedConfig, ContainerBuilder $container, string $dataServiceName)
    {
        $cacheLifetime = isset($mergedConfig[$dataServiceName]['cache_lifetime']) ?
            $mergedConfig[$dataServiceName]['cache_lifetime'] : $mergedConfig['cache_lifetime'];
        $cachePrefix = isset($mergedConfig[$dataServiceName]['cache_prefix']) ?
            $mergedConfig[$dataServiceName]['cache_prefix'] : $mergedConfig['cache_prefix'];
        $container->setParameter("keiwen_loldata.$dataServiceName.cache_lifetime", $cacheLifetime);
        $container->setParameter("keiwen_loldata.$dataServiceName.cache_prefix", $cachePrefix);
    }

}