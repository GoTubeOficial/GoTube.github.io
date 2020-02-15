<?php
if (IS_LOGGED == false || $pt->config->upload_system != 'on') {
    $data = array('status' => 400, 'error' => 'Not logged in');
    echo json_encode($data);
    exit();
}
$getID3   = new getID3;
$featured = ($user->is_pro == 1) ? 1 : 0;
$filesize = 0;

if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['tags']) || empty($_FILES['thumbnail'])) {
    $error = $lang->please_check_details;
}
if (empty($_POST['video-location'])) {
    $error = $lang->video_not_found_please_try_again;
}

if (!empty($_FILES['thumbnail']['tmp_name'])) {
    if ($_FILES['thumbnail']['size'] > $pt->config->max_upload) {
        $max   = pt_size_format($pt->config->max_upload);
        $error = $lang->file_is_too_big .": $max";
    }
}

if (empty($error)) {
    $file     = $getID3->analyze($_POST['video-location']);
    $duration = '00:00';
    if (!empty($file['playtime_string']) ) {
        $duration = PT_Secure($file['playtime_string']);
    }

    if (!empty($file['filesize'])) {
        $filesize = $file['filesize'];
    }

    $video_id        = PT_GenerateKey(15, 15);
    $check_for_video = $db->where('video_id', $video_id)->getValue(T_VIDEOS, 'count(*)');
    if ($check_for_video > 0) {
        $video_id = PT_GenerateKey(15, 15);
    }
    $thumbnail = 'upload/photos/thumbnail.jpg';
    if (!empty($_FILES['thumbnail']['tmp_name'])) {
        $file_info   = array(
            'file' => $_FILES['thumbnail']['tmp_name'],
            'size' => $_FILES['thumbnail']['size'],
            'name' => $_FILES['thumbnail']['name'],
            'type' => $_FILES['thumbnail']['type'],
            'crop' => array(
                'width' => 1076,
                'height' => 604
            )
        );
        $file_upload = PT_ShareFile($file_info);
        if (!empty($file_upload['filename'])) {
            $thumbnail = PT_Secure($file_upload['filename'], 0);
        }
    }
    $category_id = 0;
    if (!empty($_POST['category_id'])) {
        if (in_array($_POST['category_id'], array_keys($categories))) {
            $category_id = PT_Secure($_POST['category_id']);
        }
    }
    $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
    $i          = 0;
    preg_match_all($link_regex, PT_Secure($_POST['description']), $matches);
    foreach ($matches[0] as $match) {
        $match_url           = strip_tags($match);
        $syntax              = '[a]' . urlencode($match_url) . '[/a]';
        $_POST['description'] = str_replace($match, $syntax, $_POST['description']);
    }
    $data_insert = array(
        'video_id' => $video_id,
        'user_id' => $user->id,
        'title' => PT_Secure($_POST['title']),
        'description' => PT_Secure($_POST['description']),
        'tags' => PT_Secure($_POST['tags']),
        'duration' => $duration,
        'video_location' => PT_Secure($_POST['video-location'], 0),
        'category_id' => $category_id,
        'thumbnail' => $thumbnail,
        'time' => time(),
        'registered' => date('Y') . '/' . intval(date('m')),
        'featured' => $featured,
        'size' => $filesize
    );
    $insert      = $db->insert(T_VIDEOS, $data_insert);
    if ($insert) {
        $data = array(
            'status' => 200,
            'video_id' => $video_id,
            'link' => PT_Link("watch/$video_id")
        );
        pt_push_channel_notifiations($video_id);
    }
} 
else {
    $data = array(
        'status' => 400,
        'message' => $error_icon . $error
    );
}
?>