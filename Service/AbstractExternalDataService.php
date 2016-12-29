<?php

namespace Keiwen\LolDataBundle\Service;


use Keiwen\LolDataBundle\Exception\ExternalDataInvalidContentException;
use Keiwen\Utils\Curl\SimpleCurl;
use Keiwen\Utils\Curl\SimpleCurlGet;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractExternalDataService
{

    use CacheHandlerTrait;

    protected $queryError = '';
    /** @var ContainerInterface */
    protected $container;
    /** @var LoggerInterface */
    protected $logger;
    protected $urlParameters = array();

    /**
     * AbstractExternalDataService constructor.
     *
     * @param null   $cache
     * @param int    $defaultCacheLifetime
     * @param string $cacheKeyPrefix
     */
    public function __construct(ContainerInterface $container, $cache = null, int $defaultCacheLifetime = 0, string $cacheKeyPrefix = '', LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->cache = $cache;
        $this->defaultCacheLifetime = $defaultCacheLifetime;
        $this->cacheKeyPrefix = $cacheKeyPrefix;
        $this->logger = $logger;
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
    public function getName() {
        $namespace = static::class;
        $namespace = explode('\\', $namespace);
        return end($namespace);
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
     * @return array|null
     */
    public function getContent()
    {
        $content = $this->readInCache($this->getServiceKey());
        if($content === null || true) {
            $rawContent = $this->query();
            try {
                $content = $this->parseRawContent($rawContent);
            } catch(ExternalDataInvalidContentException $e) {
                if(!empty($this->logger)) $this->logger->critical($e->getMessage());
                $content = array();
            }
            $this->storeInCache($this->getServiceKey(), $content);
        } else {
            if(!empty($this->logger)) $this->logger->debug(sprintf('Service key %s read in cache', $this->getServiceKey()));
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
        if(!empty($this->logger)) $this->logger->debug(sprintf('Request to url %s', $this->getUrl()));
        $curl = new SimpleCurlGet($this->getUrl());
        $this->configureCurl($curl);
        $rawContent = $curl->query();
        $this->queryError = '';
        if($curl->hasError()) {
            $rawContent = '';
            if($curl->hasCurlError()) {
                $this->queryError = 'Curl error ' . $curl->getCurlError();
            } else {
                $this->queryError = 'Http code ' . $curl->getHttpCode();
            }
            if(!empty($this->logger)) $this->logger->error($this->queryError);
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
     * @return array
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