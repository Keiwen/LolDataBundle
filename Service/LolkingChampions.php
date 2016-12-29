<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\LolDataBundle\DependencyInjection\KeiwenLolDataExtension;
use Keiwen\LolDataBundle\Exception\ExternalDataInvalidContentException;
use Keiwen\Utils\Parsing\HtmlParsing;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LolkingChampions extends AbstractExternalDataService
{

    protected $url = 'http://www.lolking.net/champions';

    public function __construct(ContainerInterface $container, $cache, $defaultCacheLifetime, $cacheKeyPrefix)
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
        $this->url = $this->container->getParameter(KeiwenLolDataExtension::LOLKING_URL_CHAMPION);
    }
    
    
    public function getUrl()
    {
        return $this->url;
    }


    protected function parseRawContent(string $raw)
    {

        $tableChampElmt = HtmlParsing::parseHtmlElmt($raw, 'clientsort champion-list', true, 1);
        if(empty($tableChampElmt)) throw new ExternalDataInvalidContentException($this->getName(), 'champion table not found');
        $rows = HtmlParsing::parseTagList($tableChampElmt, 'tr', true);
        $rowTitle = array_shift($rows);
        $rowTitle = HtmlParsing::parseTagList($rowTitle, 'th', true);
        $ipList = array();
        $rpList = array();
        $metaList = array();
        $releaseList = array();
        foreach($rowTitle as &$title) {
            $title = str_replace('RP', 'rp', $title);
            $title = str_replace('IP', 'ip', $title);
            $title = str_replace(' ', '', lcfirst($title));
        }
        foreach($rows as &$row) {
            $row = HtmlParsing::parseTagList($row, 'td', true);
            $row = array_combine($rowTitle, $row);

            //champion name
            if(empty($row['champion'])) {
                $row['champion'] = '';
            } else {
                $row['champion'] = HtmlParsing::parseTag($row['champion'], 'a', true, 2);
                $row['champion'] = str_replace('&#039;', "'", $row['champion']);
            }

            //cost RP/IP
            if(empty($row['rpCost'])) $row['rpCost'] = '';
            $row['rpCost'] = strip_tags($row['rpCost']);
            $rpList[] = $row['rpCost'];
            if(empty($row['ipCost'])) $row['ipCost'] = '';
            $row['ipCost'] = strip_tags($row['ipCost']);
            $ipList[] = $row['ipCost'];

            //release date
            if(empty($row['released'])) {
                $row['released'] = '';
            } else {
                $timeReleased = strtotime($row['released']);
                $row['released'] = date('Y-m-d', $timeReleased);
                $releaseList[] = date('Y-m', $timeReleased);
            }

            //use stats
            $this->completeDataInRange($row, 'popularity');
            $this->completeDataInRange($row, 'winRate');
            $this->completeDataInRange($row, 'banRate');

            //meta
            if(empty($row['meta'])) $row['meta'] = '';
            $metaList[] = $row['meta'];
        }
        $this->sanitizeList($ipList);
        $this->sanitizeList($rpList);
        $this->sanitizeList($metaList);
        $this->sanitizeList($releaseList);

        return array(
            'ipList' => $ipList,
            'rpList' => $rpList,
            'metaList' => $metaList,
            'releaseList' => $releaseList,
            'popularityRange' => $this->getUseStatRange('popularity'),
            'winRateRange' => $this->getUseStatRange('winRate'),
            'banRateRange' => $this->getUseStatRange('banRate'),
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
     * @param string $field
     */
    protected function completeDataInRange(array &$row, string $field)
    {
        $range = $this->getUseStatRange($field);
        //initiate value if not set
        if(!isset($row[$field])) $row[$field] = 0;
        //remove percent
        $row[$field] = trim(trim($row[$field], '%'));
        //normalize in range
        $row[$field . 'InRange'] = ($row[$field] - $range['min']) / ($range['max'] - $range['min']) * 100;
        //keep percent
        if($row[$field . 'InRange'] < 0) $row[$field . 'InRange'] = 0;
        if($row[$field . 'InRange'] > 100) $row[$field . 'InRange'] = 100;
    }


    /**
     * @param string $part
     * @return array|mixed
     */
    protected function getUseStatRange(string $part = '')
    {
        $configuration = $this->container->getParameter(KeiwenLolDataExtension::LOLKING_USESTATRANGE);
        if(empty($part)) return $configuration;
        return empty($configuration[$part]) ? array() : $configuration[$part];
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
    public function getMetaList()
    {
        return $this->getContentPart('metaList');
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
    public function getPopularityRange()
    {
        return $this->getContentPart('popularityRange');
    }

    /**
     * @return mixed|null
     */
    public function getWinRateRange()
    {
        return $this->getContentPart('winRateRange');
    }

    /**
     * @return mixed|null
     */
    public function getBanRateRange()
    {
        return $this->getContentPart('banRateRange');
    }

    /**
     * @return mixed|null
     */
    public function getChampions()
    {
        return $this->getContentPart('champions');
    }


}