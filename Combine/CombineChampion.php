<?php

namespace Keiwen\LolDataBundle\Combine;


use Keiwen\Utils\Mutator\ArrayMutator;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChampionCombine extends AbstractCombine
{
    use CacheHandlerTrait;

    protected $switchRegional = true;
    protected $switchWikia = true;
    protected $switchLolking = true;

    public function __construct(ContainerInterface $container, $cache = null, int $defaultCacheLifetime = 0, string $cacheKeyPrefix = '')
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
    }

    protected function getCombineId()
    {
        $conf = array();
        $conf[] = 'rar' . $this->switchRegional;
        $conf[] = 'wik' . $this->switchWikia;
        $conf[] = 'lkg' . $this->switchLolking;
        return implode('', $conf);
    }


    public function switchRegionalData(bool $switch)
    {
        $this->switchRegional = $switch;
    }

    public function switchWikiaData(bool $switch)
    {
        $this->switchWikia = $switch;
    }

    public function switchLolkingData(bool $switch)
    {
        $this->switchLolking = $switch;
    }


    /**
     * @param string $locale
     * @param string $version
     * @return array|\Keiwen\RiotApi\Dto\Champion\ChampionDto[]|\stdClass|string
     */
    public function getChampions(string $locale = '', string $version = '')
    {
        $champions = $this->readInCache('champions');
        if($champions !== null) return $champions;
        $champions = $this->getChampionsGlobal($locale, $version);
        $refChampions = $this->retrieveOutputFieldList($champions, 'name');

        //optionnal data
        if($this->switchRegional) {
            $regional = $this->getChampionsRegional();
            //todo new DTO class
            //todo set service and test
            $champions = $this->combineContentList($champions, $regional, 'id', 'regional');
        }
        if($this->switchWikia) {
            $wikia = $this->getChampionsWikia();
            $wikia = ArrayMutator::convertKeysWithRefMap($wikia, 'champion', $refChampions);
            $champions = $this->combineContentList($champions, $wikia, '', 'wikia');
        }
        if($this->switchLolking) {
            $lolking = $this->getChampionsLolking();
            $lolking = ArrayMutator::convertKeysWithRefMap($lolking, 'champion', $refChampions);
            $champions = $this->combineContentList($champions, $lolking, '', 'lolking');
        }

        $this->storeInCache('champions', $champions);
        return $champions;
    }

    /**
     * @param string $locale
     * @param string $version
     * @return \Keiwen\RiotApi\Dto\Champion\ChampionDto[]|array|string|\stdClass
     */
    protected function getChampionsGlobal(string $locale = '', string $version = '')
    {
        $service = $this->container->get('keiwen_riot_api.api.global');
        $content = $service->getServiceStaticData()->getChampions($locale, $version, true, array('all'));
        //extract list of champions in 'data' field
        return $this->retrieveOutputField($content, 'data');
    }

    /**
     * @return \Keiwen\RiotApi\Dto\Champion\ChampionInfoDto[]|array|string|\stdClass
     */
    protected function getChampionsRegional()
    {
        $service = $this->container->get('keiwen_riot_api.api.regional');
        $content = $service->getServiceChampion()->getChampions();
        //extract list of champions in 'champions' field
        return $this->retrieveOutputField($content, 'champions');
    }

    /**
     * @return array
     */
    protected function getChampionsWikia()
    {
        $service = $this->container->get('keiwen_loldata.external.wikiachampions');
        $content = $service->getContent();
        //extract list of champions in 'champions' field
        return $this->retrieveOutputField($content, 'champions');
    }

    /**
     * @return array
     */
    protected function getChampionsLolking()
    {
        $service = $this->container->get('keiwen_loldata.external.lolkingchampions');
        $content = $service->getContent();
        //extract list of champions in 'champions' field
        return $this->retrieveOutputField($content, 'champions');
    }





}