<?php



class FacialRecognitionCropping
{
    public $cropData;
    public $oringlaImageInfo;
    public $original_image;
    public $random_number;
    public $error;
	//get sample image from url
	public $sample = 1;
    public $apiclient = null;
    public $apiConfig;
    public $im;

    public $spaces;
    public $deltas;

    public function __construct(ApiConfiguration $apiConfig)
    {
        //$this->oringalImageInfo = ImageInformation();

        $this->sample = isset($_GET['sample']) ? $_GET['sample'] : 1;
        $this->random_number = mt_rand(0, 9999999);

        $this->im = new Imagick();
        $this->cropData = new CropData;
        $this->apiConfig = $apiConfig;
        $this->apiclient = new FacePlusPlusApiClient($apiConfig);

        $this->deltas = new CropDelta;
        $this->spaces = new CropSpace;
    }

    //proxy requests for attributes to the cropData object if not found on $this
    public function __get($name)
    {
        if (isset($this->cropData->$name))
            return $this->cropData->name;
    }

    protected function getSample()
    {
        return isset($_GET['sample']) ? $_GET['sample'] : 1;
    }

    protected function getOriginalImageUrl($sample)
    {
    	switch ($sample) {
            case 2:
                return 'https://www.iadb.com/frc/landscape-shifted.jpg';
            case 3:
                return 'https://www.iadb.com/frc/portrait.jpg';
            case 4:
                return 'https://www.iadb.com/frc/portrait-shifted.jpg';
            case 1:
            default:
                return 'https://www.iadb.com/frc/landscape.jpg';
        	}
    }

    protected function retrieveImage($url)
    {
        $binary = file_get_contents($url);
    	try{
    		$this->im->readImageBlob($binary);
    	} catch(Exception $e){
    	    return false;
    	}
    	return true;
    }

    function step1()
    {
    	/*
    		STEP 1 - define original image
    	*/
        $this->sample = $this->getSample();
        $this->original_image = $this->getOriginalImageUrl($this->sample);
        $this->retrieveImage($this->original_image);

        //get dimensions
    	$this->cropData->w = $this->im->getImageWidth();
    	$this->cropData->h = $this->im->getImageHeight();
    }

    function step2()
    {
    	/*
    		STEP 2 - resize sample image
    	*/
    	$this->im->scaleImage($this->cropData->max_size, 0);
    	$this->im->setImageCompressionQuality(80);
    	$this->im->setImageFormat("jpg");
    	$this->im->writeImage(asset("resized.jpg"));

    	//get dimensions
    	$this->cropData->w_new = $this->im->getImageWidth();
    	$this->cropData->h_new = $this->im->getImageHeight();
    	$this->cropData->landscape = ($this->cropData->w_new > $this->cropData->h_new ? true : false);
    	$this->cropData->isPortrait = !$this->cropData->landscape;
    }

    function step3()
    {
    	/*
    		STEP 3 - find the face
    	*/
    	$this->cropData->first_face = $this->apiclient->detect(); //$response->faces[0]->face_rectangle;
    }

