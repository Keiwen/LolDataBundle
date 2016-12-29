<?php

namespace Keiwen\LolDataBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestResultExternalController extends Controller
{

    /**
     * @Route("/_index", name="kld_indexResult")
     */
    public function indexAction()
    {
        $rClass = new \ReflectionClass($this);
        $rMethods = $rClass->getMethods();
        $routes = array();
        foreach($rMethods as $rMethod) {
            $methodName = $rMethod->getName();
            if(strpos($methodName, 'Action') !== false) {
                $routeName = 'kld_' . str_replace('Action', 'Result', $methodName);
                $routes[] = '<a href="'.$this->generateUrl($routeName).'">'.str_replace('Action', '', $methodName).'</a>';
            }
        }
        return new Response(implode('<br/>', $routes));
    }

    /**
     * @Route("/wikia/champions", name="kld_wikiaChampionsResult")
     */
    public function wikiaChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.wikiachampions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }

    /**
     * @Route("/lolking/champions", name="kld_lolkingChampionsResult")
     */
    public function lolkingChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.lolkingchampions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }

    /**
     * @Route("/opgg/profile", name="kld_opggProfileResult")
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
     * @Route("/championgg/champions", name="kld_championggChampionsResult")
     */
    public function championggChampionsAction()
    {
        $service = $this->get('keiwen_loldata.external.championggchampions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }



    /**
     * @Route("/riot/apiVersions", name="kld_riotApiVersionsResult")
     */
    public function riotApiVersionsAction()
    {
        $service = $this->get('keiwen_loldata.external.riotapiversions');
        $content = $service->getContent();
        return new JsonResponse($content);
    }

    /**
     * @Route("/lol/champion", name="kld_lolChampionResult")
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