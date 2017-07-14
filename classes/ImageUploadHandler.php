<?php


class ImageUploadHandler
{
    public $sample = 0;
    public $error = 0;
    public $target_file = '';

   public function __construct($sample =0)
   {
       $this->reset($sample);
   }

   public function reset($sample)
   {
       $this->sample = $sample;
       $this->error = '';
       $this->target_file = asset('test.jpg');
   }

   public function userIsUploading()
   {
       return (!empty($_FILES));
   }

   public function handleUpload()
   {
        $allowedFileTypes = ['jpg','png','jpeg','gif'];

        if($this->userIsUploading())
            $this->reset();
            $fileData = $_FILES["files"];

            try {
                $check  = getimagesize($fileData["tmp_name"]);

                if ($check === false)
                    throw new \Exception('Error: File is not an image.');

                // JPG, JPEG, PNG & GIF files are allowed.');
                $fileExt= trim(strtolower(pathinfo(["name"], PATHINFO_EXTENSION)));

                if (!in_array($fileExt, $allowedFileTypes))
                   throw new \Exception('Error: Permitted types: '.implode(', ',$allowedFileTypes).'.');


                if(!move_uploaded_file($fileData["tmp_name"], $target_file))
                    throw new \Exception('Error: Failed to upload.');

            } catch(\Exception $e) {
                $frc->error = $e->getMessage();
            }

	   return true;
   }

}