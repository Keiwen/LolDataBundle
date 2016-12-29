<?php

namespace Keiwen\LolDataBundle\Model;


use Keiwen\RiotApi\Dto\DtoParent;

class ChampionggChampion extends DtoParent
{



    protected static function includedJsonObjects()
    {
        $included = parent::includedJsonObjects();
        $included['roles'] = ChampionggChampionRole::class;
        return $included;
    }


    /**
     * @return string
     */
    public function getMainRole()
    {
        return $this->get('mainRole', '');
    }


    /**
     * @param string $mainRole
     * @return static
     */
    public function setMainRole(string $mainRole)
    {
        return $this->set('mainRole', $mainRole);
    }

    /**
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
     *
     * @return ChampionggChampionRole[]
     */
    public function getRoles()
    {
        return $this->get('roles', array());
    }


    /**
     * @param ChampionggChampion[] $roles
     * @return static
     */
    public function setRoles($roles)
    {
        if(empty($roles)) $roles = array();
        return $this->set('roles', $roles);
    }




}