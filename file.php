<?php
// SummerNote Server-side upload script
$contents_path = isset($_SESSION['contents_path']) ? $_SESSION['contents_path']:'files';

$do = isset($_POST['do']) ? $_POST['do'] : isset($_GET['do']) ? $_GET['do'] : '';

$image_exts = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
$document_ext = array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt');

if ($do=='ul') {
	$type = isset($_POST['type']) ? $_POST['type'] : isset($_GET['type']) ? $_GET['type'] : '';
	if ($_FILES['file']['name']) {
		if (!$_FILES['file']['error']) {
			$filename = $_FILES['file']['name'];
			$ext = strtolower(end((explode(".", $filename))));
			if ($type == 'images') {
				$exts = $image_exts;
			} else {
				$exts = $document_ext;
			}
			if (in_array($ext, $exts)) {
				if (!file_exists("$contents_path/$type")) {
					mkdir("$contents_path/$type", 0755, true);
				}
				$destination = dirname(__FILE__)."/../../$contents_path/$type/$filename";
				$location = $_FILES["file"]["tmp_name"];
				move_uploaded_file($location, $destination);
				echo "$contents_path/$type/$filename";
			} else {
				echo  $message = 'Ooops! File extension is not permit';
			}
		} else {
			echo  $message = 'Ooops! Your upload triggered the following error:  '.$_FILES['file']['error'];
		}
	} else {
		echo  $message = 'Ooops! You are upload without file.';
	}
}
if ($do=='ls') {
	$type = isset($_POST['type']) ? $_POST['type'] : isset($_GET['type']) ? $_GET['type'] : '';

	$dir = dirname(__FILE__)."/../../$contents_path/$type";


	$list = array();
	$dir = new DirectoryIterator($dir);

	foreach ($dir as $fileinfo) {
	    if ($fileinfo->isFile()) {
	        $ext = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
			if ($type == 'images') {
				if (in_array($ext, $image_exts)) {
					$list[] = $fileinfo->getFilename();//array($fileinfo->getFilename()=>'f');
				}
			} else {
				$list[] = $fileinfo->getFilename();//array($fileinfo->getFilename()=>'f');
			}
	    } else if ($fileinfo->isDir() && ! $fileinfo->isDot()) {
			$list[] = $fileinfo->getFilename();//array($fileinfo->getFilename()=>'d');
		}
	}
	echo json_encode($list);
}
?>
