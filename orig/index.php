<?
	include_once($dir.'functions.php');
	
	/*
		STEP 0 - defaults
	*/
	
	//include imagick for resizing
	$im = new Imagick();

	//define faceplusplus API
	$url = 'https://api-us.faceplusplus.com/facepp/v3/detect';
	$key = 'noYRcO3F0uGyPgWhU-n0TfBnd-ex_ipI';
	$secret = 'uT8SzzSxN3ihF1WpzcoTpqdj-90aICXh';
	
	//define max width or height of the image
	$max_size = 600;
	$initial_top = 0;
	$initial_bottom = 0;
	$initial_left = 0;
	$initial_right = 0;
	$final_landscape = 0;
	$final_portrait = 0;
	$final_crop_needed = true;
	$random_number = mt_rand(0, 9999999);
	
	//get sample image from url
	$sample = isset($_GET['sample']) ? $_GET['sample'] : 1;
	
	if(!empty($_FILES)){
		$sample = 0;
		
		$target_file = 'test.jpg';
		$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		
		$check = getimagesize($_FILES["files"]["tmp_name"]);
    if($check !== false) {
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
					$error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
				}
				else {
					if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_file)){
						//success
					}
					else {
						$error = 'Failed to upload.';
					}
				}
    } else {
        $error = "File is not an image.";
    }
	}
	
	
	/*
		STEP 1 - define original image
	*/
	
	//path to original image
	switch ($sample) {
		case 0:
			$original_image = 'https://www.iadb.com/frc/test.jpg';	
      break;
    case 1:
			$original_image = 'https://www.iadb.com/frc/landscape.jpg';	
      break;
    case 2:
			$original_image = 'https://www.iadb.com/frc/landscape-shifted.jpg';	
      break;
    case 3:
			$original_image = 'https://www.iadb.com/frc/portrait.jpg';	
      break;
    case 4:
			$original_image = 'https://www.iadb.com/frc/portrait-shifted.jpg';	
      break;
	}
	
	//convert it to binary
	$binary = file_get_contents($original_image);
	try{ 
		$im->readImageBlob($binary); 
	}
	catch(Exception $e){}
	
	//get dimensions
	$w = $im->getImageWidth();
	$h = $im->getImageHeight();
	
	/*
		STEP 2 - resize sample image
	*/
	$im->scaleImage($max_size, 0);
	$im->setImageCompressionQuality(80);
	$im->setImageFormat("jpg");
	$im->writeImage("resized.jpg");
	
	//get dimensions
	$w_new = $im->getImageWidth();
	$h_new = $im->getImageHeight();
	$landscape = $w_new > $h_new ? true : false;
	
	/*
		STEP 3 - find the face
	*/
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "api_key=".$key."&api_secret=".$secret."&image_url=https://www.iadb.com/frc/resized.jpg");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$response = json_decode($server_output);
	$first_face = $response->faces[0]->face_rectangle;
	curl_close ($ch);
	
	
	/*
		STEP 4 - find the extra space
	*/
	
	//calculate some spaces
	if($landscape){
		$left_space = $first_face->left;
		$right_space = $w_new - $left_space - $first_face->width;
		$delta_left_right = $left_space - $right_space;
		$delta_right_left = $right_space - $left_space;
			
		//full cropping needed
		$full_crop = $max_size - $h_new;
	
		//if cropping from the left
		if($delta_left_right > $delta_right_left){
		
			//if final crop will be needed
			if($full_crop > $delta_left_right){
				
				//crop the initial width
				$initial_left = $delta_left_right;
				
			}
			else{
				
				//crop the initial width
				$final_crop_needed = false;
				$initial_left = $full_crop;
			}
		}
		
		//if cropping from the right
		else {
			
			//if final crop will be needed
			if($full_crop > $delta_right_left){
				
				//crop the initial width
				$initial_right = $delta_right_left;
				
			}
			else{
				
				//crop the initial width
				$final_crop_needed = false;
				$initial_right = $full_crop;
			}
		}
		
		//perform final crop
		if($final_crop_needed){
			
			$final_landscape = ($full_crop - $initial_right - $initial_left) / 2;
			
		}
		
	}
	else {
		$top_space = $first_face->top;
		$bottom_space = $h_new - $top_space - $first_face->height;
		$delta_top_bottom = $top_space - $bottom_space;
		$delta_bottom_top = $bottom_space - $top_space;
		
		//full cropping needed
		$full_crop = $h_new - $max_size;
	
	
		//if cropping from the left
		if($delta_top_bottom > $delta_bottom_top){
		
			//if final crop will be needed
			if($full_crop > $delta_top_bottom){
				
				//crop the initial width
				$initial_top = $delta_top_bottom;
				
			}
			else{
				
				//crop the initial width
				$final_crop_needed = false;
				$initial_top = $full_crop;
			}
		}
		
		//if cropping from the right
		else {
			
			//if final crop will be needed
			if($full_crop > $delta_bottom_top){
				
				//crop the initial width
				$initial_bottom = $delta_bottom_top;
				
			}
			else{
				
				//crop the initial width
				$final_crop_needed = false;
				$initial_bottom = $full_crop;
			}
		}
		
		//perform final crop
		if($final_crop_needed){
			
			$final_portrait = ($full_crop - $initial_bottom - $initial_top) / 2;
			
		}
	}
	
	/*
		STEP 5 - crop final image
	*/
	$max_size = min($w_new, $h_new);
	$im->cropImage($max_size, $max_size, $initial_left + $final_landscape, $initial_top + $final_portrait);
	$im->writeImage("cropped.jpg");
	
	
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Facial Recognition Crop</title>
  <meta name="description" content="Facial Recognition Crop">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <link rel="stylesheet" href="styles.css">
  <style>
		.face {
			top:<?=$first_face->top;?>px; 
			left:<?=$first_face->left;?>px; 
			width:<?=$first_face->width;?>px; 
			height:<?=$first_face->height;?>px;
		}
		.initial_left, 
		.initial_right {
			height:<?=$h_new;?>px;
		}
		.initial_left {
			width:<?=$initial_left;?>px;
		}
		.initial_right {
			left:<?=$w_new-$initial_right;?>px;
			width:<?=$initial_right;?>px;
		}
		.final_left, 
		.final_right {
			height:<?=$h_new;?>px;
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
		}
		.initial_top {
			height:<?=$initial_top;?>px;
		}
		.initial_bottom {
			top:<?=$h_new - $initial_bottom - $initial_top;?>px;
			height:<?=$initial_bottom;?>px;
		}
		.final_top, 
		.final_bottom {
			width:<?=$w_new;?>px;
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
	
  <div class="container"
    <div class="row">
    
    	<!-- MENU -->
    	<div class="col-sm-3">      
      	<nav class="navbar navbar-default sidebar" role="navigation">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-sidebar-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>      
            </div>
            <div class="collapse navbar-collapse" id="bs-sidebar-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li class="<?=$sample == 1 ? 'active' : '';?>"><a href="?sample=1">Landscape</a></li>
                <li class="<?=$sample == 2 ? 'active' : '';?>"><a href="?sample=2">Landscape Shifted</a></li>        
                <li class="<?=$sample == 3 ? 'active' : '';?>"><a href="?sample=3">Portrait</a></li>      
                <li class="<?=$sample == 4 ? 'active' : '';?>"><a href="?sample=4">Portrait Shifted</a></li>
              </ul>
            </div>
          </div>
        </nav>
        
        <form method="post" enctype="multipart/form-data" id="try_own">
          <label class="btn btn-success">
            Try Your Own Image
            <input type="file" name="files" style="display:none;">
          </label>
        </form>
			</div>
    	<!-- END MENU -->
      
      <!-- OUTPUT -->
      <div class="col-sm-9">
        <div class="row">
          <div class="col-sm-12">
          	
            <?
							//upload error
							if(!empty($error)){
								?>
                	<div class="alert alert-warning"><?=$error;?></div>
                <?	
							}
						?>
            
            <!-- display original image -->
            <div class="holder">
            	<div class="definition">
                <h3>Original Image</h3>
                <h5>Note: width:<?=$w;?>px | height:<?=$h;?>px</h5>
              </div>
              <img src="<?=$original_image;?>?r=<?=$random_number;?>" alt="Original Image" class="img-responsive" />
            </div>
            
            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
                <h3>Resized Image</h3>
                <h5>Note: max-width:<?=$max_size;?>px</h5>
              </div>
              <img src="resized.jpg?r=<?=$random_number;?>" alt="Resized Image" class="img-responsive" />
            </div>
            
            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
                <h3>Show Face</h3>
                <h5>Note: used for single face only</h5>
              </div>
              <div class="holder">
                <div class="face"></div>
                <img src="resized.jpg?r=<?=$random_number;?>" alt="Resized Image" class="img-responsive" />
              </div>
            </div>
            
            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
                <h3>Remove Extra Space</h3>
                <h5>Note: now the face is centered</h5>
              </div>
              <div class="holder">
                <div class="face"></div>
                <div class="initial_left"></div>
                <div class="initial_right"></div>
                <div class="initial_top"></div>
                <div class="initial_bottom"></div>
                <img src="resized.jpg?r=<?=$random_number;?>" alt="Resized Image" class="img-responsive" />
              </div>
            </div>
            
            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
                <h3>Remove Final Space</h3>
                <h5>Note: now the thumbnail is square</h5>
              </div>
              <div class="holder">
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
            </div>
            
            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
            		<h3>Final Image</h3>
                <h5>Note: brought to you by IADB and Patrick Organ</h5>
              </div>
              <div class="holder">
                <img src="cropped.jpg?r=<?=$random_number;?>" alt="Cropped Image" class="img-responsive" />
              </div>
						</div>   
            
          </div>
        </div>
      </div>
      <!-- END OUTPUT -->
      
  	</div>
  </div>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g=" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="scripts.js"></script>
</body>
</html>