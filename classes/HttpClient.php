<?php


class HttpClient
{
    protected $curlHandle = null;

    public function construct()
    {
        //
    }

    protected function createCurl()
    {
    	$this->ch = curl_init();
    	curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }
    
    protected function destroyCurl()
    {
        if (!is_null($this->ch))
    	   curl_close($this->ch);
        $this->ch = null;
    }

    public function send($method, $url, $params, $decodeJson = true)
    {
        $this->createCurl();
        $isPost = (strtolower($method)=='post' ? 1 : 0);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POST, $isPost);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
    	$rawResponse = curl_exec($this->ch);
    	$info = curl_getinfo($this->ch);
    	$resp = HttpResponse::create($info, $rawResponse);
    	$this->destroyCurl();

    	return $resp;
    }

    public function post($url, $data)
    {
        return $this->send("post", $url, $data);
    }

    public function get($url, $params)
    {
        return $this->send("get", $url, $data);
    }
}