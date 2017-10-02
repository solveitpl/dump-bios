<?php
// Gdyby coś ktoś kombinował....
if (!isset($_POST['TermsAgree']))
	_die("You must accept terms", "REGISTER");	


$month = array(1=>'January', 
		2=>'February', 3=>'March', 
		4=>'April', 5=>'May', 
		6=>'June', 7=>'July', 
		8=>'August', 9=>'September',
		10=>'October', 11=>'November',
		12=>'December');

$days = '';
for ($i=1;$i<32;$i++)
	$days .= "<option>$i</option>";

$months = '';
for ($i=1;$i<13;$i++)
	$months .= "<option value='$i'>".$month[$i]."</option>";

$years = '';
for ($i=2010;$i>1960;$i--)
	$years .= "<option>$i</option>";
	
	
?>
<div class="banerregistration">REGISTRY</div>
<form method="post" id="register_form" action="<?= BDIR.$ARG[0].'/Check'?>">
<div class="register_form">
	<div id="Email_row"  class="section">
        <div class="email-header">EMAIL ADRESS:</div>
		<input type="email" name="email" placeholder="EMAIL ADRESS" id="email" VALIDITY="BAD">
        <span id="EmailMsg" class="RegisterSideInputMsg"></span>

	</div>
   
	
	<div id="password_row" class="section">
        <div class="password-header">PASSWORD:</div>
		<input type="text" name="password" placeholder="PASSWORD" id="password" VALIDITY="BAD"><br>
        <div class="retype-header">PASSWORD RETYPE:</div>
		<input type="text" name="password_retype" placeholder="PASSWORD RETYPE" id="password_retype" VALIDITY="BAD">
		<span id="PassMsg" class="RegisterSideInputMsg"></span>
	</div>
 
	<div id="user_info" class="section">
        <div class="nick-header">NICK:</div>
		<table class="other_info_table">

		<tr><td width="503" style="position: relative"><input VALIDITY="BAD" type="text" name="Nick" placeholder="NICK" id="Nick"><span align="left" width="45%" id="NickMsg"></span></td></tr>

		<tr>

            <td>
                <div class="city-header">CITY:</div>
                <input type="text" name="City" placeholder="CITY" id="City">
            </td>
            <td id="CitykMsg">

            </td>
        </tr>
            
		<tr>
            <td>
                <div class="country-header">COUNTRY:</div>
                <input type="text" name="Country" placeholder="COUNTRY" id="Country">
            </td>
            <td id="CountrykMsg">

            </td>
        </tr>
             
		<tr class="BirthDate"><td>BIRTHDATE</td>
            <td class="select_birthday" style="margin-top:8px">
            
                <select name="Bday_day" id="bday"><?= $days ?></select>
                <select name="Bday_month" id="bmonth"><?= $months ?></select>
                <select name="Bday_year" id="byear"><?= $years ?></select>
		    </td>
        
        <td id="BirthDaykMsg"></td></tr>
		
		<tr class="Newsletter">
            <td>NEWSLETTER
                <span class="tooltiptext">We do not send spam</span>
            </td>
            <td>
                <input type="checkbox" name="NewsLetter" id="NewLetter" CHECKED>
            </td>
            <td id="NewsLetterMsg"></td>
        </tr>
		</table>
	</div>
	
	<div id="SubmitRow" class="section">
		
		<input type="submit" name="SubmitForm" id="SubmitForm" value=" > REGISTER NOW  ">
	
	
	</div>
	
</div>
</form>


