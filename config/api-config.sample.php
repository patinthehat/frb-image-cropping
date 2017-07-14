<?php 

$apiConfig = IADB\ApiConfiguration::create(
    //service name
    'FacePlusPlus',
    //service API version/prefix
    'v3',
    //servce endpoint template
    'https://api-us.faceplusplus.com/facepp/{APIVERSION}/{ENDPOINTNAME}',
    //app key
    FPP_APPLICATION_KEY ,
    //app secret
    FPP_APPLICATION_SECRET,
    //url to image for processing
    'https://www.iadb.com/frc/resized.jpg'
  );
