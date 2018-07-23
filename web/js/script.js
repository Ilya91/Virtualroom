var sess;
var defaultChannels = ['global:classroom:*', 'global:classroom:users:*'];
$(function() {


// connect to WAMP server
    ab.connect("ws://localhost:8087",
        // WAMP session was established
        function (session) {
            // things to do once the session has been established
            console.log("ab: session connected");
            sess = session;
            on_connect();
        },
        // WAMP session is gone
        function (code, reason) {
            // things to do once the session fails
            notify(reason, 'error');
            console.log("ab: session gone code " + code + " reason " + reason)
        }
    );


    on_connect = function() {
        // initialise default channels
        console.log("ab: subscribing to default channels");
        $.each(defaultChannels, function (i, el) {
            subscribe_to(el);
        });
    }


    subscribe_to = function (chan) {
        sess.subscribe(chan, function (channel, event) {
            console.log(event);
            var obj = jQuery.parseJSON(event);

            if (obj.type){
                if (obj.type === "class_config_changed") {
                    $('.list-members').empty();
                    for (var i = 0; i < obj.members.length; ++i) {
                        var hand = '';
                        if (obj.members[i].handState){
                            hand = 'glyphicon-hand-up';
                        }
                        $('.list-members').append('<a href="#" class="list-group-item" data-key="'+ obj.members[i].id +'">' + obj.members[i].name + ' <i class="glyphicon '+ hand +' pull-right "></i></a>');
                    }
                }else if (obj.type === "student_state_changed"){
                    console.log(obj.student);
                    $("a[data-key='" + obj.student.id +"']").find('i').toggleClass('glyphicon-hand-up');
                }

            }
            $("a[data-key='" + event +"']").find('i').toggleClass('glyphicon-hand-up');
            notify("Message: " + event, 'info');
        });
        console.log("ab: subscribed to: " + chan);
        notify("Subscribed to channel " + chan, 'success');
        return true;
    }

    notify = function (message, type) {
        n = $('#notify')
        n.stop().text(message).css({opacity: 1}).removeClass().addClass('alert alert-' + type)
        n.delay(1000).fadeTo(2000, 0.3)
    }
});
