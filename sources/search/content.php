<?php
if (empty($_GET['keyword'])) {
	header("Location: " . PT_Link('login'));
    exit();
}
$keyword = PT_Secure($_GET['keyword']);

$list = '<div class="text-center no-content-found">' . $lang->no_videos_found_for_now . '</div>';
$final = '';

if ($pt->config->total_videos > 1000000) {
    $get_videos = $db->rawQuery("SELECT * FROM " . T_VIDEOS . " WHERE MATCH (title) AGAINST ('$keyword') ORDER BY id ASC LIMIT 20");
} else {
    $get_videos = $db->rawQuery("SELECT * FROM " . T_VIDEOS . " WHERE title LIKE '%$keyword%' ORDER BY id ASC LIMIT 20");
}

if (!empty($get_videos)) {
    $len = count($get_videos);
    foreach ($get_videos as $key => $video) {
        $video = PT_GetVideoByID($video, 0, 0, 0);
        $pt->last_video = false;
        if ($key == $len - 1) {
            $pt->last_video = true;
        }
        $final .= PT_LoadPage('subscriptions/list', array(
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
$pt->videos      = $get_videos;
$pt->page        = 'search';
$pt->title       = $lang->search . ' | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword     = $pt->config->keyword;
$pt->content     = PT_LoadPage('search/content', array(
    'VIDEOS' => $final,
    'KEYWORD' => $keyword
));