<?php

//namespace IADB\Http;

class HttpResponse
{
    public $content_type   = null;
    public $content_length = 0;
    public $http_code      = 0;	    
    public $rawData        = null;
    public $content        = null;
    
    public static function create($info, $response)
    {
        $resp = new static;	        
        
        foreach($info as $name=>$value) {
            $name = preg_replace('/^(download|upload)_content_length/', 'content_length', $name);
            if (isset($resp->$name)) 
                $resp->$name = $value;
        }
        $resp->rawData = $response;
        $resp->content = $response;
        
        //if ($resp->content_type == 'application/json')
        $resp->content = json_decode($resp->rawData);
        
        return $resp;
    }
    
    public function success()
    {
        return !is_null($this->content) && $this->http_code == 200;
    }
}