    function step4()
    {
    	/*
    		STEP 4 - find the extra space
    	*/

    	//calculate some spaces
    	if ($this->cropData->landscape) {
    		$this->cropData->spaces->left = $this->cropData->first_face->left;
    		$this->cropData->spaces->right = $this->cropData->w_new - $this->cropData->spaces->left - $this->cropData->first_face->width;
    		$this->cropData->deltas->left_right = $this->cropData->spaces->left - $this->cropData->spaces->right;
    		$this->cropData->deltas->right_left = $this->cropData->spaces->right - $this->cropData->spaces->left;

    		$this->cropData->spaces->left = $this->cropData->first_face->left;
    		$this->cropData->spaces->right = $this->cropData->w_new - $this->cropData->spaces->left - $this->cropData->first_face->width;
    		$this->cropData->deltas->left_right = $this->cropData->spaces->left - $this->cropData->spaces->right;
    		$this->cropData->deltas->right_left = $this->cropData->spaces->right - $this->cropData->spaces->left;


    		//full cropping needed
    		$this->cropData->full_crop = $this->cropData->max_size - $this->cropData->h_new;

    		//if cropping from the left
    		if($this->cropData->deltas->left_right > $this->cropData->deltas->right_left){
    			//if final crop will be needed
    			if($this->cropData->full_crop > $this->cropData->deltas->left_right){
    				//crop the initial width
    				$this->cropData->initial_left = $this->cropData->deltas->left_right;
    			} else {
    				//crop the initial width
    				$this->cropData->final_crop_needed = false;
    				$this->cropData->initial_left = $this->cropData->full_crop;
    			}
    		}

    		//if cropping from the right
    		else {
    			//if final crop will be needed; crop the initial width
    			if($this->cropData->full_crop > $this->cropData->deltas->right_left){
    				$this->cropData->initial_right = $this->cropData->deltas->right_left;
    			} else {
    				$this->cropData->final_crop_needed = false;
    				$this->cropData->initial_right = $this->cropData->full_crop;
    			}
    		}

    		//perform final crop
    		if($this->cropData->final_crop_needed) {
    			$this->cropData->final_landscape = ($this->cropData->full_crop - $this->cropData->initial_right - $this->cropData->initial_left) / 2;
    		}


    		return 'landscape';
    	} // end if landscape


    	if ($this->cropData->isPortrait) {
    		$this->cropData->spaces->top = $this->cropData->first_face->top;
    		$this->cropData->spaces->bottom = $this->cropData->h_new - $this->cropData->spaces->top - $this->cropData->first_face->height;
    		$this->cropData->deltas->top_bottom = $this->cropData->spaces->top - $this->cropData->spaces->bottom;
    		$this->cropData->deltas->bottom_top = $this->cropData->spaces->bottom - $this->cropData->spaces->top;

    		//full cropping needed
    		$this->cropData->full_crop = $this->cropData->h_new - $this->cropData->max_size;


    		//if cropping from the left
    		if($this->cropData->deltas->top_bottom > $this->cropData->deltas->bottom_top){
    			//if final crop will be needed
    			if($this->cropData->full_crop > $this->cropData->deltas->top_bottom){
    				//crop the initial width
    				$this->cropData->initial_top = $this->cropData->deltas->top_bottom;

    			} else {
    				//crop the initial width
    				$this->cropData->final_crop_needed = false;
    				$this->cropData->initial_top = $this->cropData->full_crop;
    			}
    		}

    		//if cropping from the right
    		else {

    			//if final crop will be needed
    			if($this->cropData->full_crop > $this->cropData->deltas->bottom_top){

    				//crop the initial width
    				$this->cropData->initial_bottom = $this->cropData->deltas->bottom_top;

    			}
    			else{

    				//crop the initial width
    				$this->cropData->final_crop_needed = false;
    				$this->cropData->initial_bottom = $this->cropData->full_crop;
    			}
    		}

    		//perform final crop
    		if($this->cropData->final_crop_needed){

    			$this->cropData->final_portrait =
    			    ( $this->cropData->full_crop
    			    - $this->cropData->initial_bottom
    			    - $this->cropData->initial_top) / 2;
    		}


    		return 'portrait';
    	}

    	//this should never be reached
    	throw new \Exception('Error: Could not determine image layout.');
    }

    function step5($outputFile = "cropped.jpg")
    {
    	/*
    		STEP 5 - crop final image
    	*/
        $max_size = $this->cropData->max_size;
        $initial_left = $this->cropData->initial_left;
        $initial_top = $this->cropData->initial_top;
        $final_landscape = $this->cropData->final_landscape;
        $final_portrait = $this->cropData->final_portrait;

    	$this->im->cropImage($max_size, $max_size, $initial_left + $final_landscape, $initial_top + $final_portrait);
    	$this->im->writeImage(asset($outputFile));
    }
}
