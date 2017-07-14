<?php 

class ApiClient
{
    protected $client;
    protected $config;
    public $response;

	public function __construct(ApiConfiguration $config)
    {
        $this->config = $config;
        $this->client = new HttpClient();
    }

	protected function getEndpointUrl($action)
    {
        $url = $this->config->url;
        $url = str_replace('{APIVERSION}', $this->config->apiVersion, $url);
        $url = str_replace('{ENDPOINTNAME}', $action, $url);

        return $url;
	}

	protected function sendRequest($endpointName, $params)
	{
        $url = $this->getEndpointUrl($endpointName);
        $this->response = $this->client->post($url, $params);

	    return $this->response;
	}
}