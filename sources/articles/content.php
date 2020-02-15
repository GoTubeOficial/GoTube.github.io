<?php
$pt_cats      = array_keys(get_object_vars($pt->categories));
$html_posts   = '';
$html_p_posts = '';
$category     = 0;
$query        = false;

if (!empty($_POST['q'])) {
	$keyword = PT_Secure($_POST['q']);
	$sub_sql = '';
	$query   = true;
	
	if (!empty($_GET['category_id']) && is_numeric($_GET['category_id']) && in_array($_GET['category_id'],$pt_cats)) {
		$category = $_GET['category_id'];
		$sub_sql  = " AND `category` = '$category'";
	}

	$sql     = "(`title` LIKE '%$keyword%' OR `description` LIKE '%$keyword%' OR `tags` LIKE '%$keyword%') {$sub_sql}";
	$db->where($sql);
	$posts   = $db->orderBy('id', 'DESC')->get(T_POSTS,10);
}

else{

	if (!empty($_GET['category_id']) && is_numeric($_GET['category_id']) && in_array($_GET['category_id'],$pt_cats)) {
		$db->where('category',$_GET['category_id']);
		$category = $_GET['category_id'];
	}
	$posts   = $db->where('active', '1')->orderBy('id', 'DESC')->get(T_POSTS, 20);
}

$popular_posts = $db->where('active', '1')->orderBy('views', 'DESC')->get(T_POSTS, 7);


$pt->category = $category;

if (!empty($posts)) {
    foreach ($posts as $key => $post) {
        $html_posts .= PT_LoadPage('articles/list', array(
            'ID' => $post->id,
	        'TITLE' => $post->title,
	        'DESC'  => PT_ShortText($post->description,150),
            'VIEWS_NUM' => number_format($post->views),
	        'THUMBNAIL' => PT_GetMedia($post->image),
	        'CAT' => ($post->category),
	        'URL' => PT_Link('articles/read/' . PT_URLSlug($post->title,$post->id)),
	        'TIME' => date('d-F-Y',$post->time),
        ));
    }
}

foreach ($popular_posts as $key => $post) {
    $html_p_posts .= PT_LoadPage('articles/popular', array(
        'TITLE' => $post->title,
        'THUMBNAIL' => PT_GetMedia($post->image),
        'URL' => PT_Link('articles/read/' . PT_URLSlug($post->title,$post->id)),
    ));
}

if ($query && empty($html_posts)) {
	$html_posts = PT_LoadPage('articles/404',array('QUERY' => $keyword));
}

else if(empty($html_posts)){
	$html_posts = '<div class="text-center no-content-found"><p class="no-posts-found">'.$lang->no_post_found.'</p></div>';
}



$pt->title       = $lang->articles . ' | ' . $pt->config->title;
$pt->page        = "articles";
$pt->description = $pt->config->description;
$pt->posts_count = count($posts);
$pt->keyword     = @$pt->config->keyword;
$pt->content     = PT_LoadPage('articles/content', array(
    'POSTS'         => $html_posts,
    'POPULAR_POSTS' => $html_p_posts,
    'CATEGORY'      => $category,
));
