<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show/add Events</title>
</head>

<body>
    <h1>Show/add Events</h1>
    <?php
    $mysqli = mysqli_connect("localhost", "root", "", "calendar");
    if ($_POST) {
        //creating safe variables for sql statments
        $safe_M = mysqli_real_escape_string($mysqli, $_POST['m']);
        $safe_D = mysqli_real_escape_string($mysqli, $_POST['d']);
        $safe_Y = mysqli_real_escape_string($mysqli, $_POST['y']);
        $safe__event__title = mysqli_real_escape_string($mysqli, $_POST['event__title']);
        $safe__event__shortdesc = mysqli_real_escape_string($mysqli, $_POST['event__shortdesc']);
        $safe__event__time__hh = mysqli_real_escape_string($mysqli, $_POST['event__time__hh']);
        $safe__event__time__mm = mysqli_real_escape_string($mysqli, $_POST['event__time__mm']);
  
        //creating date for entring in database for start column
        $event__date = $safe_Y . "-" . $safe_M . "-" . $safe_D . " " . $safe__event__time__hh . ":" . $safe__event__time__mm . ":00";
        
        //insert into database calendar_events tabel
        $insEvent__sql = "INSERT INTO calendar_events (event_title,event_shortdesc,event_start) 
                    VALUES ('" . $safe__event__title . "','" . $safe__event__shortdesc . "','" . $event__date . "')";

        $insEvent__res = mysqli_query($mysqli, $insEvent__sql) or die(mysqli_error($mysqli));
    } else {
        $safe_M = mysqli_real_escape_string($mysqli, $_GET['m']);
        $safe_D = mysqli_real_escape_string($mysqli, $_GET['d']);
        $safe_Y = mysqli_real_escape_string($mysqli, $_GET['y']);
    }
    //show events that are avialable for that day
    $getEvent__sql = "SELECT event_title,event_shortdesc,date_format(event_start,'%l:%i:%p')as fmt_date FROM calendar_events 
                    WHERE year(event_start)='" . $safe_Y . "'AND 
                    month(event_start)='" . $safe_M . "'AND 
                    dayofmonth(event_start)='" . $safe_D . "'
                    ORDER BY event_start";
    $getEvent__res = mysqli_query($mysqli, $getEvent__sql)
                     or die(mysqli_error($mysqli));
     
    if(mysqli_num_rows($getEvent__res)>0){
        $event__txt="<ul>";
        while($row=@mysqli_fetch_array($getEvent__res)){
            $event__title=stripslashes($row['event_title']);
            $event__shortdesc=stripslashes($row['event_shortdesc']);
            $fmt__date=$row['fmt_date'];
            $event__txt.="<li><strong>".$fmt__date."</strong>:".$event__title."<br>".$event__shortdesc."</li>";
        }
        $event__txt.="</ul>";
        mysqli_free_result($getEvent__res);
    }else{
        $event__txt="";
    }  
    
    //close connections to mysql

    mysqli_close($mysqli);

    if($event__txt!=""){
       echo"<p><strong>Today's Events:</strong></p> 
              $event__txt<hr>"; 
    }
    //showing from for adding events 
    ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <p><strong>Would you like to add an event?</strong><br>
    Complete the form below and press the submit button to
    add the event and refresh this window.</p>

    <p><label for="event__title">Event Title:</label><br>
    <input type="text" id="event__title" name="event__title" size="25" maxlength="25"></p>    
    
    <p><label for="event__shortdesc">Event Description:</label><br>
    <input type="text" id="event__shortdesc" name="event__shortdesc" size="25" maxlength="255"></p>

    <fieldset>
    <legend>Event Time (hh:mm):</legend>
    <select name="event__time__hh">
    <?php
     for($i=1;$i<=24;$i++){
         echo"<option value=\"$i\">$i</option>";
     }
    ?> 
    </select>
    <select name="event__time__mm">
    <option value="00">00</option>
    <option value="15">15</option>
    <option value="30">30</option>
    <option value="45">45</option>
    </select>
    </fieldset>
    <input type="hidden" name="m" value="<?php echo $safe_M; ?>">
    <input type="hidden" name="d" value="<?php echo $safe_D  ?>">    
    <input type="hidden" name="y" value="<?php echo $safe_Y  ?>">  

    <button type="submit" name="submit" value="submit">Add Event</button>      
    </form>
</body>
</html>