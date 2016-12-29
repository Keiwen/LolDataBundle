<?php

namespace Keiwen\LolDataBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestExternalController extends Controller
{

    /**
     * @Route("/_index", name="kld_indexTest")
     */
    public function indexAction()
    {
        $rClass = new \ReflectionClass($this);
        $rMethods = $rClass->getMethods();
        $routes = array();
        foreach($rMethods as $rMethod) {
            $methodName = $rMethod->getName();
            if(strpos($methodName, 'Action') !== false) {
                $routeName = 'kld_' . str_replace('Action', 'Test', $methodName);
                $routes[] = '<a href="'.$this->generateUrl($routeName).'">'.str_replace('Action', '', $methodName).'</a>';
            }
        }
        return new Response(implode('<br/>', $routes));
    }

    /**
     * @Route("/wikia/champions", name="kld_wikiaChampionsTest")
     */
    public function wikiaChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.wikiachampions');
        dump($service->getUrl());
        return $service->testQuery();
    }

    /**
     * @Route("/lolking/champions", name="kld_lolkingChampionsTest")
     */
    public function lolkingChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.lolkingchampions');
        dump($service->getUrl());
        return $service->testQuery();
    }

    /**
     * @Route("/opgg/profile", name="kld_opggProfileTest")
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
     * @Route("/championgg/champions", name="kld_championggChampionsTest")
     */
    public function championggChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.championggchampions');
        dump($service->getUrl());
        return $service->testQuery();
    }

    /**
     * @Route("/riot/apiVersions", name="kld_riotApiVersionsTest")
     */
    public function riotApiVersionsAction()
    {
        $service = $this->get('keiwen_loldata.external.riotapiversions');
        dump($service->getUrl());
        return $service->testQuery();
    }


    /**
     * @Route("/lol/champion", name="kld_lolChampionTest")
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