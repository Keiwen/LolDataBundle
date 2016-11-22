<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\Summoner\SummonerDto;

class CombinedSummoner extends SummonerDto
{



    protected static function includedJsonObjects()
    {
        $included = parent::includedJsonObjects();
        $included['opgg'] = OpggSummoner::class;
        return $included;
    }


    /**
     *
     * @return OpggSummoner
     */
    public function getOpgg()
    {
        return $this->get('opgg', new OpggSummoner());
    }


    /**
     * @param OpggSummoner $opgg
     * @return static
     */
    public function setOpgg(OpggSummoner $opgg)
    {
        return $this->set('opgg', $opgg);
    }

    
    /**
     *
     * @return string
     */
    public function getServer()
    {
        return $this->get('server', '');
    }


    /**
     * @param string $server
     * @return static
     */
    public function setServer(string $server)
    {
        return $this->set('server', $server);
    }



}
