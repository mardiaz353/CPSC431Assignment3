<?php
if(isset($_POST["username"])){
	session_start();
	ob_start();
	header("Content-type: application/json");
	date_default_timezone_set('UTC');
	//connect to database
	$db = mysqli_connect('mariadb', 'cs431s28', 'Moh3poox', 'cs431s28');
	if (mysqli_connect_errno()) {
	   echo '<p>Error: Could not connect to database.<br/>
	   Please try again later.</p>';
	   exit;
	}
	//helper funtion to replace get_results() if without mysqlnd 
	function get_result( $Statement ) {
		$RESULT = array();
		$Statement->store_result();
		for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
			$Metadata = $Statement->result_metadata();
			$PARAMS = array();
			while ( $Field = $Metadata->fetch_field() ) {
				$PARAMS[] = &$RESULT[ $i ][ $Field->name ];
			}
			call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
			$Statement->fetch();
		}
		return $RESULT;
	}
	try {
		$username=$_GET["username"];
		$insertQuery = "INSERT INTO `chatlog` 
		(`username`) 
		VALUES ('$username')";
    
    
    // if query works tell user
    $retval = mysql_query($insertQuery,$db);
    
    if(! $retval){
        die('Could not enter data: ' . mysql_error());
    }
		$currentTime = time();
		$session_id = session_id();    
		$lastPoll = isset($_SESSION['last_poll']) ? $_SESSION['last_poll'] : $currentTime;    
		$action = isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST') ? 'send' : 'poll';
		switch($action) {
			case 'poll':
			   $query = "SELECT * FROM chatlog WHERE date_created >= ".$lastPoll;
			   $stmt = $db->prepare($query);
			   $stmt->execute();
			   $stmt->bind_result($id, $message, $session_id, $date_created);
			   $result = get_result( $stmt);
			   $newChats = [];
			   while($chat = array_shift($result)) {
				   
				   if($session_id == $chat['sent_by']) {
					  $chat['sent_by'] = 'self';
				   } else {
					  $chat['sent_by'] = 'other';
				   }
				 
				   $newChats[] = $chat;
				}
			   $_SESSION['last_poll'] = $currentTime;

			   print json_encode([
					'success' => true,
			'messages' => $newChats
			   ]);
			   exit;
			case 'send':
				$message = isset($_POST['message']) ? $_POST['message'] : '';            
				$message = strip_tags($message);
				$query = "INSERT INTO chatlog (message, sent_by, date_created) VALUES(?, ?, ?)";
				$stmt = $db->prepare($query);
				$stmt->bind_param('ssi', $message, $session_id, $currentTime); 
				$stmt->execute(); 
				print json_encode(['success' => true]);
				exit;
		}
	} catch(Exception $e) {
		print json_encode([
			'success' => false,
			'error' => $e->getMessage()
		]);
	}
}
?>
