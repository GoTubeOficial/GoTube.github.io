<?php
if (empty($_GET['first']) || empty($_POST['last_id'])) {
 	$data = array('status' => 404);
} 

else {
	$type = PT_Secure($_GET['first']);
	$id = PT_Secure($_POST['last_id']);
	$views = 0;
	if (!empty($_GET['views'])) {
		$views = PT_Secure($_GET['views']);
	}
	$final = '';
	$user_id = 0;
	if (!empty($_POST['user_id'])) {
		$user_id = PT_Secure($_POST['user_id']);
	}
	if ($type == 'subscriptions') {
		$get = $db->where('subscriber_id', $user->id)->get(T_SUBSCRIPTIONS);
		$userids = array();
		foreach ($get as $key => $userdata) {
		    $userids[] = $userdata->user_id;
		}
		$get_subscriptions_videos = false;
		$userids = implode(',', ToArray($userids));
		if (!empty($userids)) {
			$get_subscriptions_videos = $db->rawQuery("SELECT * FROM " . T_VIDEOS . " WHERE id < $id AND id <> $id AND user_id IN ($userids) ORDER BY `id` DESC LIMIT 40");
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
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} if ($type == 'my_videos') {
		$videos = $db->where('user_id', $user->id)->where('id', $id, '<')->orderBy('id', 'DESC')->get(T_VIDEOS, 40);
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
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'top') {
	    $ids = array();
	    if (!empty($_POST['ids'])) {
	    	foreach ($_POST['ids'] as $key => $one_id) {
	    		$ids[] = PT_Secure($one_id);
	    	}
	    }
	    $videos = $db->where('views', $views, '<=')->where('id', $ids, 'NOT IN')->orderBy('views', 'DESC')->get(T_VIDEOS, 100);
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('videos/list', array(
		            'ID' => $video->id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'latest') {
		$videos = $db->where('id', $id, '<')->orderBy('id', 'DESC')->get(T_VIDEOS, 100);
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('videos/list', array(
		            'ID' => $video->id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'trending') {
		$videos = $db->where('time', time() - 172800, '>')->where('id', $id, '>')->orderBy('views', 'DESC')->get(T_VIDEOS, 40);
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('videos/list', array(
		            'ID' => $video->id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'history') {
		$videos = array();
		$get = $db->where('user_id', $user->id)->where('id', $id, '<')->orderby('id', 'DESC')->get(T_HISTORY, 40);
		if (!empty($get)) {
		    foreach ($get as $key => $video_) {
		       $fetched_video = $db->where('id', $video_->video_id)->getOne(T_VIDEOS);
		       $fetched_video->history_id = $video_->id;
		       $videos[] = $fetched_video;
		    }
		}
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('history/list', array(
		            'ID' => $video->history_id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'saved_videos') {
		$videos = array();
		$get = $db->where('user_id', $user->id)->where('id', $id, '<')->orderby('id', 'DESC')->get(T_SAVED, 40);
		if (!empty($get)) {
		    foreach ($get as $key => $video_) {
		       $fetched_video = $db->where('id', $video_->video_id)->getOne(T_VIDEOS);
		       $fetched_video->history_id = $video_->id;
		       $videos[] = $fetched_video;
		    }
		}
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('saved/list', array(
		            'ID' => $video->history_id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'liked_videos') {
		$videos = array();
		$get = $db->where('user_id', $user->id)->where('type', 1)->where('id', $id, '<')->orderby('id', 'DESC')->get(T_DIS_LIKES, 40);
		if (!empty($get)) {
		    foreach ($get as $key => $video_) {
		       $fetched_video = $db->where('id', $video_->video_id)->getOne(T_VIDEOS);
		       $fetched_video->like_id = $video_->id;
		       $videos[] = $fetched_video;
		    }
		}
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('subscriptions/list', array(
		            'ID' => $video->like_id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'category') {
		$videos = '';
		if (!empty($_GET['c_id'])) {
			if (in_array($_GET['c_id'], array_keys($categories))) {
	            $cateogry = PT_Secure($_GET['c_id']);
	            $videos   = $db->where('category_id', $cateogry)->where('id', $id, '<')->orderBy('id', 'DESC')->get(T_VIDEOS, 40);
	        }
		}
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('videos/list', array(
		            'ID' => $video->id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'search') {
		$keyword = '';
		if (!empty($_POST['keyword'])) {
			$keyword = PT_Secure($_POST['keyword']);
		}
		if (!empty($keyword)) {
			if ($pt->config->total_videos > 1000000) {
			    $videos = $db->rawQuery("SELECT * FROM " . T_VIDEOS . " WHERE MATCH (title) AGAINST ('$keyword') AND id > $id ORDER BY id ASC LIMIT 40");
			} else {
			    $videos = $db->rawQuery("SELECT * FROM " . T_VIDEOS . " WHERE title LIKE '%$keyword%' AND id > $id ORDER BY id ASC LIMIT 40");
			}
			if (!empty($videos)) {
			    $len = count($videos);
			    foreach ($videos as $key => $video) {
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
			            'TIME' => $video->time_ago,
			            'DURATION' => $video->duration,
			            'VIEWS_NUM' => number_format($video->views),
			        ));
			    }
			    $data = array('status' => 200, 'videos' => $final);
			}
		}
	} else if ($type == 'profile_videos') {
		$videos = $db->where('user_id', $user_id)->where('id', $id, '<')->orderBy('id', 'DESC')->get(T_VIDEOS, 40);
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('videos/list', array(
		            'ID' => $video->id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	} else if ($type == 'liked_videos_profile') {
		$videos = array();
		$get = $db->where('user_id', $user_id)->where('type', 1)->where('id', $id, '<')->orderby('id', 'DESC')->get(T_DIS_LIKES, 40);
		if (!empty($get)) {
		    foreach ($get as $key => $video_) {
		       $fetched_video = $db->where('id', $video_->video_id)->getOne(T_VIDEOS);
		       $fetched_video->like_id = $video_->id;
		       $videos[] = $fetched_video;
		    }
		}
		if (!empty($videos)) {
		    $len = count($videos);
		    foreach ($videos as $key => $video) {
		        $video = PT_GetVideoByID($video, 0, 0, 0);
		        $pt->last_video = false;
		        if ($key == $len - 1) {
		            $pt->last_video = true;
		        }
		        $final .= PT_LoadPage('videos/list', array(
		            'ID' => $video->like_id,
		            'USER_DATA' => $video->owner,
		            'THUMBNAIL' => $video->thumbnail,
		            'URL' => $video->url,
		            'TITLE' => $video->title,
		            'DESC' => $video->markup_description,
		            'VIEWS' => $video->views,
		            'TIME' => $video->time_ago,
		            'DURATION' => $video->duration,
		            'VIEWS_NUM' => number_format($video->views),
		        ));
		    }
		    $data = array('status' => 200, 'videos' => $final);
		}
	}

	else if ($type == 'articles') {
		$request  = (!empty($_POST['last_id']) && is_numeric($_POST['last_id']));
		$articles = array();
		$data     = array('status' => 404);
		$posts    = "";

		if ($request === true) {
			$id              = PT_Secure($_POST['last_id']);

			if (empty($_POST['cat'])){
				$articles    = $db->where('active', '1')->where('id', $id, '<')->orderby('id', 'DESC')->get(T_POSTS, 10);
			}

			else if (!empty($_POST['cat']) && is_numeric($_POST['cat'])) {	
				$category_id = PT_Secure($_POST['cat']);
				$articles    = $db->where('active', '1')->where('id', $id, '<')->where('category', $category_id)->orderby('id', 'DESC')->get(T_POSTS, 10);
			}

			

			if (count($articles) > 0) {
				foreach ($articles as $key => $article) {
			        $posts .= PT_LoadPage('articles/list', array(
			            'ID' => $article->id,
				        'TITLE' => $article->title,
				        'DESC'  => PT_ShortText($article->description,150),
			            'VIEWS_NUM' => number_format($article->views),
				        'THUMBNAIL' => PT_GetMedia($article->image),
				        'URL' => PT_Link('articles/read/' . PT_URLSlug($article->title,$article->id)),
				        'TIME' => date('d-F-Y',$article->time),
				        'CAT' => $article->category,
			        ));
			    }

			    $data = array('status' => 200, 'posts' => $posts);
			}

		    
		}
	}

}  
