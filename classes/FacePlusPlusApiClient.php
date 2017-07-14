<?php


class FacePlusPlusApiClient extends ApiClient
{
    public function detect()
    {
        $response = $this->sendRequest(
            'detect',
            //"api_key=".$key."&api_secret=".$secret."&image_url=https://www.iadb.com/frc/resized.jpg"
            "api_key=".$this->config->key."&api_secret=".$this->config->secret."&image_url=".$this->config->imageUrl
        );
        $this->first_face = $response->content->faces[0]->face_rectangle;
        return $this->first_face;
    }
}