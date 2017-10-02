<?php
$user_name = htmlspecialchars($ARG[1]);
if (!isset($ARG[2])) $ARG[2]='';

$UserData = oUser::withName($user_name);

if ($UserData==BAD_LOGIN_DATA)
{
	_die("Błędny strumień wejścia...");
	StrangeEvent("Próba dostępu do użytkownika który nie istnieje...","MEMBER");
}

switch(strtolower($ARG[2]))
{
	case 'sendmessage':
		include 'messages.php';
		break;
	default:
		

?>
<input type='hidden' id='user_name' value='<?= $UserData->UserNick() ?>'>
<div class='user_profile_container'>
	<img class='mail_icon' action="Navigate" arg="member/<?= $UserData->UserNick() ?>/SendMessage" src="<?= BDIR ?>images/MailIcon.png">
	<table class='userinfo_profile'>
	<tr >
		<td class='user_img' rowspan='3'><img alt="Avatar" src="<?= IMAGES ?>avatars/<?= $UserData->Avatar ?>"></td>
		<td><?= $UserData->UserNick() ?></td>
	</tr>
	<tr><td><?= $UserData->City() ?></td></tr>
	<tr><td><?= $UserData->Country()?></td></tr>
	
	<tr><td>Registration</td><td><?= $UserData->RegisterDate() ?></td></tr>
	<tr><td>Last visit</td><td><?= $UserData->LastVisit() ?></td></tr>
	<tr><td>Birth date</td><td><?= $UserData->BirthDay() ?></td></tr>
	<tr><td>Points</td><td>N/A</td></tr>
	</table>
	
	<div id="user_article_list" class="user_added_value">
		<div class="div_title">User article</div>
		<div class="div_content"></div>
		
	</div>
	
	
	<div id="user_file_list" class="user_added_value">
		<div class="div_title">User files</div>
		<div class="div_content"></div>
	</div>
	
	<div id="user_posts_list" class="user_added_value">
		<div class="div_title">User posts</div>
		<div class="div_content"></div>
	</div>
	

</div>
<?php }?>
