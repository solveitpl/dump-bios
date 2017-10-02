<?php


// Wyciągniecie z bazdy danych o artykule
$article_name = $ARG[2];
$article = oArticle::withLink($article_name);


if ($article === NULL) _die("Błąd wewnętrzny");

// ilość punktów artykułu
// sprawdzenie czy użytkownik głosował i na co głosował
$article->LoadVoteInfo();

// Rozwiniecie menu bocznego w zależności od otwartego artykułu
//FindExpandTree($article->GetCategory());


if ($article->GetCategory() > 0) {
    $back_link = FindSelectTreeByID($article->GetCategory(), 'select_menu', TRUE);
    $back_link = BDIR . "Article/" . $article->GetSubCategory() . "/" . $back_link . "item/" . $article->GetLink();
    FindCatTree($article->GetCategory(), 'PreparedMenu');

} else
    $back_link = BDIR . "Article/item/" . $article->GetLink();


$StatusStr = '';
$ArticleStyle = '';
if (!$article->GetStatus(ARTICLE_ACCEPTED) && array_key_exists($article->GetStatus(), $STATUS_LABEL)) $StatusStr = "Status: " . $STATUS_LABEL[$article->GetStatus()];
if ($article->GetStatus(ARTICLE_REJECTED) || $article->GetStatus(ARTICLE_NEW)) $ArticleStyle .= ' not_for_public';

?>
    <script> var UserVote =<?= $article->UserVoted ?>;
        $('#_ModulName').val('<?= $article->GetSubCategory() ?>');</script>
    <input type='hidden' value='<?= $article->GetID() ?>' id='article_id'>
    <div class="article_view_site">
        <div class="article_view_header">
            <div class="article_view_title"><?= strip_tags($article->GetTitle()) ?></div>
        </div>
        <div class="article_view_content">
            <div class='article_voting'>
                <div>Vote article:</div>
                <div class='vote'>
                    <div point='1'>
                        <i style="color: #6fd8d4" class="material-icons">check</i>
                    </div>
                    <div><?= $article->GetGoodPoints() ?></div>
                </div>
                <div class='vote'>
                    <div>
                        <i style="color: #d45b3b" class="material-icons">close</i>
                    </div>

                    <div><?= abs($article->GetBadPoints()) ?></div>
                </div>
            </div>


            <?= htmlspecialchars_decode($article->GetContent()) ?></div>

        <div class="article_info">
            Author: <a href='<?= BDIR ?>member/<?= $article->GetAuthorNick() ?>'><?= $article->GetAuthorNick() ?></a>,
            Date <?= $article->GetAddDate() ?>
            <div class="view_art_status<?= $ArticleStyle ?>"><?= $StatusStr ?></div>

        </div>

        <div class="article_view_fotter">
            <?php if ($article->UserCanEdit()) { ?>
                <div class='redactor_tools'>
                    <?php if (IsAdmin()) { ?>
                        <form action="">
                            <select id="ArticleStatus" marker="<?= Encrypt($article->GetID() . "|" . time(NULL)) ?>">
                                <option value="<?= ARTICLE_REJECTED ?>" <?= $article->GetStatus(ARTICLE_REJECTED) ? 'SELECTED' : '' ?>>
                                    REJECTED
                                </option>
                                <option value="<?= ARTICLE_NEW ?>" <?= $article->GetStatus(ARTICLE_NEW) ? 'SELECTED' : '' ?>>
                                    NEW
                                </option>
                                <option value="<?= ARTICLE_ACCEPTED ?>" <?= $article->GetStatus(ARTICLE_ACCEPTED) ? 'SELECTED' : '' ?>>
                                    ACCEPTED
                                </option>
                                <option value="<?= ARTICLE_VERIFIED ?>" <?= $article->GetStatus(ARTICLE_VERIFIED) ? 'SELECTED' : '' ?>>
                                    VERIFIED
                                </option>
                            </select>
                            <img id="change_status_img" src="<?= IMAGES ?>loading2.gif">
                        </form>
                    <?php } ?>

                    <input type="button" action='Navigate' arg='<?= $ARG[0] . '/edit/' . $article->GetLink() ?>'
                           name="EditArt" Value="Edit">
                    <?php if (IsAdmin()) { ?>
                        <form action="" method="POST">
                            <input type="hidden" name="marker"
                                   value="<?= Encrypt($article->GetID() . '|' . time(NULL)) ?>">
                            <input type="submit" name="DelArt" Value="Delete">
                        </form>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

    </div>


<?php
/*
 * KLIENT ZREZYGNOWAŁ Z KOMENTARZY
$Comments = new oComments(ARTICLE, $article->GetID());
if (IsLogin()){
	// tworzenie klasy z komenarzami
	$Comments->PropagatePOST($_POST);
	?>
	<input type='button' id='ShowCommentPanel' value="Dodaj komentarz">
	<div class='addComent'><?= $Comments->InputPanel(); ?></div>
	<?php
}
$Comments->ShowComments();
*/
?>