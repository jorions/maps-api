<form action="Day10-MapsAPI.php" method="POST">
    Enter a location to put on your path:  <input type="text" name="location" value=""><br>
    <input type="submit" name="submit" value="Submit your map!" style="width: 300px">
    <input type="submit" name="reset" value="Reset your path" style="width: 300px">
    <input type="submit" name="goBack" value="Go back 1 location" style="width: 300px">
</form>

<?php
$db = new mysqli("localhost", "root", "root", "intro_to_php");

//connection error handling
if($db->connect_errno) {
    echo "Oh no! Failed to connect to MySQL<br>";
    echo $db->connect_error;
    exit();
}


//reset table
if(isset($_POST["reset"])) {
    $db->query("TRUNCATE TABLE markers");
}


//go back 1 step
if(isset($_POST["goBack"])) {
    //GET COUNT OF ROWS. IF = 0, echo "Nothing to step back!" else go back 1
    $count = $db->query("SELECT COUNT(*) FROM markers");
    $row = $count->fetch_array(); //turns $count into an array
    if($row[0] > 0) {

        //CREATE NEW TABLE WITH ID, THE SELECT LAST-ADDED ROW USING LIMIT 1
        $stmt = $db->prepare("DELETE FROM markers WHERE id=(?)");
        $lastID = $db->query("SELECT * FROM markers ORDER BY id DESC LIMIT 1");
        $lastID = $lastID->fetch_array();
        $stmt->bind_param("i", $lastID[0]);
        $stmt->execute();
        //createMapStepBack(); - commented out because it doesn't work

        //generate map using list
        echo "<img src=https://maps.googleapis.com/maps/api/staticmap?size=600x600&path=".$fullList.">";
    } else {
        echo "<strong>Error: </strong>You don't have any markers placed yet!<br>";

        //get all addresses and put them into a text string
        $allAddresses = $db->query("SELECT * FROM markers");
        $fullList = "";
        foreach($allAddresses as $address) {
            $fullList .= urlencode($address["address"]) . "|";
        }
        $fullList = substr($fullList, 0, -1);

        //generate map using list
        echo "<img src=https://maps.googleapis.com/maps/api/staticmap?size=600x600&path=".$fullList.">";
    }

}


//create map
if(isset($_POST["submit"])) {
    //createMapAdded();

    //add location to the database
    if ($_POST["location"] != "") {
        $stmt = $db->prepare("INSERT INTO markers (address) VALUES (?)"); //creates an instance of a mysql query called $stmt
        $stmt->bind_param("s", $_POST["location"]);
        $stmt->execute();
    } else {
        echo "<strong>Error: </strong>Enter a location!<br>";
    }

    //get all addresses and put them into a text string
    $allAddresses = $db->query("SELECT * FROM markers");
    $fullList = "";
    foreach($allAddresses as $address) {
        $fullList .= urlencode($address["address"]) . "|";
    }
    $fullList = substr($fullList, 0, -1);

    //generate map using list
    echo "<img src=https://maps.googleapis.com/maps/api/staticmap?size=600x600&path=".$fullList.">";
} else {
    if(!isset($_POST["goBack"]))
        echo "<h1>No map yet!</h1>";
}



/*

function createMapAdded() {
    $db = new mysqli("localhost", "root", "root", "intro_to_php"); //WHY DO I NEED TO RE-DECLARE THIS? IS IT BECAUSE IT'S IN A FUNCTION?
    //add location to the database
    if ($_POST["location"] != "") {
        $stmt = $db->prepare("INSERT INTO markers (address) VALUES (?)"); //creates an instance of a mysql query called $stmt
        $stmt->bind_param("s", $_POST["location"]);
        $stmt->execute();
    } else {
        echo "Enter a location";
    }

    //get all addresses and put them into a text string
    $allAddresses = $db->query("SELECT * FROM markers");
    $fullList = "";
    foreach($allAddresses as $address) {
        $fullList .= urlencode($address["address"]) . "|";
    }
    $fullList = substr($fullList, 0, -1);

    //generate map using list
    echo "<img src=https://maps.googleapis.com/maps/api/staticmap?size=600x600&path=".$fullList.">";
}

function createMapStepBack() {
    $db = new mysqli("localhost", "root", "root", "intro_to_php"); //WHY DO I NEED TO RE-DECLARE THIS? IS IT BECAUSE IT'S IN A FUNCTION?
    //get all addresses and put them into a text string
    $allAddresses = $db->query("SELECT * FROM markers");
    $fullList = "";
    foreach($allAddresses as $address) {
        $fullList .= urlencode($address["address"]) . "|";
    }
    $fullList = substr($fullList, 0, -1);

    //generate map using list
    echo "<img src=https://maps.googleapis.com/maps/api/staticmap?size=600x600&path=".$fullList.">";
}

*/

?>
