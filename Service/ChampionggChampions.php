<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\LolDataBundle\DependencyInjection\KeiwenLolDataExtension;
use Keiwen\Utils\Math\Divisibility;
use Keiwen\Utils\Mutator\ArrayMutator;
use Keiwen\Utils\Parsing\HtmlParsing;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChampionggChampions extends AbstractExternalDataService
{

    protected $url = 'http://champion.gg/statistics/';

    public function __construct(ContainerInterface $container, $cache, $defaultCacheLifetime, $cacheKeyPrefix)
    {
        parent::__construct($container, $cache, $defaultCacheLifetime, $cacheKeyPrefix);
        $this->url = $this->container->getParameter(KeiwenLolDataExtension::CHAMPIONGG_URL_CHAMPION);
    }

    public function getUrl()
    {
        return $this->url;
    }


    protected function parseRawContent(string $raw)
    {
        $tagIteration = 1;
        $script = '';
        //search for script defining champ data
        while(true) {
            $script = HtmlParsing::parseTag($raw, 'script', false, $tagIteration);
            //if empty, last tags was reached
            if(empty($script)) break;
            if(strpos($script, 'matchupData.stats') !== false) {
                //found script
                $script = str_replace('<script>', '', $script);
                $script = str_replace('</script>', '', $script);
                $script = str_replace('matchupData.stats', '', $script);
                $script = str_replace('=', '', $script);
                $script = trim($script);
                $script = trim($script, ';');
                break;
            }
            $tagIteration++;
        }
        $data = json_decode($script, true);
        if(empty($data)) return array();

        $champions = array();
        $roleList = array();
        $roleRanking = array();
        $mainRoles = array();
        foreach($data as $row) {
            if(empty($row['key'])) continue;
            if(empty($row['role'])) continue;
            //rename some fields
            $row['general']['playRate'] = $row['general']['playPercent'];
            unset($row['general']['playPercent']);
            $row['general']['winRate'] = $row['general']['winPercent'];
            unset($row['general']['winPercent']);
            //force overall position and play rate
            if(empty($row['general']['overallPosition'])) $row['general']['overallPosition'] = 9999;
            if(empty($row['general']['playRate'])) $row['general']['playRate'] = 0;
            //collect roles
            if(!in_array($row['role'], $roleList)) $roleList[] = $row['role'];
            //store in role by position
            $roleRanking[$row['role']][$row['general']['overallPosition']] = $row['key'];
            //use stats
            $this->completeDataInRange($row, 'playRate');
            $this->completeDataInRange($row, 'winRate');
            $this->completeDataInRange($row, 'banRate');
            //store by champ
            $row['general']['role'] = $row['role'];
            //store main role playRate
            if(empty($champions[$row['key']]['mainRole']) || $mainRoles[$row['key']] < $row['general']['playRate']) {
                $champions[$row['key']]['mainRole'] = $row['role'];
                $mainRoles[$row['key']] = $row['general']['playRate'];
            }
            $champions[$row['key']]['champion'] = $row['title'];
            $champions[$row['key']]['roles'][$row['role']] = $row['general'];
        }
        sort($roleList);
        ksort($roleRanking);
        foreach($roleRanking as $role => &$roleRankingByRole) {
            ksort($roleRankingByRole);
            $totalInRole = count($roleRankingByRole);
            foreach($roleRankingByRole as $rank => $champ) {
                $champions[$champ]['roles'][$role]['roleTier'] = $this->getTier($rank, $totalInRole);
            }
        }

        return array(
            'champions' => $champions,
            'roleList' => $roleList,
            'roleRanking' => $roleRanking,
            'playRateRange' => $this->getUseStatRange('playRate'),
            'winRateRange' => $this->getUseStatRange('winRate'),
            'banRateRange' => $this->getUseStatRange('banRate'),
        );
    }


    /**
     * @param string $part
     * @return array|mixed
     */
    protected function getUseStatRange(string $part = '')
    {
        $configuration = $this->container->getParameter(KeiwenLolDataExtension::CHAMPIONGG_USESTATRANGE);
        if(empty($part)) return $configuration;
        return empty($configuration[$part]) ? array() : $configuration[$part];
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
     * @param int $rank
     * @param int $total
     * @return int
     */
    protected function getTier(int $rank, int $total) {
        return Divisibility::getThird($rank, $total, false);
    }

}