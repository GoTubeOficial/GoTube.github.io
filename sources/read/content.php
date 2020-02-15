<?php 
if (empty($_GET['id'])) {
	header('Location: ' .PT_Link('404'));
	exit();
}

$post_id = PT_GetIdFromURL($_GET['id']);
$db->where('id',$post_id);
if (!PT_IsAdmin()) {
	$db->where('active','1');
}
$article = $db->getOne(T_POSTS);

if (empty($article)) {
	header('Location: ' .PT_Link('404'));
	exit();
}

$keyword        = $article->title;
$pt->cateogries = get_object_vars($pt->categories);
$post_comments  = "";
$post_likes     = $db->where('post_id', $post_id)->where('type', 1)->getValue(T_DIS_LIKES, "count(*)");
$post_dislikes  = $db->where('post_id', $post_id)->where('type', 2)->getValue(T_DIS_LIKES, "count(*)");
$liked          = '';
$disliked       = '';


if (IS_LOGGED === true) {
	$u_like     = $db->where('post_id', $post_id)->where('user_id', $user->id)->where('type', 1)->getValue(T_DIS_LIKES, "count(*)");
	$liked      = ($u_like > 0) ? 'active' : '';	

	$u_dislike  = $db->where('post_id', $post_id)->where('user_id', $user->id)->where('type', 2)->getValue(T_DIS_LIKES, "count(*)");
	$disliked   = ($u_dislike > 0) ? 'active' : '';
}

//Get more post related articless
$db->where('id',$post_id)->where('active','1')->update(T_POSTS,array('views' => ($article->views += 1)));
$sql            = "(`title` LIKE '%$keyword%' OR `description` LIKE '%$keyword%' OR `tags` LIKE '%$keyword%') AND `id` <> '{$post_id}' ";
$db->where($sql);
$related        = $db->orderBy('id', 'DESC')->get(T_POSTS,7);
if (empty($related)) {
	$related    = $db->where('id',$post_id,'<>')->orderBy('views', 'DESC')->get(T_POSTS,7);
}

$related_list   = "";
$videos_list    = "";
foreach ($related as $post) {
	$slug       = PT_URLSlug($post->title,$post->id);
	$category   = $post->category;
	$related_list .= PT_LoadPage('articles/related-list', array(
	    'TITLE' => $post->title,
	    'IMAGE' => PT_GetMedia($post->image),
	    'VIEWS' => number_format($post->views),
	    'CATEGORY_NAME' => $categories[$category],
	    'URL' => PT_Link("articles/read/$slug"),
	    'CATEGORY_URL' => PT_Link("articles/category/$category"),
	));
}

//Get post related videos
$sql            = "(`title` LIKE '%$keyword%' OR `description` LIKE '%$keyword%' OR `tags` LIKE '%$keyword%')";
$videos         = $db->where($sql)->orderBy('id', 'DESC')->get(T_VIDEOS,7);

if (empty($videos)){
	$videos     = $db->orderBy('views', 'DESC')->get(T_VIDEOS,7);
}

foreach ($videos as $video) {
	$video_id   = $video->video_id;
	$category   = $video->category_id;
	$videos_list .= PT_LoadPage('articles/videos', array(
	    'TITLE' => $video->title,
	    'IMAGE' => PT_GetMedia($video->thumbnail),
	    'VIEWS' => number_format($video->views),
	    'CATEGORY_NAME' => $categories[$category],
	    'URL' => PT_Link("watch/$video_id"),
	    'CATEGORY_URL' => PT_Link("videos/category/$category"),
	));
}


//Get post related comments

$comments_limit     = $pt->config->comments_default_num;

if (!empty($_GET['cl']) || !empty($_GET['rl'])) {
    $comments_limit = null;
}

