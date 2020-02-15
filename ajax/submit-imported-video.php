<?php
if (IS_LOGGED == false || $pt->config->import_system != 'on') {
    $data = array('status' => 400, 'error' => 'Not logged in');
    echo json_encode($data);
    exit();
}

$getID3 = new getID3;

if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['tags']) || empty($_POST['thumbnail-image'])) {
    $error = $lang->please_check_details;
}

else if (empty($_POST['video-id']) || empty($_POST['video-type'])) {
    $error = $lang->video_not_found_please_try_again;
}

else if (!empty($_FILES['thumbnail'])) {
    $media_file = getimagesize($_FILES["thumbnail"]["tmp_name"]);
    $img_types  = array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_BMP);

    if (!in_array($media_file[2],$img_types)) {
        $error  = $lang->ivalid_thumb_file;
    }
}

if (empty($error)) {

	$duration        = '00:00';
    $video_id        = PT_GenerateKey(15, 15);
    $check_for_video = $db->where('video_id', $video_id)->getValue(T_VIDEOS, 'count(*)');
    $thumbnail       = PT_Secure($_POST['thumbnail-image'], 0);
    $category_id     = 0;
    $type            = "";
    $link_regex      = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
    $i               = 0;
    $video_ok        = false;

    if (!empty($_POST['duration'])) {
        $duration = PT_Secure($_POST['duration']);
    }
    
    if ($check_for_video > 0) {
        $video_id = PT_GenerateKey(15, 15);
    }
    
    if (!empty($_FILES['thumbnail']['tmp_name'])) {
        $file_info = array(
            'file' => $_FILES['thumbnail']['tmp_name'],
            'size' => $_FILES['thumbnail']['size'],
            'name' => $_FILES['thumbnail']['name'],
            'type' => $_FILES['thumbnail']['type'],
            'allowed'    => 'jpg,png,jpeg,gif',
            'crop'       => array(
                'width'  => 538,
                'height' => 302
            )
        );

        $file_upload   = PT_ShareFile($file_info);
        if (!empty($file_upload['filename'])) {
            $thumbnail = PT_Secure($file_upload['filename'], 0);
        }
    }

    if (!empty($_POST['category_id'])) {
        if (in_array($_POST['category_id'], array_keys($categories))) {
            $category_id = PT_Secure($_POST['category_id']);
        }
    }

    
    preg_match_all($link_regex, PT_Secure($_POST['description']), $matches);
    foreach ($matches[0] as $match) {
        $match_url            = strip_tags($match);
        $syntax               = '[a]' . urlencode($match_url) . '[/a]';
        $_POST['description'] = str_replace($match, $syntax, $_POST['description']);
    }

    $data_insert      = array(
        'video_id'    => $video_id,
        'user_id'     => $user->id,
        'title'       => PT_Secure($_POST['title']),
        'description' => PT_Secure($_POST['description']),
        'tags'        => PT_Secure($_POST['tags']),
        'duration'    => $duration,
        'category_id' => $category_id,
        'thumbnail'   => $thumbnail,
        'time'        => time(),
        'registered'  => date('Y') . '/' . intval(date('m')),
        'type'        => $type
    );



    
    if ($_POST['video-type'] == 'youtube') {
    	$data_insert['youtube'] = PT_Secure($_POST['video-id']);
    	$video_ok = true;
    }

    if ($_POST['video-type'] == 'vimeo') {
    	$data_insert['vimeo'] = PT_Secure($_POST['video-id']);
    	$video_ok = true;
    }

    if ($_POST['video-type'] == 'daily') {
        $data_insert['daily'] = PT_Secure($_POST['video-id']);
        $video_ok = true;
    }

    if ($_POST['video-type'] == 'mp4') {
        $data_insert['video_location'] = urlencode($_POST['video-id']);
    	$data_insert['type'] = 4;
    	$video_ok            = true;
    }

    if ($video_ok == true) {
    	$insert   = $db->insert(T_VIDEOS, $data_insert);

	    if ($insert) {
	        $data          = array(
	            'status'   => 200,
	            'video_id' => $video_id,
	            'link'     => PT_Link("watch/$video_id")
	        );
	    }
    }
} 

else {
    $data = array(
        'status'  => 400,
        'message' => $error_icon . $error
    );
}
