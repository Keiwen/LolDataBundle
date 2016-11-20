<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\DtoParent;

class ChampionggChampionRole extends DtoParent
{



    /**
     * @return string
     */
    public function getRole()
    {
        return $this->get('role', '');
    }


    /**
     * @param string $role
     * @return static
     */
    public function setRole(string $role)
    {
        return $this->set('role', $role);
    }


    /**
     * @return int
     */
    public function getRoleTier()
    {
        return $this->get('roleTier', 0);
    }


    /**
     * @param int $roleTier
     * @return static
     */
    public function setRoleTier(int $roleTier)
    {
        return $this->set('roleTier', $roleTier);
    }

    
    /**
     *
     * @return float
     */
    public function getPlayRate()
    {
        return $this->get('playRate', 0);
    }


    /**
     * @param float $playRate
     * @return static
     */
    public function setPlayRate(float $playRate)
    {
        return $this->set('playRate', $playRate);
    }

    /**
     *
     * @return float
     */
    public function getPlayRateInRange()
    {
        return $this->get('playRateInRange', 0);
    }


    /**
     * @param float $playRateInRange
     * @return static
     */
    public function setPlayRateInRange(float $playRateInRange)
    {
        return $this->set('playRateInRange', $playRateInRange);
    }

    /**
     *
     * @return float
     */
    public function getWinRate()
    {
        return $this->get('winRate', 0);
    }


    /**
     * @param float $winRate
     * @return static
     */
    public function setWinRate(float $winRate)
    {
        return $this->set('winRate', $winRate);
    }

    /**
     *
     * @return float
     */
    public function getWinRateInRange()
    {
        return $this->get('winRateInRange', 0);
    }


    /**
     * @param float $winRateInRange
     * @return static
     */
    public function setWinRateInRange(float $winRateInRange)
    {
        return $this->set('winRateInRange', $winRateInRange);
    }

    /**
     *
     * @return float
     */
    public function getBanRate()
    {
        return $this->get('banRate', 0);
    }


    /**
     * @param float $banRate
     * @return static
     */
    public function setBanRate(float $banRate)
    {
        return $this->set('banRate', $banRate);
    }

    /**
     *
     * @return float
     */
    public function getBanRateInRange()
    {
        return $this->get('banRateInRange', 0);
    }


    /**
     * @param float $banRateInRange
     * @return static
     */
    public function setBanRateInRange(float $banRateInRange)
    {
        return $this->set('banRateInRange', $banRateInRange);
    }


    /**
     * Riot point cost.
     * @return int
     */
    public function getRpCost()
    {
        return $this->get('rpCost', 0);
    }


    /**
     * @param int $rpCost
     * @return static
     */
    public function setRpCost(int $rpCost)
    {
        return $this->set('rpCost', $rpCost);
    }


    /**
     *
     * @return float
     */
    public function getExperience()
    {
        return $this->get('experience', 0);
    }


    /**
     * @param float $experience
     * @return static
     */
    public function setExperience(float $experience)
    {
        return $this->set('experience', $experience);
    }

    /**
     *
     * @return float
     */
    public function getKills()
    {
        return $this->get('kills', 0);
    }


    /**
     * @param float $kills
     * @return static
     */
    public function setKills(float $kills)
    {
        return $this->set('kills', $kills);
    }

    /**
     *
     * @return float
     */
    public function getDeaths()
    {
        return $this->get('deaths', 0);
    }


    /**
     * @param float $deaths
     * @return static
     */
    public function setDeaths(float $deaths)
    {
        return $this->set('deaths', $deaths);
    }

    /**
     *
     * @return float
     */
    public function getAssists()
    {
        return $this->get('assists', 0);
    }


    /**
     * @param float $assists
     * @return static
     */
    public function setAssists(float $assists)
    {
        return $this->set('assists', $assists);
    }

    /**
     *
     * @return float
     */
    public function getLargestKillingSpree()
    {
        return $this->get('largestKillingSpree', 0);
    }


    /**
     * @param float $largestKillingSpree
     * @return static
     */
    public function setLargestKillingSpree(float $largestKillingSpree)
    {
        return $this->set('largestKillingSpree', $largestKillingSpree);
    }

    /**
     *
     * @return float
     */
    public function getMinionsKilled()
    {
        return $this->get('minionsKilled', 0);
    }


    /**
     * @param float $minionsKilled
     * @return static
     */
    public function setMinionsKilled(float $minionsKilled)
    {
        return $this->set('minionsKilled', $minionsKilled);
    }

    /**
     *
     * @return float
     */
    public function getNeutralMinionsKilledTeamJungle()
    {
        return $this->get('neutralMinionsKilledTeamJungle', 0);
    }


    /**
     * @param float $neutralMinionsKilledTeamJungle
     * @return static
     */
    public function setNeutralMinionsKilledTeamJungle(float $neutralMinionsKilledTeamJungle)
    {
        return $this->set('neutralMinionsKilledTeamJungle', $neutralMinionsKilledTeamJungle);
    }

    /**
     *
     * @return float
     */
    public function getNeutralMinionsKilledEnemyJungle()
    {
        return $this->get('neutralMinionsKilledEnemyJungle', 0);
    }


    /**
     * @param float $neutralMinionsKilledEnemyJungle
     * @return static
     */
    public function setNeutralMinionsKilledEnemyJungle(float $neutralMinionsKilledEnemyJungle)
    {
        return $this->set('neutralMinionsKilledEnemyJungle', $neutralMinionsKilledEnemyJungle);
    }


    /**
     * @return int
     */
    public function getOverallPosition()
    {
        return $this->get('overallPosition', 0);
    }


    /**
     * @param int $overallPosition
     * @return static
     */
    public function setOverallPosition(int $overallPosition)
    {
        return $this->set('overallPosition', $overallPosition);
    }


    /**
     *
     * @return float
     */
    public function getOverallPositionChange()
    {
        return $this->get('overallPositionChange', 0);
    }


    /**
     * @param float $overallPositionChange
     * @return static
     */
    public function setOverallPositionChange(float $overallPositionChange)
    {
        return $this->set('overallPositionChange', $overallPositionChange);
    }


    /**
     * @return int
     */
    public function getTotalDamageDealtToChampions()
    {
        return $this->get('totalDamageDealtToChampions', 0);
    }


    /**
     * @param int $totalDamageDealtToChampions
     * @return static
     */
    public function setTotalDamageDealtToChampions(int $totalDamageDealtToChampions)
    {
        return $this->set('totalDamageDealtToChampions', $totalDamageDealtToChampions);
    }


    /**
     * @return int
     */
    public function getTotalDamageTaken()
    {
        return $this->get('totalDamageTaken', 0);
    }


    /**
     * @param int $totalDamageTaken
     * @return static
     */
    public function setTotalDamageTaken(int $totalDamageTaken)
    {
        return $this->set('totalDamageTaken', $totalDamageTaken);
    }


    /**
     * @return int
     */
    public function getTotalHeal()
    {
        return $this->get('totalHeal', 0);
    }


    /**
     * @param int $totalHeal
     * @return static
     */
    public function setTotalHeal(int $totalHeal)
    {
        return $this->set('totalHeal', $totalHeal);
    }


    /**
     * @return int
     */
    public function getGoldEarned()
    {
        return $this->get('goldEarned', 0);
    }


    /**
     * @param int $goldEarned
     * @return static
     */
    public function setGoldEarned(int $goldEarned)
    {
        return $this->set('goldEarned', $goldEarned);
    }



}