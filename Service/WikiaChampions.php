<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\LolDataBundle\DependencyInjection\KeiwenLolDataExtension;
use Keiwen\Utils\Parsing\HtmlParsing;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WikiaChampions extends AbstractExternalDataService
{

    protected $url = 'http://leagueoflegends.wikia.com/wiki/List_of_champions';

    public function __construct(ContainerInterface $container, $cache, $defaultCacheLifetime, $cacheKeyPrefix)
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
        $this->url = $this->container->getParameter(KeiwenLolDataExtension::WIKIA_URL_CHAMPION);
    }

    public function getUrl()
    {
        return $this->url;
    }


    protected function parseRawContent(string $raw)
    {
        $wikiContent = HtmlParsing::parseHtmlElmt($raw, 'mw-content-text', true);
        $tables = HtmlParsing::parseTagList($wikiContent, 'table', true);
        $tableChampions = empty($tables[1]) ? '' : $tables[1]; //0 is caption;
        $rows = HtmlParsing::parseTagList($tableChampions, 'tr', true);
        $rowTitle = array_shift($rows);
        $rowTitle = HtmlParsing::parseTagList($rowTitle, 'th', true);
        $ipList = array();
        $rpList = array();
        $releaseList = array();
        foreach($rowTitle as &$title) {
            //all in span
            $title = HtmlParsing::parseTag($title, 'span', true);
            //except if image in link
            if(strpos($title, '<a') !== false) {
                $title = HtmlParsing::parseTag($title, 'a', true);
                $title = HtmlParsing::parseTagAttribute($title, 'alt');
            }
            $title = lcfirst($title);
        }
        foreach($rows as &$row) {
            $row = HtmlParsing::parseTagList($row, 'td', true);
            $row = array_combine($rowTitle, $row);
            foreach($row as &$col) {
                $col = trim($col);
            }
            //champion name
            if(empty($row['champion'])) {
                $row['champion'] = '';
            } else {
                $row['champion'] = HtmlParsing::parseTag($row['champion'], 'a', true, 2);
                $row['champion'] = str_replace('&#039;', "'", $row['champion']);
            }
            //cost RP/IP
            $rpList[] = $this->sanitizeCost($row, 'rP');
            $ipList[] = $this->sanitizeCost($row, 'iP');
            //release date
            if(empty($row['date'])) {
                $row['date'] = '';
            } else {
                if(strpos($row['date'], '<span') !== false) {
                    $row['date'] = HtmlParsing::parseTag($row['date'], 'span', true);
                }
                $timeReleased = strtotime($row['date']);
                $row['date'] = date('Y-m-d', $timeReleased);
                $releaseList[] = date('Y-m', $timeReleased);
            }
            //remove trailing dot for difficulty
            if(isset($row['diff.'])) {
                $row['diff'] = $row['diff.'];
                unset($row['diff.']);
            }
        }
        $this->sanitizeList($ipList);
        $this->sanitizeList($rpList);
        $this->sanitizeList($releaseList);

        return array(
            'ipList' => $ipList,
            'rpList' => $rpList,
            'releaseList' => $releaseList,
            'champions' => $rows,
        );

    }

    /**
     * @param array $list
     */
    protected function sanitizeList(array &$list)
    {
        $list = array_filter(array_values(array_unique($list)));
        sort($list);
    }


    /**
     * @param array  $row
     * @param string $costType
     * @return string value
     */
    protected function sanitizeCost(array &$row, string $costType)
    {
        $setCostType = strtolower($costType) . 'Cost';
        if(empty($row[$costType])) $row[$costType] = '';
        //convert to expected field
        $row[$setCostType] = $row[$costType];
        unset($row[$costType]);
        if(strpos($row[$setCostType], '<span') !== false) {
            $row[$setCostType] = HtmlParsing::parseTag($row[$setCostType], 'span', true);
        }
        $row[$setCostType] = trim($row[$setCostType]);
        return $row[$setCostType];

    }


    /**
     * @return mixed|null
     */
    public function getIpList()
    {
        return $this->getContentPart('ipList');
    }

    /**
     * @return mixed|null
     */
    public function getRpList()
    {
        return $this->getContentPart('rpList');
    }

    /**
     * @return mixed|null
     */
    public function getReleaseList()
    {
        return $this->getContentPart('releaseList');
    }

    /**
     * @return mixed|null
     */
    public function getChampions()
    {
        return $this->getContentPart('champions');
    }


}