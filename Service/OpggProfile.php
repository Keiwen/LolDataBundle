<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\LolDataBundle\DependencyInjection\KeiwenLolDataExtension;
use Keiwen\LolDataBundle\Exception\ExternalDataInvalidContentException;
use Keiwen\LolDataBundle\Exception\ExternalDataMissingParamException;
use Keiwen\Utils\Parsing\HtmlParsing;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @deprecated seems that opgg is somehow blocking automatic request
 */
class OpggProfile extends AbstractExternalDataService
{

    protected $url = 'http://{server}.op.gg/summoner/userName={summonerName}';

    public function __construct(ContainerInterface $container, $cache, $defaultCacheLifetime, $cacheKeyPrefix)
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
        $this->url = $this->container->getParameter(KeiwenLolDataExtension::OPGG_URL_PROFILE);
    }


    public function getUrl()
    {
        if(empty($this->urlParameters['server'])) {
            throw new ExternalDataMissingParamException($this->getName(), 'server');
        }
        if(empty($this->urlParameters['summonerName'])) {
            throw new ExternalDataMissingParamException($this->getName(), 'summonerName');
        }
        $urlPattern = $this->url;
        $urlPattern = str_replace('{server}', $this->urlParameters['server'], $urlPattern);
        $urlPattern = str_replace('{summonerName}', $this->urlParameters['summonerName'], $urlPattern);
        return $urlPattern;
    }

    /**
     * @param string $server
     * @return $this
     */
    public function setServer(string $server)
    {
        $this->urlParameters['server'] = $server;
        return $this;
    }

    /**
     * @param string $summonerName
     * @return $this
     */
    public function setSummonerName(string $summonerName)
    {
        $this->urlParameters['summonerName'] = $summonerName;
        return $this;
    }



    protected function parseRawContent(string $raw)
    {
        $ladderRankElmt = HtmlParsing::parseHtmlElmt($raw, 'LadderRank', true, 1);
        if(empty($ladderRankElmt)) throw new ExternalDataInvalidContentException($this->getName(), 'ladder rank not found');
        $ranking = HtmlParsing::parseHtmlElmt($ladderRankElmt, 'ranking', true, 1);
        $ranking = (int) str_replace(',', '', $ranking);
        $pregMatch = array();
        $percentFromTop = 0;
        if(preg_match('/\(([0-9]+(\.[0-9]+)?)% of top\)/', $ladderRankElmt, $pregMatch) and !empty($pregMatch[1])) {
            $percentFromTop = $pregMatch[1];
        }

        return array(
            'ranking' => $ranking,
            'percentFromTop' => $percentFromTop,
        );
    }

    /**
     * @deprecated seems that opgg is somehow blocking automatic request
     * @inheritdoc
     */
    public function getContent()
    {
        return parent::getContent();
    }

    /**
     * @deprecated seems that opgg is somehow blocking automatic request
     * @return mixed|null
     */
    public function getRanking()
    {
        return $this->getContentPart('ranking');
    }

    /**
     * @deprecated seems that opgg is somehow blocking automatic request
     * @return mixed|null
     */
    public function getPercentFromTop()
    {
        return $this->getContentPart('percentFromTop');
    }

}