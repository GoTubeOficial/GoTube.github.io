<?php
if (empty($_GET['type']) || !isset($_GET['type'])) {
	header("Location: " . PT_Link(''));
	exit();
}
$pages = array('terms','privacy-policy','about-us');
if (!in_array($_GET['type'], $pages)) {
	header("Location: " . PT_Link(''));
	exit();
}
$pt->terms = PT_GetTerms();

$pt->description  = $pt->config->description;
$pt->keyword   = $pt->config->keyword;
$pt->page        = 'terms';
$pt->title       = '';
$type = PT_Secure($_GET['type']);

if ($type == 'terms') {
	$pt->title  = 'Terms Of Use';
} else if ($type == 'about-us') {
    $pt->title  = 'About Us';
} else if ($type == 'privacy-policy') {
    $pt->title  = 'Privacy Policy';
}

$page = 'terms/' . $type;

$pt->title = $pt->config->name . ' | ' . $pt->title;
$pt->content  = PT_LoadPage($page);