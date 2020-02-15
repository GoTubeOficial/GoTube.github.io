<?php 
$pt->title       = 'Contact Us | ' . $pt->config->title;
$pt->page        = "contact_us";
$pt->description = $pt->config->description;
$pt->keyword     = @$pt->config->keyword;
$pt->content     = PT_LoadPage('contact/content');