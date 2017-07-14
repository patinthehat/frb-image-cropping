<?php

class WebApplication
{
    
    public $allowUserUploads = true;
    public $facialRecognitionCropper;
    public $imageUploadHandler;
    public $title = 'Facial Recognition Crop';
    
    public function __construct($allowUploads, $apiConfig)
    {
        $this->imageUploadHandler = new ImageUploadHandler(0);
        $this->facialRecognitionCropper = new FacialRecognitionCropping($apiConfig);
    }
    
    
}
