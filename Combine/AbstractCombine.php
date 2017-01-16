<?php

namespace Keiwen\LolDataBundle\Combine;


use Keiwen\RiotApi\Api\RiotApi;
use Keiwen\RiotApi\Dto\DtoParent;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractCombine
{
    use CacheHandlerTrait {
        getCacheFullKey as getCacheFullKeyNoConf;
    }

    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container, $cache = null, int $defaultCacheLifetime = 0, string $cacheKeyPrefix = '')
    {
        $this->container = $container;
        $this->cache = $cache;
        $this->defaultCacheLifetime = $defaultCacheLifetime;
        $this->cacheKeyPrefix = $cacheKeyPrefix;

    }

    /**
     * unique identifier representing current combination
     * @return string
     */
    abstract protected function getCombineId();


    /**
     * @param string $partKey
     * @return string
     */
    public function getCacheFullKey(string $partKey)
    {
        $key = $this->getCacheFullKeyNoConf($partKey);
        return $key . $this->getCombineId();
    }

    /**
     * @param array|DtoParent|\stdClass|string $data
     * @return array
     */
    protected function extractDataArray($data) {
        switch(RiotApi::detectOutputFormat($data)) {
            case RiotApi::FORMAT_JSON:
                $array = json_decode($data, true);
                break;
            case RiotApi::FORMAT_STDCLASS:
                $array = json_decode(json_encode($data), true);
                break;
            case RiotApi::FORMAT_DTO:
                if(is_array($data)) {
                    $array = array();
                    foreach($data as $add) {
                        $array[] = $add->exportData();
                    }
                } else {
                    $array = $data->exportData();
                }
                break;
            case RiotApi::FORMAT_ARRAY:
            default:
                $array = $data;
        }
        return $array;
    }


    /**
     * @see combineContentList() used when ouput is array of DTO
     * @param array|DtoParent|\stdClass|string $parentContent
     * @param array|DtoParent|\stdClass|string $childContent
     * @param string                           $fieldMap if array
     * @param string                           $insertInNewField empty to merge data
     * @param string                           $dtoClassOutput if dto expected, fully qualified class name here
     * @return array|DtoParent|\stdClass|string
     */
    public function combineContent($original, $additional, string $fieldMap = '', string $insertInNewField = '', string $dtoClassOutput = '')
    {

        $originalData = $this->extractDataArray($original);
        $additionalData = $this->extractDataArray($additional);

        if($insertInNewField) {
            //insert in new field
            $originalData[$insertInNewField] = $additionalData;
        } else {
            //combine
            $originalData = array_merge($originalData, $additionalData);
        }

        switch(RiotApi::detectOutputFormat($original)) {
            case RiotApi::FORMAT_DTO:
                if(is_array($original)) {
                    //redirect array of dto
                    return $this->combineContentList($original, $additional, $fieldMap, $insertInNewField, $dtoClassOutput);
                } else {
                    //try to generate from provided class
                    if(!empty($dtoClassOutput) && class_exists($dtoClassOutput) && is_subclass_of($dtoClassOutput, DtoParent::class)) {
                        try {
                            return new $dtoClassOutput($originalData);
                        } catch (\Exception $e) {

                        }
                    }
                    $parentClass = get_class($original);
                    //return same class, just complete object
                    return new $parentClass($originalData);
                }
            case RiotApi::FORMAT_STDCLASS:
                return json_decode(json_decode($originalData));
            case RiotApi::FORMAT_ARRAY:
                return $originalData;
            case RiotApi::FORMAT_JSON:
            default:
                return json_encode($originalData);
        }
    }


    /**
     * @param array|string|\stdClass|DtoParent[] $original
     * @param array|string|\stdClass|DtoParent[] $additional
     * @param string                             $fieldMap
     * @param string                             $insertInNewField
     * @param string                             $dtoClassOutput
     * @return array|string|\stdClass|DtoParent[]
     */
    public function combineContentList($original, $additional, string $fieldMap = '', string $insertInNewField = '', string $dtoClassOutput = '')
    {
        //get array
        $originalData = is_array($original) ? $original : $this->extractDataArray($original);
        $additionalData = is_array($additional) ? $additional : $this->extractDataArray($additional);
        if(empty($additionalData)) return $original;
        foreach($additionalData as $index => $add) {
            $add = $this->extractDataArray($add);
            if($fieldMap) {
                //associate using a field of additional data
                //check if field filled and found in original
                if(empty($add[$fieldMap]) || empty($originalData[$add[$fieldMap]])) continue;
                $key = $add[$fieldMap];
            } else {
                //associate using a key of additional data
                //check if found in original
                if(empty($originalData[$index])) continue;
                $key = $index;
            }
            $originalData[$key] = $this->combineContent($originalData[$key], $add, $fieldMap, $insertInNewField, $dtoClassOutput);
        }

        //ensure output format
        if(!is_array($original)) {
            switch(RiotApi::detectOutputFormat($original)) {
                case RiotApi::FORMAT_STDCLASS:
                    return json_decode(json_decode($originalData));
                case RiotApi::FORMAT_JSON:
                    return json_encode($originalData);
            }
        }
        return $originalData;
    }


    /**
     * @see retrieveOutputFieldList() used when output is array of DTO
     * @param array|DtoParent|\stdClass|string $output
     * @param string                           $fieldName
     * @return mixed|string
     */
    public function retrieveOutputField($output, string $fieldName) {
        if(empty($output)) return $output;
        //extract list of champions in 'champions' field
        switch(RiotApi::detectOutputFormat($output)) {
            case RiotApi::FORMAT_DTO:
                //redirect array of DTO
                if(is_array($output)) return $this->retrieveOutputFieldList($output, $fieldName);
                //try getter
                $getter = 'get' . ucfirst($fieldName);
                if(method_exists($output, $getter)) {
                    $outputField = $output->$getter();
                } else {
                    $outputField = $output->get($fieldName, $output);
                }
                break;
            case RiotApi::FORMAT_STDCLASS:
                //get array
                $output = json_decode(json_encode($output), true);
                $output = $output[$fieldName];
                //rebuild stdclass
                $outputField = json_decode(json_encode($output));
                break;
            case RiotApi::FORMAT_ARRAY:
                $outputField = $output[$fieldName];
                break;
            case RiotApi::FORMAT_JSON:
            default:
                $output = json_decode($output, true);
                $output = $output[$fieldName];
                //rebuild json
                $outputField = json_encode($output);
                break;
        }
        return $outputField;
    }


    /**
     * @param array  $output
     * @param string $fieldName
     * @param bool   $resultAsArray
     * @return array|string|\stdClass
     */
    public function retrieveOutputFieldList($output, string $fieldName, bool $resultAsArray = false) {
        $outputFormat = RiotApi::detectOutputFormat($output);
        switch($outputFormat) {
            //get array
            case RiotApi::FORMAT_JSON:
                $output = json_decode($output, true);
                break;
            case RiotApi::FORMAT_STDCLASS:
                $output = json_decode(json_encode($output), true);
        }
        if(empty($output)) $output = array();
        foreach($output as $key => &$data) {
            $data = $this->retrieveOutputField($data, $fieldName);
        }
        if($resultAsArray) return $output;
        switch($outputFormat) {
            //turn back to format
            case RiotApi::FORMAT_JSON:
                $output = json_encode($output);
                break;
            case RiotApi::FORMAT_STDCLASS:
                $output = json_decode(json_encode($output));
        }
        return $output;
    }

}
