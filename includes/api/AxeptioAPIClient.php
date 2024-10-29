<?php

class AxeptioAPIClient
{
    /**
     * Get all consents by type
     * @param $type
     * @return mixed
     */
    public static function getConfigurations($type)
    {
        $configurations = json_decode(AxeptioAPIClient::getAllCollections());

        return $configurations->$type;
    }

    /**
     * Get all collection for a client ID
     * @param $clientId
     * @param bool $decode
     * @param string $url
     * @return array|bool|mixed|null|object|string
     */
    public static function getAllCollections($clientId, $decode = false, $url = 'https://api.axept.io/v1')
    {
        $configurations = file_get_contents(
            $url.'/app/configurations/'.$clientId
        );

        if ($decode) {
            return json_decode($configurations);
        }

        return $configurations;
    }

    /**
     * Sort consents by lang
     * @param $configurations
     * @return array
     */
    public static function getSortCollectionByLang($configurations) {

        $result = array();
        foreach ($configurations->documents as $configuration) {
            $result[$configuration->language]['documents'][] = $configuration;
        }

        foreach ($configurations->personalDataUsages as $configuration) {
            $result[$configuration->language]['pdu'][] = $configuration;
        }

        return $result;
    }
}