$get_comments   = $db->where('post_id',$post_id)->orderBy('id','desc')->get(T_COMMENTS,$comments_limit);
$comments_count = count($get_comments);
$pt->count_cmt  = $comments_count;
foreach ($get_comments as $get_comment) {
	$pt->is_comment_owner  = false;
	$is_comment_liked      = '';
	$is_comment_disliked   = '';     
    $replies               = "";
    $comment_replies       = $db->where('comment_id', $get_comment->id)->get(T_COMM_REPLIES);
    $is_liked_comment      = '';
    $is_comment_disliked   = '';
    $comment_user_data     = PT_UserData($get_comment->user_id);

    foreach ($comment_replies as $reply) {
        $pt->is_reply_owner = false;
        $pt->is_ro_verified = false;
        $reply_user_data    = PT_UserData($reply->user_id);
        $is_liked_reply     = '';
        $is_disliked_reply  = '';
        if (IS_LOGGED == true) {
            $is_reply_owner = $db->where('id', $reply->id)->where('user_id', $user->id)->getValue(T_COMM_REPLIES, 'count(*)');
            if ($is_reply_owner) {
                $pt->is_reply_owner = true;
            }

            //Check is this reply  voted by logged-in user
            $db->where('reply_id', $reply->id);
            $db->where('user_id', $user->id);
            $db->where('type', 1);
            $is_liked_reply    = ($db->getValue(T_COMMENTS_LIKES, 'count(*)') > 0) ? 'active' : '';

            $db->where('reply_id', $reply->id);
            $db->where('user_id', $user->id);
            $db->where('type', 2);
            $is_disliked_reply = ($db->getValue(T_COMMENTS_LIKES, 'count(*)') > 0) ? 'active' : '';
        }
        
        if ($reply_user_data->verified == 1) {
            $pt->is_ro_verified = true;
        }

        //Get related to reply likes
        $db->where('reply_id', $reply->id);
        $db->where('type', 1);
        $reply_likes    = $db->getValue(T_COMMENTS_LIKES, 'count(*)');

        $db->where('reply_id', $reply->id);
        $db->where('type', 2);
        $reply_dislikes = $db->getValue(T_COMMENTS_LIKES, 'count(*)');

        $replies    .= PT_LoadPage('articles/includes/replies', array(
            'ID' => $reply->id,
            'TEXT' => PT_Markup($reply->text),
            'TIME' => PT_Time_Elapsed_String($reply->time),
            'USER_DATA' => $reply_user_data,
            'COMM_ID' => $get_comment->id,
            'LIKES'  => $reply_likes,
            'DIS_LIKES' => $reply_dislikes,
            'LIKED' => $is_liked_reply,
            'DIS_LIKED' => $is_disliked_reply,
        ));
    }


    //Check is user PRO or verified
    $pt->is_verified      = ($comment_user_data->verified == 1) ? true : false;
    $comment_likes        = $get_comment->likes;
    $comment_dislikes     = $get_comment->dis_likes;;  


    if (IS_LOGGED && $user->id == $get_comment->user_id) {
        $pt->is_comment_owner = true;    	
    }

    if (IS_LOGGED === true) {
    	//Check is comment voted by logged-in user
		$db->where('user_id', $user->id);
	    $db->where('comment_id', $get_comment->id);
	    $db->where('type', 1);
	    $is_comment_liked     = ($db->getValue(T_COMMENTS_LIKES, 'count(*)') > 0) ? 'active' : '';
	    

	    $db->where('user_id', $user->id);
	    $db->where('comment_id', $get_comment->id);
	    $db->where('type', 2);
    	$is_comment_disliked  = ($db->getValue(T_COMMENTS_LIKES, 'count(*)') > 0) ? 'active' : '';
    }

    

	$post_comments .= PT_LoadPage('articles/includes/comments', array(
        'ID'   => $get_comment->id,
        'TEXT' => PT_Markup($get_comment->text),
        'TIME' => PT_Time_Elapsed_String($get_comment->time),
        'USER_DATA' => $comment_user_data,
        'LIKES' => $comment_likes,
        'DIS_LIKES' => $comment_dislikes,
        'LIKED' => $is_comment_liked,
        'DIS_LIKED' =>$is_comment_disliked,
        'COMM_REPLIES' => $replies,
        'POST_ID' => $article->id
    ));
}

if (empty($comments_count)) {
	$post_comments = '<div class="text-center no-comments-found">' . $lang->no_comments_found . '</div>';
}


$pt->page        = 'read';
$pt->title       =  $article->title . ' | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword     = @$pt->config->keyword;
$pt->content     = PT_LoadPage('articles/read', array(
    'ID' => $article->id,
    'TITLE' => $article->title,
    'DESC' => $article->description,
    'IMAGE' => PT_GetMedia($article->image),
    'TEXT' => PT_Decode($article->text),
    'TIME' => date('F/m/Y h:i',$article->time),
    'VIEWS' => number_format($article->views),
    'SHARED' => number_format($article->shared),
    'CATEGORY_NAME' => $categories[$article->category],
    'CATEGORY_ID' => $article->category,
    'RELATED_ARTICLES' => $related_list,
    'RELATED_VIDEOS' => $videos_list,
    'COMMENTS' => $post_comments,
    'COMMENTS_COUNT' => $comments_count,
    'LIKES' => $post_likes,
    'DIS_LIKES' => $post_dislikes,
    'LIKED' => $liked,
    'DIS_LIKED' => $disliked,
    'POST_ENCODED_URL' => urlencode(PT_Link('articles/read/' . PT_URLSlug($article->title,$article->id))),
));
