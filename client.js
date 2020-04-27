var pollServer = function() {
    // $.get('http://ecs.fullerton.edu/~cs431s28/assignment3/chat.php', function(result) {
        $.get('chat.php', function(result) {
        if(!result.success) { // a callback function that check it went thru
            console.log("Error polling server for new messages!");
            return;
        }
        
        $.each(result.messages, function(idx) {
            
            var chatBubble;
			const root = document.documentElement;
		

            if(this.sent_by == 'self') {
                var colorStyle = '<div class="row bubble-sent pull-right" style= "background: #' 
                + this.color + '; border-color: #' + this.color + '; --color: #' + this.color + '; color: white">';
                chatBubble = $(colorStyle +
                               'me: ' + this.message +
                               '</div><div class="clearfix"></div>');
            } else {
                var cstyle = '<div class="row bubble-recv" style="background: #'
                + this.color + '; --color: #' + this.color + '; color: white">';
                chatBubble = $(cstyle + this.username + ": " + this.message + '</div><div class="clearfix"></div>');
							   
            }
            
            $('#chatPanel').append(chatBubble);
        });
        
        setTimeout(pollServer, 5000);
    });
}

$(document).on('ready', function() {
    pollServer();
    
    $('button').click(function() {
        $(this).toggleClass('active');
    });
});

$('#sendMessageBtn').on('click', function(event) {
    event.preventDefault(); // ensure it dos not take the user away from page
    
    var message = $('#chatMessage').val();
    // gets the username and random color thru url
    let params = new URLSearchParams(location.search);
    var color = params.get('color');
    var username = params.get('username');
    
    $.post('chat.php', {
        'message' : message,
        'color': color,
        'username': username
    }, function(result) {
        
        $('#sendMessageBtn').toggleClass('active');
        
        
        if(!result.success) {
            alert("There was an error sending your message");
        } else {
            console.log("Message sent!");
            $('#chatMessage').val('');
        }
    });
    
});
