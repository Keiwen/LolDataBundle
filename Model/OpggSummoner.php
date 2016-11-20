<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\DtoParent;

class OpggSummoner extends DtoParent
{


    /**
     *
     * @return int
     */
    public function getRanking()
    {
        return $this->get('ranking', 0);
    }


    /**
     * @param int $ranking
     * @return static
     */
    public function setRanking(int $ranking)
    {
        return $this->set('ranking', $ranking);
    }

    /**
     *
     * @return float
     */
    public function getPercentFromTop()
    {
        return $this->get('percentFromTop', 0);
    }


    /**
     * @param float $percentFromTop
     * @return static
     */
    public function setPercentFromTop(float $percentFromTop)
    {
        return $this->set('percentFromTop', $percentFromTop);
    }



}