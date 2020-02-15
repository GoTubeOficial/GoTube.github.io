<?php
if (IS_LOGGED == false) {
    header("Location: " . PT_Link('login'));
    exit();
}
if (empty($_GET['id'])) {
    header("Location: " . PT_Link('login'));
    exit();
}
$id    = PT_Secure($_GET['id']);
$video = $db->where('id', $id)->getOne(T_VIDEOS);
if (empty($video)) {
    header("Location: " . PT_Link('login'));
    exit();
}
if (!PT_IsAdmin()) {
    if (empty($db->where('id', $id)->where('user_id', $user->id)->getValue(T_VIDEOS, 'count(*)'))) {
        header("Location: " . PT_Link('login'));
        exit();
    }
}
$video           = PT_GetVideoByID($video, 0, 0, 0);
$pt->video       = $video;
$pt->page        = 'edit-video';
$pt->title       = $lang->edit_video . ' | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword     = $pt->config->keyword;
$pt->content     = PT_LoadPage('edit-video/content', array(
    'ID' => $video->id,
    'USER_DATA' => $video->owner,
    'THUMBNAIL' => $video->thumbnail,
    'URL' => $video->url,
    'TITLE' => $video->title,
    'DESC' => br2nl($video->edit_description),
    'DESC_2' => $video->markup_description,
    'VIEWS' => $video->views,
    'TIME' => $video->time_ago,
    'TAGS' => $video->tags,

));