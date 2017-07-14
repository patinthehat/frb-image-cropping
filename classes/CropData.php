<?php


class CropData
{
	public $max_size       = 600;
	
	public $initial_top    = 0;
	public $initial_bottom = 0;
	public $initial_left   = 0;
	public $initial_right  = 0;
	
	public $final_landscape= 0;
	public $final_portrait = 0;
	
	public $final_crop_needed = true;
	

	
	//-----
	
	public $landscape;
	public $isPortrait;
	public $first_face;
	public $w;
	public $h;
	public $width;
	public $height;
	public $full_crop;
	
	//-------
	
	public $spaces;
	public $deltas;
	
	public $left_space = -1;
	public $right_space = -1;
	public $top_space = -1;
	public $bottom_space = -1;

	public $delta_left_right = -1;
	public $delta_right_left = -1;
	public $delta_top_bottom = -1;
    public $delta_bottom_top = -1;
                                
	
    public function __construct()
    {
        $this->spaces = new CropSpace;
        $this->deltas = new CropDelta;
    }
    
}


