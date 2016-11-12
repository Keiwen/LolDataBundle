<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\Utils\Curl\SimpleCurl;
use Keiwen\Utils\Curl\SimpleCurlGet;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractExternalDataService
{

    use CacheHandlerTrait;

    protected $queryError = '';
    /** @var ContainerInterface */
    protected $container;
    protected $urlParameters = array();

    /**
     * AbstractExternalDataService constructor.
     *
     * @param null   $cache
     * @param int    $defaultCacheLifetime
     * @param string $cacheKeyPrefix
     */
    public function __construct(ContainerInterface $container, $cache = null, int $defaultCacheLifetime = 0, string $cacheKeyPrefix = '')
    {
        $this->container = $container;
        $this->cache = $cache;
        $this->defaultCacheLifetime = $defaultCacheLifetime;
        $this->cacheKeyPrefix = $cacheKeyPrefix;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setUrlParameters(array $parameters = array())
    {
        $this->urlParameters = $parameters;
        return $this;
    }


    /**
     * @return string
     */
    abstract public function getUrl();

    /**
     * @return string
     */
    protected function getServiceKey()
    {
        return $this->getUrl();
    }


    /**
     * @return mixed|null
     */
    public function getContent()
    {
        $content = $this->readInCache($this->getServiceKey());
        if($content === null) {
            $rawContent = $this->query();
            $content = $this->parseRawContent($rawContent);
            $this->storeInCache($this->getServiceKey(), $content);
        }
        return $content;
    }


    /**
     * @param string $part
     * @return mixed|null
     */
    protected function getContentPart(string $part)
    {
        $content = $this->getContent();
        return empty($content[$part]) ? null : $content[$part];
    }

    /**
     * @return string
     */
    protected function query()
    {
        $curl = new SimpleCurlGet($this->getUrl());
        $this->configureCurl($curl);
        $rawContent = $curl->query();
        $this->queryError = '';
        if($curl->hasError()) {
            if($curl->hasCurlError()) {
                $this->queryError = 'Curl error ' . $curl->getCurlError();
            } else {
                $this->queryError = 'Http code ' . $curl->getHttpCode();
            }
        }
        return $rawContent;
    }

    /**
     * @param SimpleCurl $curl
     */
    protected function configureCurl(SimpleCurl $curl)
    {
    }

    /**
     * @return bool
     */
    public function hasQueryError()
    {
        return !empty($this->queryError);
    }

    /**
     * @return string
     */
    public function getQueryError()
    {
        return $this->queryError;
    }

    /**
     * @param string $raw
     * @return mixed
     */
    abstract protected function parseRawContent(string $raw);


    /**
     * @return Response
     */
    public function testQuery()
    {
        $raw = $this->query();
        return new Response($raw);
    }

}