<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\DtoParent;

class LolChampion extends DtoParent
{



    /**
     * Champion key.
     * @return string
     */
    public function getChampionKey()
    {
        return $this->get('champKey', '');
    }


    /**
     * @param string $championKey
     * @return static
     */
    public function setChampionKey(string $championKey)
    {
        return $this->set('champKey', $championKey);
    }


    /**
     * @return string
     */
    public function getAffiliationId()
    {
        return $this->get('affiliationId', '');
    }


    /**
     * @param string $affiliationId
     * @return static
     */
    public function setAffiliationId(string $affiliationId)
    {
        return $this->set('affiliationId', $affiliationId);
    }


    /**
     * @return array
     */
    public function getFriends()
    {
        return $this->get('friends', array());
    }


    /**
     * @param array $friends
     * @return static
     */
    public function setFriends(array $friends)
    {
        return $this->set('friends', $friends);
    }

    /**
     * @return array
     */
    public function getRivals()
    {
        return $this->get('rivals', array());
    }


    /**
     * @param array $rivals
     * @return static
     */
    public function setRivals(array $rivals)
    {
        return $this->set('rivals', $rivals);
    }

    /**
     * @return array
     */
    public function getSpellVideosHtml()
    {
        return $this->get('spellVideosHtml', array());
    }


    /**
     * @param array $spellVideosHtml
     * @return static
     */
    public function setSpellVideosHtml(array $spellVideosHtml)
    {
        return $this->set('spellVideosHtml', $spellVideosHtml);
    }


    /**
     * @return string
     */
    public function getPassiveVideoHtml()
    {
        return $this->get('passiveVideoHtml', '');
    }


    /**
     * @param string $passiveVideoHtml
     * @return static
     */
    public function setPassiveVideoHtml(string $passiveVideoHtml)
    {
        return $this->set('passiveVideoHtml', $passiveVideoHtml);
    }


}
