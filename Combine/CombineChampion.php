<?php

namespace Keiwen\LolDataBundle\Combine;


use Keiwen\LolDataBundle\Model\CombinedChampion;
use Keiwen\RiotApi\Api\RiotApi;
use Keiwen\Utils\Mutator\ArrayMutator;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CombineChampion extends AbstractCombine
{
    use CacheHandlerTrait;

    protected $switchRegional = true;
    protected $switchWikia = true;
    protected $switchLol = false;
    protected $switchLolking = true;
    protected $switchChampiongg = true;

    public function __construct(ContainerInterface $container, $cache = null, int $defaultCacheLifetime = 0, string $cacheKeyPrefix = '')
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
    }

    protected function getCombineId()
    {
        $conf = array();
        $conf[] = 'rar' . $this->switchRegional;
        $conf[] = 'wik' . $this->switchWikia;
        $conf[] = 'lol' . $this->switchLol;
        $conf[] = 'lkg' . $this->switchLolking;
        $conf[] = 'cgg' . $this->switchChampiongg;
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


    public function switchLolData(bool $switch)
    {
        $this->switchLol = $switch;
    }

    public function switchChampionggData(bool $switch)
    {
        $this->switchChampiongg = $switch;
    }


    /**
     * @param string $locale
     * @param string $version
     * @return array|\Keiwen\RiotApi\Dto\Champion\ChampionDto[]|\stdClass|string|CombinedChampion[]
     */
    public function getChampions(string $locale = '', string $version = '')
    {
        $champions = $this->readInCache('champions');
        if($champions !== null && false) return $champions;
        $champions = $this->getChampionsGlobal($locale, $version);
        $refChampions = $this->retrieveOutputFieldList($champions, 'name', true);
        //if dto received, build the combined dto
        if(RiotApi::detectOutputFormat($champions) == RiotApi::FORMAT_DTO) {
            foreach($champions as &$championDto) {
                $championDto = CombinedChampion::generateFromParent($championDto);
            }
        }

        //optionnal data
        if($this->switchRegional) {
            $regional = $this->getChampionsRegional();
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
        if($this->switchLol) {
            $championsList = $champions;
            switch(RiotApi::detectOutputFormat($champions)) {
                case RiotApi::FORMAT_STDCLASS: $championsList = json_decode(json_encode($champions), true); break;
                case RiotApi::FORMAT_JSON: $championsList = json_decode($champions, true); break;
            }
            $lol = array();
            foreach($championsList as $championElmt) {
                if(RiotApi::detectOutputFormat($champions) == RiotApi::FORMAT_DTO) {
                    $champKey = $championElmt->getKey();
                    $champName = $championElmt->getName();
                } else {
                    $champKey = $championElmt['key'];
                    $champName = $championElmt['name'];
                }
                $lolChamp = $this->getChampionLol($champKey);
                $lolChamp['champion'] = $champName;
                $lol[] = $lolChamp;
            }
            $lol = ArrayMutator::convertKeysWithRefMap($lol, 'champion', $refChampions);
            $champions = $this->combineContent($champions, $lol, '', 'lolking');
        }
        if($this->switchChampiongg) {
            $championgg = $this->getChampionsChampiongg();
            $championgg = ArrayMutator::convertKeysWithRefMap($championgg, 'champion', $refChampions);
            $champions = $this->combineContent($champions, $championgg, '', 'championgg');
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
    
    /**
     * @return array
     */
    protected function getChampionLol(string $champKey)
    {
        $service = $this->container->get('keiwen_loldata.external.lolchampion');
        if(!$service->isCached($champKey)) {
            return array();
        }
        $content = $service->getContent($champKey);
        //extract list of champions in 'champions' field
        return $content;
    }

    /**
     * @return array
     */
    protected function getChampionsChampiongg()
    {
        $service = $this->container->get('keiwen_loldata.external.championggchampions');
        $content = $service->getContent();
        //extract list of champions in 'champions' field
        return $this->retrieveOutputField($content, 'champions');
    }





}
