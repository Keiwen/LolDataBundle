<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\LolDataBundle\DependencyInjection\KeiwenLolDataExtension;
use Keiwen\RiotApi\Api\RiotApi;
use Keiwen\RiotApi\Services\ServiceRegistry;
use Keiwen\Utils\Curl\SimpleCurl;
use Keiwen\Utils\Parsing\HtmlParsing;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RiotApiVersions extends AbstractExternalDataService
{

    protected $url = 'https://developer.riotgames.com/api/methods';

    public function __construct(ContainerInterface $container, $cache, $defaultCacheLifetime, $cacheKeyPrefix)
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
        $this->url = $this->container->getParameter(KeiwenLolDataExtension::RIOT_URL_APIVERSIONS);
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param SimpleCurl $curl
     */
    protected function configureCurl(SimpleCurl $curl)
    {
        parent::configureCurl($curl);
        $curl->addOption(CURLOPT_SSL_VERIFYPEER, false);
    }

    protected function parseRawContent(string $raw)
    {
        //html code is not consistent, lot of unclosed tags, so parse it manually
        //isolate block of resources
//        $resources = HtmlParsing::parseHtmlElmt($raw, 'resources', true);
        $resources = explode('id="resources"', $raw);
        $resources = empty($resources[1]) ? '' : $resources[1];
        $resources = explode('id="footer"', $resources);
        $resources = empty($resources[0]) ? '' : $resources[0];

        //then should be ok
        $resourcesElmt = array();
        $classIteration = 1;
        while(true) {
            $resource = HtmlParsing::parseHtmlElmt($resources, 'resource', true, $classIteration);
            if(empty($resource)) break;
            $resourcesElmt[] = $resource;
            $classIteration++;
        }
        $apiVersions = array();
        foreach($resourcesElmt as $resource) {
            $resource = HtmlParsing::parseTag($resource, 'h2', true);
            $resource = HtmlParsing::parseTag($resource, 'span', true);
            $resource = explode('-', $resource);
            if(count($resource) > 1) {
                $last = array_pop($resource);
                $version = str_replace('v', '', $last);
                if(!is_numeric($version)) {
                    $version = '';
                    array_push($resource, $last);
                }
                $resource = implode('-', $resource);
            } else {
                $version = '';
                $resource = reset($resource);
            }
            $apiVersions[$resource] = $version;
        }

        $currentUse = ServiceRegistry::getServicesVersionsMap();

        return array(
            'diff' => $this->diffApiVersions($apiVersions, $currentUse),
            'riotApi' => $apiVersions,
            'currentlyUsed' => $currentUse,
        );
    }


    /**
     * @param array $riotVersions
     * @param array $currentVersions
     * @return array
     */
    protected function diffApiVersions(array $riotVersions, array $currentVersions)
    {
        $diff = array();
        foreach($riotVersions as $method => $riot) {
            switch(true) {
                case !isset($currentVersions[$method]):
                    //not used
                    $diff[$method] = 'Not used';
                    break;
                case $currentVersions[$method] != $riot:
                    //version unmatch
                    $diff[$method] = "Unmatch: Riot v$riot, current use v".$currentVersions[$method];
                    break;
            }
            //remove corresponding version found
            unset($currentVersions[$method]);
        }
        foreach($currentVersions as $method => $current) {
            $diff[$method] = 'Used while undefined';
        }
        return $diff;
    }

}