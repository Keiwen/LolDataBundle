<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\LolDataBundle\DependencyInjection\KeiwenLolDataExtension;
use Keiwen\LolDataBundle\Exception\ExternalDataMissingParamException;
use Keiwen\Utils\Parsing\HtmlParsing;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LolChampion extends AbstractExternalDataService
{

    protected $url = 'http://gameinfo.euw.leagueoflegends.com/en/game-info/champions';

    const FRIENDS_DIV_NUMBER = 1;
    const RIVALS_DIV_NUMBER = 2;

    public function __construct(ContainerInterface $container, $cache, $defaultCacheLifetime, $cacheKeyPrefix)
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
        $this->url = $this->container->getParameter(KeiwenLolDataExtension::LOL_URL_CHAMPION);
    }

    public function getUrl()
    {
        if(empty($this->urlParameters['champion'])) {
            throw new ExternalDataMissingParamException($this->getName(), 'champion');
        }
        return $this->url . '/' . strtolower($this->urlParameters['champion']);
    }

    /**
     * @param string $champion
     * @return $this
     */
    public function setChampion(string $champion)
    {
        if(!empty($champion)) $this->urlParameters['champion'] = $champion;
        return $this;
    }


    /**
     * @return array|null
     */
    public function getContent(string $championKey = '')
    {
        $this->setChampion($championKey);
        return parent::getContent();
    }


    protected function parseRawContent(string $raw)
    {
        $info = HtmlParsing::parseHtmlElmt($raw, 'spacer');
        $affiliationId = HtmlParsing::parseTagAttribute($info, 'class');

        $friends = $this->getChampLinks($raw, static::FRIENDS_DIV_NUMBER);
        $rivals = $this->getChampLinks($raw, static::RIVALS_DIV_NUMBER);

        $videoPassive = HtmlParsing::parseTag($raw, 'video', false, 1);
        $videos = array();
        $videos['q'] = HtmlParsing::parseTag($raw, 'video', false, 2);
        $videos['w'] = HtmlParsing::parseTag($raw, 'video', false, 3);
        $videos['e'] = HtmlParsing::parseTag($raw, 'video', false, 4);
        $videos['r'] = HtmlParsing::parseTag($raw, 'video', false, 5);
        return array(
            'champKey' => $this->urlParameters['champion'],
            'affiliationId' => $affiliationId,
            'friends' => $friends,
            'rivals' => $rivals,
            'spellVideosHtml' => $videos,
            'passiveVideoHtml' => $videoPassive,
        );
    }


    /**
     * @param string $htmlContent
     * @param int    $divNumber
     * @return array|string
     */
    protected function getChampLinks(string $htmlContent, int $divNumber)
    {
        $links = HtmlParsing::parseHtmlElmt($htmlContent, 'grid-list gs-no-gutter champion-grid', false, $divNumber);
        $links = HtmlParsing::parseTagList($links, 'li');
        foreach($links as &$link) {
            $link = HtmlParsing::parseTag($link, 'div', false, 2);
            $link = HtmlParsing::parseTagAttribute($link, 'data-rg-id');
        }
        return $links;
    }


    /**
     * @param string $championKey
     * @return bool
     */
    public function isCached(string $championKey = '')
    {
        $this->setChampion($championKey);
        $content = $this->readInCache($this->getServiceKey());
        return $content !== null;
    }


    /**
     * @return string
     */
    public function getAffiliationId()
    {
        return $this->getContentPart('affiliationId');
    }

    /**
     * @return string[]
     */
    public function getFriends()
    {
        return $this->getContentPart('friends');
    }

    /**
     * @return string[]
     */
    public function getRivals()
    {
        return $this->getContentPart('rivals');
    }

    /**
     * @return string[]
     */
    public function getSpellVideosHtml()
    {
        return $this->getContentPart('spellVideosHtml');
    }

    /**
     * @return string
     */
    public function getPassiveVideoHtml()
    {
        return $this->getContentPart('passiveVideoHtml');
    }


}