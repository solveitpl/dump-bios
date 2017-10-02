<?php
ob_start();;
if (!isset($Token)) $Token='';
if (!isset($Nick)) $Nick='użytkowniku';
?>
<html>
	<head>
		<style>	
			body{
				font-family:verdana;
				background-color: #EEE;
			}
			.mail_content{
				background-color: white;
				width: 556px;
				height:700px;
				position:absolute;
				left: 20%;
				top: 0;
                border: 0.5px solid #c9c9c9;
			}
            .logo
            {
                width: 50%;
                float: left;
                height: 100%;
            }
            .title_text
            {
                width: 43%;
                float: left;
            }
			
			.mail_title{
				background-color: black;
				vertical-align: center;
				width:100%;
				font-size: 12px;
				color: #EEE;
				text-align: center;
				text-transform: uppercase;
				font-weight: bold;
                height: 50px;
                display: block;
                border: none;
			}
			
			.mail_title div{
				position: relative;
			}
			
			.mail_text{
				width: 90%;
				left: 5%;
				background-color: #EEE;
				height: 70%;
				position: relative;
				top: 5%;
			}
			
			.hi_message{
				position: relative;
				margin-left: 5%;
				margin-right: 5%;
				top: 5%;
				font-size: 12px;
				text-align: justify;
			}
			
			.no_replay_info{
			font-size: 10px;
			color: #888;
			position: absolute;
			left: 10px;
			bottom: 3px;
			width:100%;
			}
			
			
			.myButton {
				-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
				-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
				box-shadow:inset 0px 1px 0px 0px #ffffff;
				background-color:#474747;
				-moz-border-radius:6px;
				-webkit-border-radius:6px;
				border-radius:6px;
				border:1px solid #dcdcdc;
				display:inline-block;
				cursor:pointer;
				color:white;
				font-family:Arial;
				font-size:15px;
				font-weight:bold;
				vertical-align: middle;
				padding:10px 50px;
				text-decoration:none;
				text-shadow:0px 1px 0px #ffffff;
			}
			.myButton:hover {
				background-color:#e9e9e9;
			}
			.myButton:active {
				position:relative;
				top:1px;
			}
			.hi_message
            {
                position: relative;
                margin-left:0%;
                margin-top:0%;
                font-size: 15px;
                text-align: justify;
                height: 35px;
                width: 94%;
                font-family: 'PixelMix';
                background-color: #f8f8f8;
                padding-top: 22px;
                padding-left: 6%;
                text-transform: uppercase;
            }
			.logo
            {
                font-size: 24px;
                color: white;
                padding: 10px;
                font-weight: bold;
                background-color: black;
                padding-left: 0px;
                font-size: 26px;
                
            }
            .title_text
            {
                color: white;
                text-align: left;
                font-size: 11px;
                opacity: 0.5;
                border-left: 1px solid white;
                height: 100%;
                padding-top: 18px;
                padding-left: 10px;
            }
            #figure
            {
                height: 20px;
                width: 17px;
                background-color: #6fd8d4;
                border: 1px solid white;
                display: block;
                position: absolute;
                top: 23%;
                left: 83%;
                
                
            }
            .mail_info
            {
                margin-left: 7.5%;
                margin-right: 7.5%;
                margin-top: 35px;
                margin-bottom: 76px;
                font-size: 11px;
                padding: 10px;
                background-color: #bdbdbd;
                margin-bottom: 50px;
                font-weight: bold;
            }
            .btn_box{
				width:100%;
				min-height: 16%;
				text-align: center;
			}
			
			.btn_box div{
				position: relative;
				height: 15%;
				top: 25%;
				width: 99%;
			}
		  
            #warning
            {
                border-top: 1px solid black;
                padding-top: 2%;
                opacity: 0.6;
                margin-left: 7.5%;
                margin-right: 7.5%;
                margin-top: 10px;
                margin-bottom: 55px;
                font-size: 11px;
                font-weight: bold;
            }
            #regards
            {
                font-size: 13px;
                font-weight: bold;
                position: absolute;
                right: 14%;
                color:#6fd8d4;
            }
            #no_reply
                        {
                border-top: 1px solid black;
                padding-top: 2%;
                opacity: 0.6;
                margin-left: 7.5%;
                margin-right: 7.5%;
                margin-top: 260px;
                margin-bottom: 65px;
                font-size: 11px;
                font-weight: bold;
            }
            
		</style>
	</head>
	<body>
		        <div class="mail_content">
			<div class="mail_title"><div class="logo">DUMP BIOS <span id="figure"></span></div><div class="title_text">REGISTRATION</div></div>
            <div class="hi_text">
            <div class="hi_message">HELLO <?= $Nick ?>,</div>
            </div>
            <div class="mail_info">
            THANK YOU FOR SIGN UP AT DUMP BIOS<br/>
            CLICK ON THE BUTTONS TO COMPLETE THE REGISTRATION PROCESS
				
            </div>
            
                <div class="btn_box">
					<div><a href="<?= BDIR ?>register/activate/<?= $Token ?>" class="myButton">CONTINUE</a></div>
				</div>
            
            <div id="warning">IF YOU NEED TO CONTACT US YOU CAN DO IT THROUGH THIS E-MAIL ADDRES SUPPORT@DUMPBIOS.COM</div>
            
            
            <span id="regards">REGARDS<br/> THE DUMP BIOS TEAM</span>
            
            
            <div id="no_reply"> THE MESSAGE WAS GENERATED AUTOMATICALLY, PLEASE DO NOT REPLY.</div>
            
            <!--
			<div class="mail_text">
                
				,<br>
					WE GOT A REQUEST FOR YOUR PASSWORD FROM DUMP BIOS.
					<br>TO C0NTINUE CLICK THAT BUTTON.
				</div>

				<hr>
		          <b></b>
                
			
				<div class="no_replay_info">
				
				</div>
			
			</div>
			-->
		</div>
	</body>
</html>

<?php $EMAIL_CONTENT = ob_get_clean();?>