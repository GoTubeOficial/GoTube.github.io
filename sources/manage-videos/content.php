<?php 
if (IS_LOGGED == false) {
	header("Location: " . PT_Link('login'));
	exit();
}

$list = '<div class="text-center no-content-found">' . $lang->no_videos_found_for_now . '</div>';
$final = '';

$videos = $db->where('user_id', $user->id)->orderBy('id', 'DESC')->get(T_VIDEOS, 20);
if (!empty($videos)) {
	$len = count($videos);
	foreach ($videos as $key => $video) {
		$video = PT_GetVideoByID($video, 0, 0, 0);
	    $pt->last_video = false;
	    if ($key == $len - 1) {
	        $pt->last_video = true;
	    }
	    $final .= PT_LoadPage('manage-videos/list', array(
	        'ID' => $video->id,
	        'USER_DATA' => $video->owner,
	        'THUMBNAIL' => $video->thumbnail,
	        'URL' => $video->url,
	        'TITLE' => $video->title,
	        'DESC' => $video->markup_description,
	        'VIEWS' => $video->views,
	        'TIME' => $video->time_ago
	    ));
	}
}

if (empty($final)) {
	$final = $list;
}

$pt->page = 'manage-video';
$pt->videos  = $videos;
$pt->title = $lang->manage_videos . ' | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword = $pt->config->keyword;
$pt->content = PT_LoadPage('manage-videos/content', array('VIDEOS' => $final));