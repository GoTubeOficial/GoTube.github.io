<?php
if (IS_LOGGED == true) {
	header("Location: " . PT_Link(''));
	exit();
}

$color1 = '0095D8';
$color2 = '87ddff';

$errors   = '';
$username = '';
if (!empty($_POST)) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $errors = $error_icon . $lang->please_check_details;
    } 
    else {
        $username        = PT_Secure($_POST['username']);
        $password        = PT_Secure($_POST['password']);
        $password_hashed = sha1($password);
        $db->where("(username = ? or email = ?)", array(
            $username,
            $username
        ));

        $db->where("password", $password_hashed);
        $login = $db->getOne(T_USERS);
        
        if (!empty($login)) {
            if ($login->active == 0) {
                $errors = $error_icon . $lang->account_is_not_active . ' <a href="{{LINK resend/' . $login->email_code . '/' . $login->username . '}}">' . $lang->resend_email . '</a>';
            } 

            else {
                $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime());
                $insert_data         = array(
                    'user_id' => $login->id,
                    'session_id' => $session_id,
                    'time' => time()
                );
                $insert              = $db->insert(T_SESSIONS, $insert_data);
                $_SESSION['user_id'] = $session_id;
                setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
                $pt->loggedin = true;
                if (!empty($_GET['to'])) {
                    $site_url = $_GET['to'];
                }

                $db->where('id',$login->id)->update(T_USERS,array(
                    'ip_address' => get_ip_address()
                ));
                
                header("Location: $site_url");
                exit();
            }
        } else {
            $errors = $error_icon . $lang->invalid_username_or_password;
        }
    }
}
if (empty($_POST) && !empty($_GET['resend'])) {
    $errors = $success_icon . $lang->email_sent;
}
$pt->page        = 'login';
$pt->title       = $lang->login . ' | ' . $pt->config->title;
$pt->description = $pt->config->description;
$pt->keyword     = $pt->config->keyword;
$pt->content     = PT_LoadPage('auth/login/content', array(
    'COLOR1' => $color1,
    'COLOR2' => $color2,
    'ERRORS' => $errors,
    'USERNAME' => $username
));