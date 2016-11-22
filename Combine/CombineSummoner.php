<?php

namespace Keiwen\LolDataBundle\Combine;


use Keiwen\LolDataBundle\Model\CombinedSummoner;
use Keiwen\RiotApi\Api\RiotApi;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CombineSummoner extends AbstractCombine
{
    use CacheHandlerTrait;

    protected $switchOpgg = false;

    public function __construct(ContainerInterface $container, $cache = null, int $defaultCacheLifetime = 0, string $cacheKeyPrefix = '')
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
    }

    protected function getCombineId()
    {
        $conf = array();
        $conf[] = 'opg' . $this->switchOpgg;
        return implode('', $conf);
    }


    /**
     * @deprecated false by default
     * @param bool $switch
     */
    public function switchOpggData(bool $switch)
    {
        $this->switchOpgg = $switch;
    }

    /**
     * @param int    $id empty to search by name
     * @param string $name optional if id filled
     * @param string $server
     * @return array|\Keiwen\RiotApi\Dto\Summoner\SummonerDto|null|\stdClass|string|CombinedSummoner
     */
    public function getSummoner($id = 0, string $name = '', string $server = '')
    {
        $cacheKey = 'summoner.' . empty($id) ? "name.$name" : "id.$id";
        $summoner = $this->readInCache($cacheKey);
        if($summoner !== null) return $summoner;
        $summoner = $this->getSummonerRegional($id, $name, $server);
        //if dto received, build the combined dto
        if(RiotApi::detectOutputFormat($summoner) == RiotApi::FORMAT_DTO) {
            $summoner = CombinedSummoner::generateFromParent($summoner);
            $summoner->setServer($server);
        }

        //optionnal data
        if($this->switchOpgg) {
            $opgg = $this->getSummonerOpgg($name, $server);
            $summoner = $this->combineContent($summoner, $opgg, 'opgg');
        }

        $this->storeInCache($cacheKey, $summoner);
        return $summoner;
    }

    /**
     * @param int    $id empty to search by name
     * @param string $name optional if id filled
     * @param string $server
     * @return array|\Keiwen\RiotApi\Dto\Summoner\SummonerDto|\stdClass|string
     */
    protected function getSummonerRegional($id = 0, string $name = '', string $server = '')
    {
        $service = $this->container->get('keiwen_riot_api.api.regional');
        $service = $service->getServiceSummoner();
        if(empty($id)) {
            //search by name
            $content = $service->getByName(array($name), $server);
        } else {
            //search by id
            $content = $service->get(array($id), $server);
        }
        //array of summoner. If array type received, get first.
        if(is_array($content)) return reset($content);
        //here we have json or stdclass
        switch (RiotApi::detectOutputFormat($content)) {
            case RiotApi::FORMAT_STDCLASS:
                $array = json_decode(json_encode($content), true);
                $content = reset($array);
                return json_decode(json_encode($content));
            case RiotApi::FORMAT_JSON:
                $array = json_decode($content, true);
                $content = reset($array);
                return json_encode($content);
        }

        return $content;
    }


    /**
     * @deprecated
     * @param string $name
     * @param string $server
     * @return mixed|null
     */
    protected function getSummonerOpgg(string $name, string $server)
    {
        $service = $this->container->get('keiwen_loldata.external.opggprofile');
        $service->setSummonerName($name)->setServer($server);
        $content = $service->getContent();
        return $content;
    }




}
