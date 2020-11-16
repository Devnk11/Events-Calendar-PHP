<?php
define("ADAY", (60 * 60 * 24));
if ((!isset($_POST['month'])) || (!isset($_POST['year']))) {
    $nowArray = getdate();
    $month = $nowArray['mon'];
    $year = $nowArray['year'];
} else {
    $month = $_POST['month'];
    $year = $_POST['year'];
}
$start = mktime(12, 0, 0, $month, 1, $year);
$firstDayArray = getdate($start);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Calendar:" . $firstDayArray['month'] . " " . $firstDayArray['year']; ?></title>
    <style type="text/css">
        table {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th {
            border: 1px solid black;
            padding: 6px;
            font-weight: bold;
            background-color: #ccc;
        }

        td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
            width: 100px;
        }
    </style>

</head>

<body>
    <h1>Select a Month/Year Combination</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <select name='month'>
            <?php
            $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
            for ($i = 1; $i <= count($months); $i++) {
                echo "<option value=\"$i\"";
                if ($i == $month) {
                    echo "selected";
                }
                echo ">" . $months[$i - 1] . "</option>";
            }
            ?>
        </select>
        <select name="year">
            <?php
            for ($i = 1990; $i <= date("Y"); $i++) {

                echo "<option ";
                if ($i == $year) {
                    echo "Selected";
                }
                echo ">$i</option>";
            }

            ?>
        </select>
        <button type="submit" name="submit" value="Submit">Go!</button>
    </form>
    <br>
    <?php

    $days = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
    echo "<table><tr>\n";
    foreach ($days as $day) {
        echo "<th>" . $day . "</th>\n";
    }
    for ($count = 0; $count < (6 * 7); $count++) {
        $dayArray = getdate($start);
        if ($count % 7 == 0) {
            if ($dayArray['mon'] != $month) {
                break;
            } else {
                echo "</tr><tr>\n";
            }
        }
        if ($count < $firstDayArray['wday'] || $dayArray['mon'] != $month) {
            echo "<td>&nbsp;</td>\n";
        } else {
            $event_title = "";

            $mysqli = mysqli_connect("localhost", "root", "", "calendar");
            $chkEvent_sql = "SELECT event_title FROM calendar_events WHERE month(event_start) = '".$month."' AND dayofmonth(event_start) = '".$dayArray['mday']."' AND year(event_start) = '".$year."' ORDER BY event_start";
            $chkEvent_res = mysqli_query($mysqli, $chkEvent_sql)
                or die(mysqli_error($mysqli));
            if (mysqli_num_rows($chkEvent_res) > 0) {
                while ($ev = mysqli_fetch_array($chkEvent_res)) {
                    $event_title = stripslashes($ev['event_title']);
                }
            } else {
                $event_title = "";
            }
            echo "<td><a href=\"javascript:eventWindow('event.php?m=" . $month .
                "&amp;d=" . $dayArray['mday'] . "&amp;y=$year');\">" . $dayArray['mday'] . "</a>
                <br>" . $event_title . "</td>\n";
                unset($event_title);
            $start += ADAY;
        }
    }
    echo "</tr></table>";

    ?>
    <script type="text/javascript">
        function eventWindow(url) {
            event_popupWin = window.open(url, 'event', 'resizable=yes, scrollbars=yes,toolbar = no, width = 500, height = 600 ');
            event_popupWin.opener = self;
        }
    </script>
</body>

</html>