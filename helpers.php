<?php


function image_url($imageName, $frc)
{
    return "images/$imageName.jpg?r=".$frc->random_number;
}

function asset($relativeName)
{
    $relativeName = trim($relativeName);
   // $relativeName = (str_replace('..', '', $relativeName));
    $fileExtension = pathinfo($relativeName, PATHINFO_EXTENSION);

    switch ($fileExtension) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return "images/$relativeName";
        case 'css':
        case 'js':
            return "assets/$relativeName";
        default:
            return $relativeName;
    }

}