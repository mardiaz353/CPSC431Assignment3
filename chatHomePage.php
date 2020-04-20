<!DOCTYPE html>
<html>
    <head>
        <title>AJAX Chat</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
        <style>
            .bubble-recv
            {
              position: relative;
              width: 330px;
              height: 75px;
              padding: 10px;
              background: #AEE5FF;
              -webkit-border-radius: 10px;
              -moz-border-radius: 10px;
              border-radius: 10px;
              border: #000000 solid 1px;
              margin-bottom: 10px;
            }
            
            .bubble-recv:after 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 15px 15px 0;
              border-color: transparent #AEE5FF;
              display: block;
              width: 0;
              z-index: 1;
              left: -15px;
              top: 12px;
            }
            
            .bubble-recv:before 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 15px 15px 0;
              border-color: transparent #000000;
              display: block;
              width: 0;
              z-index: 0;
              left: -16px;
              top: 12px;
            }
                        
            .bubble-sent
            {
              position: relative;
              width: 330px;
              height: 75px;
              padding: 10px;
              background: #00E500;
              -webkit-border-radius: 10px;
              -moz-border-radius: 10px;
              border-radius: 10px;
              border: #000000 solid 1px;
              margin-bottom: 10px;
            }
            
            .bubble-sent:after 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 0 15px 15px;
              border-color: transparent #00E500;
              display: block;
              width: 0;
              z-index: 1;
              right: -15px;
              top: 12px;
            }
            
            .bubble-sent:before 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 0 15px 15px;
              border-color: transparent #000000;
              display: block;
              width: 0;
              z-index: 0;
              right: -16px;
              top: 12px;
            }
            
            .spinner {
              display: inline-block;
              opacity: 0;
              width: 0;
            
              -webkit-transition: opacity 0.25s, width 0.25s;
              -moz-transition: opacity 0.25s, width 0.25s;
              -o-transition: opacity 0.25s, width 0.25s;
              transition: opacity 0.25s, width 0.25s;
            }
            
            .has-spinner.active {
              cursor:progress;
            }
            
            .has-spinner.active .spinner {
              opacity: 1;
              width: auto; 
            }
            
            .has-spinner.btn-mini.active .spinner {
              width: 10px;
            }
            
            .has-spinner.btn-small.active .spinner {
              width: 13px;
            }
            
            .has-spinner.btn.active .spinner {
              width: 16px;
            }
            
            .has-spinner.btn-large.active .spinner {
              width: 19px;
            }
            
            .panel-body {
              padding-right: 35px;
              padding-left: 35px;
            }
            
        </style>
    </head>
    <body>
    <h1 style="text-align:center">AJAX Chat</h1>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title">Let's Chat</h2>
            </div>
            <div class="panel-body" id="chatPanel">
            </div>
            <div class="panel-footer">
                <div class="input-group">
                    <input type="text" class="form-control" id="username" placeholder="Enter your nickname here..."/>
                    <span class="input-group-btn">
                        <button id="inputUsernameBtn" class="btn btn-primary has-spinner" type="button">
                            <span class="spinner"><i class="icon-spin icon-refresh"></i></span>
                            Start
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>    
    <script src="//code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="client.js"></script>
    </body>
</html>


<?php
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

if(isset($_SESSION['id'])) {
	header('location:chat.html');
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

if(isset($_POST['id'])) {
//input the username into the database for that user
	$username = isset($_POST['username']) ? $_POST['username'] : '';            
            $username = strip_tags($username);
            $query = "INSERT INTO chatlog (username) VALUES(?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param('ssi', $username); 
            $stmt->execute(); 
            print json_encode(['success' => true]);
            exit;
}


header('Location: chat.html'); //you can replace this redirect with one to the chat page of your site
die();

?>
