<?php


class ApiConfiguration
{
    public $name;
    public $apiVersion;
    public $url;
    public $key;
    public $secret;
    public $imageUrl;

	public static function create($name, $apiVersion, $url, $key, $secret, $imageUrl)
    {
        $result = new static;
        $result->name = $name;
        $result->apiVersion = $apiVersion;
        $result->url = $url;
        $result->key = $key;
        $result->secret = $secret;
        $result->imageUrl = $imageUrl;
        return $result;
    }
}