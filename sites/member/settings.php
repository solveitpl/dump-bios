<?php
// jeśli użytkownik nie zalogowany - błąd
if (!IsLogin()) {
    _die("Błędnt strumień wejścia");
    StrangeEvent('Nie zalogowany użytkownik otworzył ustawienia konta', 'MEMBER_SETTINGS', $User);
}

?>

<input type="hidden" value="<?= Encrypt(time(NULL)) ?>" id="marker">
<input type='hidden' id='user_name' value='<?= $User->UserNick() ?>'>

<div class="user-settings">
    <p>SETTINGS</p>

</div>


<div class='user_profile_container'>

    <form action="#" class="dropzone">

        <div id="filePreview" class="dz-default dz-message">
            <img alt="Avatar" id="user_avatar" width="80px" height="80px"
                 src="<?= IMAGES ?>avatars/<?= $User->Avatar ?>">

            <div id="user_img_prg"></div>
            <input type="button" id="pickAvatar" value="set avatar">

        </div>

    </form>

    </form>

    <table class='userinfo_profile' style="text-align:left">
        <tr style="border-bottom: none;">
            <td id="UserNick"><span class="nick"><?= $User->UserNick() ?></span></td>
            <td><img class='mail_icon' action="Navigate" arg="member/<?= $User->UserNick() ?>/SendMessage"
                     src="<?= BDIR ?>images/mailcon.svg"></td>
        </tr>
        <tr>
            <td field='city'><?= $User->City() ?><img src='<?= BDIR ?>images/edit.svg' class='edit_field'></td>
        </tr>
        <tr>
            <td field='country'><?= $User->Country() ?><img src='<?= BDIR ?>images/edit.svg' class='edit_field'></td>
        </tr>
        <tr>
            <td>Birthday</td>
            <td field='birthday'><?= $User->BirthDay() ?><img src='<?= BDIR ?>images/edit.svg' class='edit_field date'>
            </td>
        </tr>
        <tr>
            <td>Register Date</td>
            <td><?= $User->RegisterDate() ?></td>
        </tr>
        <tr>
            <td>Last Visit</td>
            <td><?= $User->LastVisit() ?></td>
        </tr>
        <tr>
            <td>Points</td>
            <td style="color:#6fd8d4;padding-left: 62%;"><?= $User->Points->TotalPoints() ?></td>
        </tr>
        <tr style="border-bottom: none;">
            <td style="color: #908f8f;">Newsletter</td>
            <td><input type="checkbox" id='want_newsletter' <?= ($User->Newsletter() == 1 ? 'checked' : '') ?>> <img
                        id="checkbox_loading" src="<?= BDIR ?>images/loading2.gif"></td>
        </tr>

    </table>

    <div class="avatar-block">
        <div class="avatar-header"><span>SELECT AVATAR</span><span>X</span></div>
        <?php
        $dir = "images/avatars";

        if (is_dir($dir)) {
            $i = 0;
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $i++;
                    if (($file != '.') && ($file != '..'))
                        echo '<img width="50" pointer="' . Encrypt($file . "|" . (time(NULL) + $i)) . '" class="lib_avatar" src=' . BDIR . 'images/avatars/' . $file . '>';

                }
                closedir($dh);
            }
        }
        ?>
    </div>

</div>


<script>
    /*

    var myDropzone = new Dropzone(".dropzone", { url: "<?= BDIR ?>uploadFile/userIMG"});
myDropzone.autoDiscover = false;
myDropzone.options.myAwesomeDropzone = false;
myDropzone.options.previewsContainer = false;

myDropzone.on("sending", function(file) {

});

myDropzone.on("addedfile", function(file) {
	$('.dz-default.dz-message').hide();
	$('#user_img_prg').fadeIn();
});

myDropzone.on("thumbnail", function(file,dataUrl) {
	
	$('#user_avatar').fadeOut(400).delay(1000).remove();
	$('<img alt="Avatar" style="display:none" id="user_avatar" src="'+dataUrl+'">').insertBefore('#user_img_prg');
	$('#user_avatar').fadeIn();
	
//	$('#user_avatar').removeAttr("src").attr('arc',dataUrl);
});

myDropzone.on("uploadprogress", function(file,progress, bytesSent) {
	$('#user_img_prg').progressbar({value: progress});
//	$('#user_avatar').removeAttr("src").attr('arc',dataUrl);
});

myDropzone.on("complete", function(file) {
	$('.dz-default.dz-message').fadeIn();
	$('#user_img_prg').hide();
	$('#user_img_prg').progressbar({value: 0});
	
	});


*/
</script>
