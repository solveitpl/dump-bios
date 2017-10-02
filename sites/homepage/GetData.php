<?php
/* 
 * Plik zawiera funkcje odpowiadające za pobranie informacji
 */

function GetLastArticles(){
	require_once SITES.'article/header.php';
	$sql = DBquery("SELECT Articles.*, Users.Nick AS AuthorNick FROM Articles INNER JOIN Users ON Articles.AuthorID=Users.ID WHERE Articles.Status = ".ARTICLE_ACCEPTED." OR Articles.Status = ".ARTICLE_VERIFIED." ORDER BY AddDateTime DESC LIMIT 10");
	$content='';
	
	while ($row=DBarray($sql)){
		$content .= '
					<a href="'.BDIR.'Article/view/'.$row['link'].'">
						<li>
							<img src="'.IMAGES.'edit_icon.png" >
      						<h3>'.MakeItShort($row['Title']).'</h3>
      						<p>'.MakeItShort(strip_tags(htmlspecialchars_decode($row['Content'])),50).'</p>
      					</li>
      				</a>
				';
	}
	
	return  $content;
	
}

function GetLastFiles(){
	$sql = DBquery("SELECT UploadedFile.*, Users.Nick AS UploaderNick
			FROM UploadedFile 
			INNER JOIN Users ON UploadedFile.UploaderID=Users.ID 
			WHERE UploadedFile.Status > 0
			ORDER BY FileUploaded DESC LIMIT 10");
	$content='';

	while ($row=DBarray($sql)){
		$content .= '
					<a href="'.BDIR.'downloads/item/'.$row['ID'].'">
						<li>
							'.GetFileIcon($row['RealFileName'], TRUE).'
      						<h3>'.MakeItShort($row['FileDesc']).'</h3>
      						<p>
      								'.MakeItShort($row['FileDescExt'],50).'
      						</p>
      					</li>
      				</a>
				';
	}

	return  $content;

}

function GetLastPosts(){
	
	$sql = DBquery("SELECT BrowserPosts.*, a.PointsGood, b.PointsBad, Users.Nick AS UploaderNick, t_files.*
		FROM `BrowserPosts`
		LEFT JOIN Users ON Users.ID = BrowserPosts.UserID
		LEFT JOIN (SELECT PostID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM BrowserPoints WHERE Points>0 GROUP BY PostID) as a ON BrowserPosts.ID=a.PostID
		LEFT JOIN (SELECT PostID, SUM(Points) AS PointsBad FROM BrowserPoints WHERE Points<0 GROUP BY PostID) as b ON BrowserPosts.ID=b.PostID
		LEFT JOIN (SELECT COUNT(*) AS FilesQuan, PostID FROM BrowserFiles GROUP BY PostID) as t_files ON t_files.PostID = BrowserPosts.ID
		WHERE  BrowserPosts.status>0
		ORDER BY SendTime DESC
		LIMIT 0,10
		");
	
		$content='';
	
		while ($row=DBarray($sql)){
	
			if (empty($row['GPoints'])) $row['GPoints']=0;
			if (empty($row['BPoints'])) $row['BPoints']=0;
	
			$content .= '
					<a href="'.BDIR.'browser/GoTo/'.$row['ID'].'">
						<li>
							'.GetFileIcon("sd.sd", TRUE).'
      						<div class="file_info">
							<h3>'.MakeItShort($row['Title']).'</h3>
	      						<p>
	      							'.$row['FilesQuan'].' file(s)
	      						</p>
      						</div>
	
	      					
	
      						<div class="file_votes">
								<div class="vote" style="float: left;"><img src="'.IMAGES.'point_up.png" ><span>'.$row['GPoints'].'</span></div>
								<div class="vote" style="float: right;"><img src="'.IMAGES.'point_down.png"><span>'.abs($row['BPoints']).'</span></div>
							</div>
	
      					</li>
      				</a>
				';
		}
	
		return  $content;
	
	}




function GetPopularFiles(){
	$sql = DBquery("SELECT UploadedFile.*, Users.Nick AS UploaderNick, BadPoints.Points AS BPoints, GoodPoints.Points AS GPoints
			FROM UploadedFile 
			INNER JOIN Users ON UploadedFile.UploaderID=Users.ID 
            LEFT JOIN (SELECT SUM(Points) AS Points, FileID FROM FilesPoints WHERE Points>0) AS GoodPoints ON GoodPoints.FileID = UploadedFile.ID
            LEFT JOIN (SELECT SUM(Points) AS Points, FileID FROM FilesPoints WHERE Points<0) AS BadPoints ON BadPoints.FileID = UploadedFile.ID
			WHERE UploadedFile.Status > 0 
			ORDER BY DownloadCount DESC LIMIT 10");
	$content='';

	while ($row=DBarray($sql)){
		if (empty($row['GPoints'])) $row['GPoints']=0;
		if (empty($row['BPoints'])) $row['BPoints']=0;
		
		$content .= '
					<a href="'.BDIR.'downloads/item/'.$row['ID'].'">
						<li>
							'.GetFileIcon($row['RealFileName'], TRUE).'
      						<div class="file_info">
							<h3>'.MakeItShort($row['FileDesc']).'</h3>
	      						<p>
	      							'.MakeItShort($row['FileDescExt']).'	
	      						</p>
      						</div>
	      					
	      					<div class="file_votes" style="width:10%;">
      							<div class="vote" style="float: left;">
      								<img src="'.IMAGES.'downloaded.png" >
      								<span>'.$row['DownloadCount'].'</span>
      							</div>				
      						</div>
      						
      						<div class="file_votes">
								<div class="vote" style="float: left;"><img src="'.IMAGES.'point_up.png" ><span>'.$row['GPoints'].'</span></div>
								<div class="vote" style="float: right;"><img src="'.IMAGES.'point_down.png"><span>'.abs($row['BPoints']).'</span></div>
							</div>
      										
      					</li>
      				</a>
				';
	}

	return  $content;

}
	
/*
      										
      						<div class="file_votes" style="width:10%;">
      							<div class="vote" style="float: left;">
      								<img src="'.IMAGES.'downloaded.png" >
      								<span>'.$row['DownloadCount'].'</span>
      							</div>				
      						</div>
      									
      						<div class="file_votes">
								<div class="vote" style="float: left;"><img src="'.IMAGES.'point_up.png" ><span>'.$row['GPoints'].'</span></div>
								<div class="vote" style="float: right;"><img src="'.IMAGES.'point_down.png"><span>'.abs($row['BPoints']).'</span></div>
							</div>

*/
?>
