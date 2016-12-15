<?php

namespace Keiwen\LolDataBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestExternalController extends Controller
{


    /**
     * @Route("/wikia/champions", name="wikiaChampionsTest")
     */
    public function wikiaChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.wikiachampions');
        dump($service->getUrl());
        return $service->testQuery();
    }

    /**
     * @Route("/lolking/champions", name="lolkingChampionsTest")
     */
    public function lolkingChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.lolkingchampions');
        dump($service->getUrl());
        return $service->testQuery();
    }

    /**
     * @Route("/opgg/profile", name="opggProfileTest")
     */
    public function opggProfileAction(Request $request)
    {
        $server = $request->get('server');
        if($server == null) $server = 'na';
        $summonerName = $request->get('summonerName');
        if($summonerName == null) $summonerName = 'tryndamere';
        $service = $this->get('keiwen_loldata.external.opggprofile');
        $service->setServer($server)->setSummonerName($summonerName);
        dump($service->getUrl());
        return $service->testQuery();
    }

    /**
     * @Route("/championgg/champions", name="championggChampionsTest")
     */
    public function championggChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.championggchampions');
        dump($service->getUrl());
        return $service->testQuery();
    }

    /**
     * @Route("/riot/apiVersions", name="riotApiVersionsTest")
     */
    public function riotApiAction()
    {
        $service = $this->get('keiwen_loldata.external.riotapiversions');
        dump($service->getUrl());
        return $service->testQuery();
    }


    /**
     * @Route("/lol/champion", name="lolChampionTest")
     */
    public function lolChampionAction(Request $request)
    {
        $champion = $request->get('champion');
        if($champion == null) $champion = 'Teemo';
        $service = $this->get('keiwen_loldata.external.lolchampion');
        $service->setChampion($champion);
        dump($service->getUrl());
        return $service->testQuery();
    }


}
