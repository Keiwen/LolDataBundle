<?php

namespace Keiwen\LolDataBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TestResultExternalController extends Controller
{


    /**
     * @Route("/wikia/champions", name="wikiaChampionsResult")
     */
    public function wikiaChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.wikiachampions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }

    /**
     * @Route("/lolking/champions", name="lolkingChampionsResult")
     */
    public function lolkingChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.lolkingchampions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }

    /**
     * @Route("/opgg/profile", name="opggProfileResult")
     */
    public function opggProfileAction(Request $request)
    {
        $server = $request->get('server');
        if($server == null) $server = 'na';
        $summonerName = $request->get('summonerName');
        if($summonerName == null) $summonerName = 'tryndamere';
        $service = $this->get('keiwen_loldata.external.opggprofile');
        $service->setServer($server)->setSummonerName($summonerName);
        $content = $service->getContent();
        return new JsonResponse($content);
    }

    /**
     * @Route("/championgg/champions", name="championggChampionsResult")
     */
    public function championggChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.championggchampions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }



    /**
     * @Route("/riot/apiVersions", name="riotApiVersionsResult")
     */
    public function riotApiAction()
    {
        $service = $this->get('keiwen_loldata.external.riotapiversions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }

    /**
     * @Route("/lol/champion", name="lolChampionResult")
     */
    public function lolChampionAction(Request $request)
    {
        $champion = $request->get('champion');
        if($champion == null) $champion = 'Teemo';
        $service = $this->get('keiwen_loldata.external.lolchampion');
        $service->setChampion($champion);
        $content = $service->getContent();
        return new JsonResponse($content);
    }


}
