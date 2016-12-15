<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\Champion\ChampionDto;
use Keiwen\RiotApi\Dto\Champion\ChampionInfoDto;

class CombinedChampion extends ChampionDto
{



    protected static function includedJsonObjects()
    {
        $included = parent::includedJsonObjects();
        $included['lol'] = LolChampion::class;
        $included['regional'] = ChampionInfoDto::class;
        $included['wikia'] = WikiaChampion::class;
        $included['lolking'] = LolkingChampion::class;
        $included['championgg'] = ChampionggChampion::class;
        return $included;
    }


    /**
     *
     * @return ChampionInfoDto
     */
    public function getRegional()
    {
        return $this->get('regional', new ChampionInfoDto());
    }


    /**
     * @param ChampionInfoDto $regional
     * @return static
     */
    public function setRegional(ChampionInfoDto $regional)
    {
        return $this->set('regional', $regional);
    }

    /**
     *
     * @return WikiaChampion
     */
    public function getWikia()
    {
        return $this->get('wikia', new WikiaChampion());
    }


    /**
     * @param WikiaChampion $wikia
     * @return static
     */
    public function setWikia(WikiaChampion $wikia)
    {
        return $this->set('wikia', $wikia);
    }


    /**
     *
     * @return LolChampion
     */
    public function getLol()
    {
        return $this->get('lol', new LolChampion());
    }


    /**
     * @param LolChampion $lol
     * @return static
     */
    public function setLol(LolChampion $lol)
    {
        return $this->set('lol', $lol);
    }


    /**
     *
     * @return LolkingChampion
     */
    public function getLolking()
    {
        return $this->get('lolking', new LolkingChampion());
    }


    /**
     * @param LolkingChampion $lolking
     * @return static
     */
    public function setLolking(LolkingChampion $lolking)
    {
        return $this->set('lolking', $lolking);
    }

    /**
     *
     * @return ChampionggChampion
     */
    public function getChampiongg()
    {
        return $this->get('championgg', new ChampionggChampion());
    }


    /**
     * @param ChampionggChampion $championgg
     * @return static
     */
    public function setChampiongg(ChampionggChampion $championgg)
    {
        return $this->set('championgg', $championgg);
    }



}
