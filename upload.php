<?php


$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["filImage"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

//Only try to upload, if the varible contains words
if (empty($_FILES["filImage"]["name"])) {
    
} else {



// Check if image file is a actual image or fake image
    if (isset($_POST["filImage"])) {
        $check = getimagesize($_FILES["filImage"]["tmp_name"]);
        if ($check !== false) {
//        echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }


// Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists, please rename it and try again";
        $uploadOk = 0;
    }


// Check file size
    if ($_FILES["filImage"]["size"] > 2120000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }


// Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "JPG"
        && $imageFileType != "gif" && $imageFileType != "GIF" 
        && $imageFileType != "PNG" && $imageFileType != "png") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";

// if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["filImage"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["filImage"]["name"]) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?> 