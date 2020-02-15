<?php 

$pt->page = '404';
$pt->title = '404 | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword = $pt->config->keyword;
$pt->content = PT_LoadPage('404/content');