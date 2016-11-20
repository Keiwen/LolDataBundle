<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\DtoParent;

class LolkingChampion extends DtoParent
{



    /**
     * Champion name.
     * @return string
     */
    public function getChampion()
    {
        return $this->get('champion', '');
    }


    /**
     * @param string $champion
     * @return static
     */
    public function setChampion(string $champion)
    {
        return $this->set('champion', $champion);
    }

    /**
     * Release date.
     * @return string
     */
    public function getReleased()
    {
        return $this->get('released', '');
    }


    /**
     * @param string $released
     * @return static
     */
    public function setReleased(string $released)
    {
        return $this->set('released', $released);
    }

    /**
     * Current position in meta.
     * @return string
     */
    public function getMeta()
    {
        return $this->get('meta', '');
    }


    /**
     * @param string $meta
     * @return static
     */
    public function setMeta(string $meta)
    {
        return $this->set('meta', $meta);
    }

    /**
     *
     * @return float
     */
    public function getPopularity()
    {
        return $this->get('popularity', 0);
    }


    /**
     * @param float $popularity
     * @return static
     */
    public function setPopularity(float $popularity)
    {
        return $this->set('popularity', $popularity);
    }

    /**
     *
     * @return float
     */
    public function getPopularityInRange()
    {
        return $this->get('popularityInRange', 0);
    }


    /**
     * @param float $popularityInRange
     * @return static
     */
    public function setPopularityInRange(float $popularityInRange)
    {
        return $this->set('popularityInRange', $popularityInRange);
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
     * Influence point cost.
     * @return int
     */
    public function getIpCost()
    {
        return $this->get('ipCost', 0);
    }


    /**
     * @param int $ipCost
     * @return static
     */
    public function setIpCost(int $ipCost)
    {
        return $this->set('ipCost', $ipCost);
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


}