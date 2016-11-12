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



}