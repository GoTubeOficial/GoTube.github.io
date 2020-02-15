<?php
if (IS_LOGGED == false) {
    header("Location: " . PT_Link('login'));
    exit();
}

$list = '<div class="text-center no-content-found">' . $lang->no_videos_found_subs . '</div>';
$final = '';

$get = $db->where('subscriber_id', $user->id)->get(T_SUBSCRIPTIONS);
$userids = array();
foreach ($get as $key => $userdata) {
    $userids[] = $userdata->user_id;
}
$get_subscriptions_videos = false;
$userids = implode(',', ToArray($userids));
if (!empty($userids)) {
    $get_subscriptions_videos = $db->rawQuery("SELECT * FROM " . T_VIDEOS . " WHERE user_id IN ($userids) ORDER BY `id` DESC LIMIT 20");
}
if (!empty($get_subscriptions_videos)) {
    $len = count($get_subscriptions_videos);
    foreach ($get_subscriptions_videos as $key => $video) {
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
$pt->videos      = $get_subscriptions_videos;
$pt->page        = 'subscriptions';
$pt->title       = $lang->subscriptions . ' | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword     = $pt->config->keyword;
$pt->content     = PT_LoadPage('subscriptions/content', array(
    'SUBSCRIPTION_LIST' => $final
));