<?php

/*
 * Głosowanie na artykuł
 */
if (isset($_POST['ARTICLE_VOTE'])){

	if (!IsAuth())
		die(json_encode(array("result"=>"ACCESS_DENIED")));

		$VerificatedPoints = GetSettings("ART_VERIFICATED_POINTS");
		$ToTrashPoints= GetSettings("ART_TO_TRASH_VOICES");

		$article = intval($_POST['ARTICLE_VOTE']);
		$Point = intval($_POST['ARTICLE_POINT']);
		$result = array("result"=>"UNKNOWN_ERROR");
		$sql = DBquery("SELECT ArticlesPoints.*, ArticlesPoints.ID AS VoteID, Articles.*, Users.Nick AS AuthorNick FROM ArticlesPoints
					LEFT JOIN Articles ON Articles.ID = ArticlesPoints.ArticleID
					LEFT JOIN Users ON Articles.AuthorID = Users.ID
					WHERE ArticlesPoints.ArticleID=".$article." AND UserID=".$User->ID());

		if ($sql==false)
			die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"zap #1")));

		$vote = dbarray($sql);
		// jeśli możemy głosować
		if (empty($vote))
		{
			$sql = DBquery("INSERT INTO ArticlesPoints(`ID`, `ArticleID`, `UserID`, `Points`, `EntryDate`) VALUES(NULL, $article, ".$User->ID().", $Point, NOW())");
			if ($sql==false)
				$result['result'] = 'INTERNAL_ERROR';
				else{
					$result['result'] = 'VOTED_SUCCESS';
				}
		}
		else
		{
			$sql = DBquery("UPDATE ArticlesPoints SET Points=$Point WHERE ID=".$vote['VoteID']);
			if ($sql==false)
			{
				$result['result'] = 'INTERNAL_ERROR';
				die(json_encode($result));
			}

			$result['result'] = 'VOTED_SUCCESS';
		}
		$CurrVote = DBarray(DBquery("SELECT Articles.Title, a.*, b.* FROM Articles
	LEFT JOIN (SELECT ArticleID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM ArticlesPoints WHERE Points>0 GROUP BY ArticleID) as a ON Articles.ID = a.ArticleID
	LEFT JOIN (SELECT ArticleID, SUM(Points) AS PointsBad FROM ArticlesPoints WHERE Points<0 GROUP BY ArticleID) as b ON Articles.ID=b.ArticleID
			 	 WHERE ID = ".$article));


		$result['PointsGood'] = $CurrVote['PointsGood']*1;
		$result['PointsBad'] = abs($CurrVote['PointsBad']*1);
		$TotalPoints = $CurrVote['PointsGood'] + $CurrVote['PointsBad'];

		if ($result['PointsGood']>=$VerificatedPoints){ // jeśli ilość punktów przekroczyła próg weryfikacji
			$result['action'] = "VERIFICATED";
			DBquery("UPDATE Articles SET Status=2 WHERE ID=$article");
			$UploaderUser = oUser::withName($vote['AuthorNick']);
			$UploaderUser->SendNotify("Twój artykuł został zaakceptowany przez użytkowników. Gratulacje !", "Artykuły", "articles/view/", $vote['link'], "ICON_GOOD");
				
		}

		if ($TotalPoints<=$ToTrashPoints){	// jeśli ilośc punktów spadła poniżaj zadanego progu - do kosza
			$result['action'] = "TO_TRASH";
			DBquery("UPDATE Articles SET Status=-1 WHERE ID=$article");
			$UploaderUser = oUser::withName($vote['AuthorNick']);
			$UploaderUser->SendNotify("Niestety, głosami użytkowniów Twój artykuł został przeznaczony do usunięcia.", "Artykuły", "articles/view/", $vote['link'], "ICON_BAD");
				
		}


		die(json_encode($result));
}

elseif (isset($_POST['WHO_VOTED'])){
	$art_id = intval($_POST['WHO_VOTED']);
	if ($art_id==0) {
		StrangeEvent("Void article WHO_VOTED query", "ARTICLES", array($art_id, $_POST, $_SESSION, $_SERVER));
		die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"BAD DATA")));
	}
	
	$sql = DBquery("SELECT Nick, ArticlesPoints.Points FROM ArticlesPoints INNER JOIN Users on UserID=Users.ID WHERE ArticleID=$art_id");
	
	if ($sql==false) die(json_encode(array("result"=>"INTERNAL_ERROR", "msg"=>"INTERNAL_ERROR")));
	
	$votes = array();
	
	while($row = DBarray($sql))
		array_push($votes, $row);
	
	die(json_encode(array("result"=>"SUCCESS", 'voters'=> count($votes), 'VOTES'=>$votes)));
	
	
}


// jeśli nie odnaleziono żadnego zapytania zwracamy NO-DATA
die(json_encode(array("result"=>"NO-DATA")));
?>