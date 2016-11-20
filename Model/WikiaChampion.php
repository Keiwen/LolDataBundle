<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\DtoParent;

class WikiaChampion extends DtoParent
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
     * Primary role.
     * @return string
     */
    public function getPrimary()
    {
        return $this->get('primary', '');
    }


    /**
     * @param string $primary
     * @return static
     */
    public function setPrimary(string $primary)
    {
        return $this->set('primary', $primary);
    }

    /**
     * Secondary role.
     * @return string
     */
    public function getSecondary()
    {
        return $this->get('secondary', '');
    }


    /**
     * @param string $secondary
     * @return static
     */
    public function setSecondary(string $secondary)
    {
        return $this->set('secondary', $secondary);
    }

    /**
     * Release date.
     * @return string
     */
    public function getDate()
    {
        return $this->get('date', '');
    }


    /**
     * @param string $date
     * @return static
     */
    public function setDate(string $date)
    {
        return $this->set('date', $date);
    }

    /**
     *
     * @return int
     */
    public function getAttack()
    {
        return $this->get('attack', 0);
    }


    /**
     * @param int $attack
     * @return static
     */
    public function setAttack(int $attack)
    {
        return $this->set('attack', $attack);
    }

    /**
     *
     * @return int
     */
    public function getDefense()
    {
        return $this->get('defense', 0);
    }


    /**
     * @param int $defense
     * @return static
     */
    public function setDefense(int $defense)
    {
        return $this->set('defense', $defense);
    }

    /**
     *
     * @return int
     */
    public function getAbility()
    {
        return $this->get('ability', 0);
    }


    /**
     * @param int $ability
     * @return static
     */
    public function setAbility(int $ability)
    {
        return $this->set('ability', $ability);
    }

    /**
     *
     * @return int
     */
    public function getDiff()
    {
        return $this->get('diff', 0);
    }


    /**
     * @param int $diff
     * @return static
     */
    public function setDiff(int $diff)
    {
        return $this->set('diff', $diff);
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