<!DOCTYPE html>
<html>
    <head>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>

<script>
    var socket = io.connect('https://zzing.co.kr:3303');

    $(function(){
        $('button').click(function(){
            socket.emit('chat', $('.chatinput').val());
            $('.chatinput').val('');
        })
    }); 

    socket.on('chat', function(data){
        $('#chatwondow').append("<div>"+data+"</div>");
    });

</script>
</head>
<body>
<div id="chatwindow"></div>

<input type="text" class="chatinput">
<button type="button"> SEND </button>
</body>
</html>
 

