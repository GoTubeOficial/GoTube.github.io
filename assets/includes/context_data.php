<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.playtubescript.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | PlayTube - The Ultimate Video Sharing Platform
// | Copyright (c) 2017 PlayTube. All rights reserved.
// +------------------------------------------------------------------------+


$pt->months   = array(
    '1'  => 'January',
    '2'  => 'February',
    '3'  =>'March',
    '4'  =>'April',
    '5'  =>'May',
    '6'  =>'June',
    '7'  =>'July',
    '8'  =>'August',
    '9'  =>'September',
    '10' =>'October',
    '11' =>'November',
    '12' =>'December'
);

$pt->ads_media_types = array(
    'video/mp4',
    'video/mov',
    'video/mpeg',
    'video/flv',
    'video/avi',
    'video/webm',
    'video/quicktime',
    'image/png',
    'image/jpeg',
    'image/gif'
);

$pt->notif_data = array(
	'subscribed_u' => array(
		'icon' => 'user-plus', 
		'text' => $lang->subscribed_u
	),
	'unsubscribed_u' => array(
		'icon' => 'user-times',
		'text' => $lang->unsubscribed_u
	),
	'liked_ur_video' => array(
		'icon' => 'thumbs-up',
		'text' => $lang->liked_ur_video
	),
	'disliked_ur_video' => array(
		'icon' => 'thumbs-down',
		'text' => $lang->disliked_ur_video
	),
	'commented_ur_video' => array(
		'icon' => 'comments-o',
		'text' => $lang->commented_ur_video
	),
	'liked_ur_comment' => array(
		'icon' => 'thumbs-up',
		'text' => $lang->liked_ur_comment
	),
	'disliked_ur_comment' => array(
		'icon' => 'thumbs-down',
		'text' => $lang->disliked_ur_comment
	),
	'replied_2ur_comment' => array(
		'icon' => 'reply',
		'text' => $lang->replied_2ur_comment
	),
	'added_video' => array(
		'icon' => 'video-camera',
		'text' => $lang->added_new_video
	),
);
