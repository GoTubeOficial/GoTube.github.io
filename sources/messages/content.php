<?php 
if (IS_LOGGED == false) {
	header("Location: " . PT_Link('login'));
	exit();
}

$pt->page = 'messages';
$pt->title = 'Messages | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword = $pt->config->keyword;

$chat_id = 0;
$chat_user = array();

if (!empty($_GET['id'])) {
	$get_user_id = $db->where('username', PT_Secure($_GET['id']))->getValue(T_USERS, 'id');
	if (!empty($get_user_id)) {
		$chat_user = PT_UserData($get_user_id);
		if ($chat_user->id != $pt->user->id) {
			$chat_id = $chat_user->id;
		} else {
			$chat_user = array();
		}
	} else {
		$chat_user = array();
	}
}

if (empty($chat_id)) {
	$html = PT_LoadPage("messages/ajax/no-messages");
} else {
	$messages_html = PT_GetMessages($chat_id, array('chat_user' => $chat_user, 'return_method' => 'html'));
	if (!empty($messages_html)) {
		$html = PT_LoadPage("messages/{$pt->config->server}/messages", array('MESSAGES' => $messages_html));
	} else {
		$html = PT_LoadPage("messages/ajax/no-messages-users");
	}
}

$users_html = PT_GetMessagesUserList(array('return_method' => 'html'));
if (empty($users_html)) {
	$users_html = '<p class="text-center">No users found</p>';
}

$pt->chat_id = $chat_id;
$pt->chat_user = $chat_user;

$sidebar = PT_LoadPage('messages/sidebar', array('USERS' => $users_html));
$pt->content = PT_LoadPage("messages/{$pt->config->server}/content", array(
	'SIDEBAR' => $sidebar,
	'HTML' => $html
));