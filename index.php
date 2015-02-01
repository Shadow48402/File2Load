<style>

body {
	background: url(http://fc04.deviantart.net/fs70/f/2010/211/e/4/Vista_Styled_HD_Background_by_jcsawyer.jpg);
	  font-family: sans-serif;
  -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      color:white;
      font-style: italic
      font-size:15px;
      text-shadow:5px 5px 5px #000000;
margin:100
padding:100;
 

}
 .myButton { background-color:#44c767; -moz-border-radius:28px; -webkit-border-radius:28px; border-radius:28px; border:1px solid #18ab29; display:inline-block; cursor:pointer; color:#ffffff; font-family:arial; font-size:17px; padding:16px 31px; text-decoration:none; text-shadow:0px 1px 0px #2f6627; } .myButton:hover { background-color:#5cbf2a; } .myButton:active { position:relative; top:1px; }
 .mybbutton { background-color:#1ca4e3; -moz-border-radius:28px; -webkit-border-radius:28px; border-radius:28px; border:1px solid #1ca4e3; display:inline-block; cursor:pointer; color:#ffffff; font-family:arial; font-size:17px; padding:16px 31px; text-decoration:none; text-shadow:0px 1px 0px #2f6627; } .myBbutton:hover { background-color:#1ca4e3; } .myBbutton:active { position:relative; top:1px; }
</style>
<center>
<?php
////////////////////////////////////
// ALLE RECHTEN VOORBEHOUDEN AAN VIRTUAL
// En Niels Hamelink http://twitter.com/NielsHamelink
////////////////////////////////////

/* Notitie:
*******



*/

/* CONFIG
*********/

$extensions = array('png', 'gif', 'jpg', 'jpeg', 'bmp', 'pdf', 'doc', 'docx', 'html', 'psd', 'css'); // ALLOWED EXTENSIONS
$tfolder = "uploads/"; // UPLOADS FOLDER WITH "/" AT THE END (JUST DIR)!
$scriptloc = "https://smallfiles.eu/testings/"; // SCRIPT LOCATION WITH "/" AT THE END (FULL URL)!
$maxfsize = 3; // MAXIMUM FILESIZE (IN MEGABYTES)
$spamProtection = false;
  $dbdb = "database";
  $dbpass = "password";
  $dbhost = "host";
  $dbuser = "username";
$secCooldown = 2; //Seconds between file uploads (cooldown)
if($spamProtection == true){
  $db=mysqli_connect("$dbhost","$dbuser","$dbpass","$dbdb");
  if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  mysqli_query($db, "CREATE TABLE IF NOT EXISTS prot(
  ip VARCHAR(100),
  data VARCHAR(100)
  )");
}

    // CHECK IF THE FORM HAS BEEN SUBMITTED
    if($_SERVER['REQUEST_METHOD'] == "POST") {
		$ip = $_SERVER['REMOTE_ADDR'];
		if($spamProtection == true){
		  $date = date(Y.n.d.H.i.s);
	      $query = "SELECT data FROM prot WHERE ip='$ip' order by data desc LIMIT 1";
          $results = mysqli_query($db, $query);
          while($rows = mysqli_fetch_assoc($results)){ 
            $oldDate = $rows['data'];
            if($date-$oldDate < $secCooldown){
              die("You are not allowed to spam files!");
            }
          }
	 }
        $fname = $_FILES['filen']['name']; // FILE NAME FOR EXTENSION CHECK
        $fext = strtolower(end(explode('.', $fname))); // GET EXTENSION
        $ftemp = $_FILES['filen']['tmp_name']; // TEMP NAME
        $newname = md5(rand(rand(1, 9999), rand(1, 9999))) . "." . $fext; // RANDOM NUMBER BETWEEN 2 RANDOM NUMBERS BETWEEN 1 AND 9999 AND MD5 ENCODED = RANDOM FILE NAME
        $target = $tfolder . $newname; // LOCATION FILE
        
        // CHECK IF THERE IS A FILE SELECTED
        if(!empty($fname)) {
            // CHECK THE EXTENSION
            foreach($extensions as $check) {
                if($check == $fext) {
                    $extensioncheck = true;
                }
            }
            // IF EXTENSION IS ALLOWED
            if($extensioncheck == true) {
                // IF FILE IS TOO BIG
                if(filesize($ftemp) > $maxfsize * (1024*1024)) {
                    echo "Je bestand is te groot, de maximale grootte is: <b>" . $maxfsize . "</b>MB.";
                }
                // IF FILESIZE IS ALLOWED
                else {
                    // CHECK FOR FALSE FILES EG image.php.gif (SOME SERVERS JUST TAKE .php AND THIS IS A POSSIBLE RISK)
                    if(!strstr(strtolower($fname), "php")) {
                        $upload = move_uploaded_file($ftemp, $target); // MOVE TO FOLDER WITH NEW RANDOM NAME
                        // TRY TO MOVE THE FILE TO THE DIRECTORY
                        if($upload) {
							if($spamProtection == true){
							  mysqli_query($db, "INSERT INTO `prot` SET `ip`='$ip', `data`='$date'");
							}
                            echo "Je afbeelding is met succes geüpload!.<br />Download link: <b>" . $scriptloc . $target; 
							if($fext == "jpg" || $fext == "png" || $fext == "jpeg"){
								echo "<br>Preview:<br><img src=". $scriptloc . $target ."></b>";
							}
                            $succes = true;
                        }
                        // UPLOAD ERROR
                        else {
                            echo "upload error";
                        }
                    }
                    // WHEN THE FILE NAME CONTAINS php
                    else {
                        echo "Je kan de extensie niet uploaden: 'php'!";
                    }
                } // CLOSE FILESIZE ALLOWED ELSE FUNCTION
            } // CLOSE EXTENSION ALLOWED IF FUNCTION
            // EXTENSION ERROR
            else {
                echo "Deze extensie is niet toegestaan.";
            }
        } // CLOSE IF FILE SELECTED IF FUNCTION
        // NO FILE SELECTED ERROR
        else {
            echo "Selecteer een bestand.";
        }
        
    } // CLOSE IF SUBMIT IS PRESSED FUNCTION
    
    // IF FILE WAS UPLOADED SUCESSFULLY HIDE FORM
    if($succes !== true) {
        echo '<form action="" method="post" enctype="multipart/form-data">';
        echo ' <input type="file" class="mybbutton"  name="filen" /> <input type="submit" class="myButton" name="subform" value="Upload!" />';
        echo '</form>';
    }
?>
</center>