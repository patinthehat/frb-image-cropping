<?php
	//include_once($dir.'functions.php');

    require(__DIR__.'/autoload.php');
    require(__DIR__.'/helpers.php');
//====================================================================
    include(__DIR__."/config/api-config.php");

    $app = new WebApplication(true, $apiConfig);
	$frc = new FacialRecognitionCropping($apiConfig); //$app->facialRecognitionCropper;

	if ($app->allowUserUploads && $app->imageUploadHandler->userIsUploading())
        $app->imageUploadHandler->handleUpload();

	/*
	if(!empty($_FILES)){
		$frc->sample = 0;
		$frc->error  = '';
		$target_file = asset('test.jpg');

		try {
            $imageFileType = pathinfo($_FILES["files"]["name"], PATHINFO_EXTENSION);
            $check = getimagesize($_FILES["files"]["tmp_name"]);

		    if ($check === false)
		        throw new \Exception('File is not an image.');

            if (!in_array(strtolower($imageFileType), ['jpg','png','jpeg','gif']))
               throw new \Exception('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');

            if(!move_uploaded_file($_FILES["files"]["tmp_name"], $target_file))
                throw new \Exception('Failed to upload.');

		} catch(\Exception $e) {
		    $frc->error = $e->getMessage();
		}

        /*
        if($check !== false) {
            if (!in_array($imageFileType, ['jpg','png','jpeg','gif'])) {
                $frc->error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                if(!move_uploaded_file($_FILES["files"]["tmp_name"], $target_file))
                    $frc->error = 'Failed to upload.';
    	   }
        } else {
            $frc->error = "File is not an image.";
        }

	}*/

	$frc->step1();
	$frc->step2();
	$frc->step3();
	$frc->step4();
	$frc->step5();

	$data = $frc->cropData;

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?=$app->title?></title>
  <meta name="description" content="<?=$app->title?>">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo asset('styles.css'); ?>">
  <style>
		.face {
			top:<?=$data->first_face->top;?>px;
			left:<?=$data->first_face->left;?>px;
			width:<?=$data->first_face->width;?>px;
			height:<?=$data->first_face->height;?>px;
		}
        /*
		.initial_left,
		.initial_right {
			height:< ?=$data->h_new;? >px;
		}
        */
		.initial_left {
			width:<?=$data->initial_left;?>px;
		}
		.initial_right {
			left:<?=$data->w_new - $data->initial_right;?>px;
			width:<?=$data->initial_right;?>px;
		}
        .final_left,
        .final_right,
        .initial_left,
        .initial_right {
             height:<?=$data->h_new;?>px;
        }

        .final_top,
        .final_bottom,
        .initial_top,
        .initial_bottom {
            width:<?=$data->w_new;?>px;
        }

        /*
		.final_left,
		.final_right {
			height:< =$data->h_new;? >px;
		}*/

		.final_left {
			left:<?=$data->initial_left;?>px;
			width:<?=$data->final_landscape;?>px;
		}
		.final_right {
			left:<?=$data->w_new - $data->final_landscape;?>px;
			width:<?=$data->final_landscape;?>px;
		}
/*
		.initial_top,
		.initial_bottom {
			width:< ?=$data->w_new;? >px;
		}
*/
		.initial_top {
			height:<?=$data->initial_top;?>px;
		}
		.initial_bottom {
			top:<?=$data->h_new - $data->initial_bottom - $data->initial_top;?>px;
			height:<?=$data->initial_bottom;?>px;
		}

        /*
		.final_top,
		.final_bottom {
			width:< ?=$data->w_new;? >px;
		}
    */
		.final_top {
			top:<?=$data->initial_top;?>px;
			height:<?=$data->final_portrait;?>px;
		}
		.final_bottom {
			top:<?=$data->h_new - $data->initial_bottom - $data->initial_top - $data->final_portrait;?>px;
			height:<?=$data->final_portrait;?>px;
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
                <li class="<?=$frc->sample == 1 ? 'active' : '';?>"><a href="?sample=1">Landscape</a></li>
                <li class="<?=$frc->sample == 2 ? 'active' : '';?>"><a href="?sample=2">Landscape Shifted</a></li>
                <li class="<?=$frc->sample == 3 ? 'active' : '';?>"><a href="?sample=3">Portrait</a></li>
                <li class="<?=$frc->sample == 4 ? 'active' : '';?>"><a href="?sample=4">Portrait Shifted</a></li>
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

<?php     if(!empty($frc->error)) {  ?>
              <div class="alert alert-warning"><?=$frc->error;?></div>
<?php     } ?>

            <!-- display original image -->
            <div class="holder">
            	<div class="definition">
                <h3>Original Image</h3>
                <h5>Note: width:<?=$data->w;?>px | height:<?=$data->h;?>px</h5>
              </div>
              <img src="<?=$frc->original_image;?>?r=<?=$frc->random_number;?>" alt="Original Image" class="img-responsive" />
            </div>

            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
                <h3>Resized Image</h3>
                <h5>Note: max-width:<?=$data->max_size;?>px</h5>
              </div>
              <img src="images/resized.jpg?r=<?=$frc->random_number;?>" alt="Resized Image" class="img-responsive" />
            </div>

            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
                <h3>Show Face</h3>
                <h5>Note: used for single face only</h5>
              </div>
              <div class="holder">
                <div class="face"></div>
                <img src="images/resized.jpg?r=<?=$frc->random_number;?>" alt="Resized Image" class="img-responsive" />
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
                <img src="images/resized.jpg?r=<?=$frc->random_number;?>" alt="Resized Image" class="img-responsive" />
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
                <img src="images/resized.jpg?r=<?=$frc->random_number;?>" alt="Resized Image" class="img-responsive" />
              </div>
            </div>

            <!-- display resized image -->
            <div class="holder">
            	<div class="definition">
            		<h3>Final Image</h3>
                <h5><strong>Brought to you by Tomasz Mieczowski of <a href="https://www.iadb.com">IADB</a> and <a href="mail:patrick@permafrost-software.com">Patrick Organ</a></h5>
                </div>
              <div class="holder">
                <img src="images/cropped.jpg?r=<?=$frc->random_number;?>" alt="Cropped Image" class="img-responsive" />
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
    <script src="<?php asset('scripts.js'); ?>"></script>
</body>
</html>