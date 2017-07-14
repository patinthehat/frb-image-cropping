<?php
	include_once('../db.php');
	include_once($dir.'functions.php');

    spl_autoload_register(function ($className) {
        $filename = __DIR__."/classes/$className.php";
        if (file_exists($filename))
            include_once($filename);
    });

	ini_set('memory_limit', '256M');
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);



?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Facial Recognition Crop</title>
  <meta name="description" content="Facial Recognition Crop">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <style>
		.container {
			margin:20px auto;
		}
		.face {
			border:3px solid blue;
			position:absolute;
			display:block;
			top:<?=$first_face->top;?>px;
			left:<?=$first_face->left;?>px;
			width:<?=$first_face->width;?>px;
			height:<?=$first_face->height;?>px;
		}
		.initial_left,
		.initial_right {
			height:<?=$h_new;?>px;
			background:red;
			opacity:0.5;
			position:absolute;
		}
		.initial_left {
			left:0px;
			width:<?=$initial_left;?>px;
		}
		.initial_right {
			left:<?=$w_new;?>px;
			width:<?=$initial_right;?>px;
		}
		.final_left,
		.final_right {
			height:<?=$h_new;?>px;
			background:green;
			opacity:0.5;
			position:absolute;
		}
		.final_left {
			left:<?=$initial_left;?>px;
			width:<?=$final_landscape;?>px;
		}
		.final_right {
			left:<?=$w_new - $final_landscape;?>px;
			width:<?=$final_landscape;?>px;
		}


		.initial_top,
		.initial_bottom {
			width:<?=$w_new;?>px;
			background:red;
			opacity:0.5;
			position:absolute;
		}
		.initial_top {
			top:0px;
			height:<?=$initial_top;?>px;
		}
		.initial_bottom {
			top:<?=$h_new - $initial_bottom - $initial_top;?>px;
			height:<?=$initial_bottom;?>px;
		}
		.final_top,
		.final_bottom {
			width:<?=$w_new;?>px;
			background:green;
			opacity:0.5;
			position:absolute;
		}
		.final_top {
			top:<?=$initial_top;?>px;
			height:<?=$final_portrait;?>px;
		}
		.final_bottom {
			top:<?=$h_new - $initial_bottom - $initial_top - $final_portrait;?>px;
			height:<?=$final_portrait;?>px;
		}
	</style>

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->
</head>
<body>

  <div class="container">

  	<div class="row">
    	<div class="col-sm-12">
        <h3>Pick a Sample</h3>
        <h5>These four samples will showcase the example of image cropping for different dimensions</h5>
      </div>

    	<div class="col-sm-3">
      	<a href="?sample=1">
          <img src="resized.jpg" alt="Resized Image" class="img-responsive" <?=$sample == 1 ? '' : 'style="opacity:0.6"';?> />
          Landscape
       	</a>
      </div>
    	<div class="col-sm-3">
      	<a href="?sample=2">
          <img src="resized.jpg" alt="Resized Image" class="img-responsive" <?=$sample == 2 ? '' : 'style="opacity:0.6"';?> />
          Landscape Shifted
       	</a>
      </div>
    	<div class="col-sm-3">
      	<a href="?sample=3">
          <img src="resized.jpg" alt="Resized Image" class="img-responsive" <?=$sample == 3 ? '' : 'style="opacity:0.6"';?> />
          Portrait
       	</a>
      </div>
    	<div class="col-sm-3">
      	<a href="?sample=4">
          <img src="resized.jpg" alt="Resized Image" class="img-responsive" <?=$sample == 4 ? '' : 'style="opacity:0.6"';?> />
          Portrait Shifted
       	</a>
      </div>
    </div>

  	<div class="row">
    	<div class="col-sm-12">

        <!-- display original image -->
        <h3>Original Image</h3>
        <h5>Note: width:<?=$w;?>px | height:<?=$h;?>px</h5>
        <img src="<?=$original_image;?>" alt="Original Image" class="img-responsive" />

        <!-- display resized image -->
        <h3>Resized Image</h3>
        <h5>Note: max-width:<?=$max_size;?>px</h5>
        <img src="resized.jpg?r=<?=$random_number;?>" alt="Resized Image" class="img-responsive" />

        <!-- display resized image -->
        <h3>Show Face</h3>
        <h5>Note: used for single face only</h5>
        <div style="position:relative">
        	<div class="face"></div>
	        <img src="resized.jpg?r=<?=$random_number;?>" alt="Resized Image" class="img-responsive" />
        </div>

        <!-- display resized image -->
        <h3>Remove Extra Space</h3>
        <h5>Note: now the face is centered</h5>
        <div style="position:relative">
        	<div class="face"></div>
        	<div class="initial_left"></div>
        	<div class="initial_right"></div>
        	<div class="initial_top"></div>
        	<div class="initial_bottom"></div>
	        <img src="resized.jpg?r=<?=$random_number;?>" alt="Resized Image" class="img-responsive" />
        </div>

        <!-- display resized image -->
        <h3>Remove Final Space</h3>
        <h5>Note: now the thumbnail is square</h5>
        <div style="position:relative">
        	<div class="face"></div>
        	<div class="initial_left"></div>
        	<div class="initial_right"></div>
        	<div class="final_left"></div>
        	<div class="final_right"></div>
        	<div class="initial_top"></div>
        	<div class="initial_bottom"></div>
        	<div class="final_top"></div>
        	<div class="final_bottom"></div>
	        <img src="resized.jpg?r=<?=$random_number;?>" alt="Resized Image" class="img-responsive" />
        </div>

        <!-- display resized image -->
        <h3>Final Image</h3>
        <div style="position:relative">
	        <img src="cropped.jpg?r=<?=$random_number;?>" alt="Cropped Image" class="img-responsive" />
        </div>



			</div>
    </div>
  </div>


</body>
</html>