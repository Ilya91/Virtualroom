!$(function () {
    /**
     * @constructor
     */
    function Members() {
        this.$raiseHand = $('#raise_hand');
    }

    Members.prototype.addEventListener = function () {

        var sess;
        var defaultChannels = ['__keyspace@0__:global:classroom:*'];

        ab.connect("ws://localhost:8080",
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
                        $("a[data-key='" + obj.student.id +"']").find('i').toggleClass('glyphicon-hand-up');
                    }

                }
                $("a[data-key='" + event +"']").find('i').toggleClass('glyphicon-hand-up');
            });
            console.log("ab: subscribed to: " + chan);
            return true;
        }


        this.$raiseHand.on("click", function (event) {

            event.preventDefault();
            $.ajax({
                url: 'site/raise',
                type: 'post',
                data: {
                    raise: true,
                },
                error: function (response) {
                    console.log('error');
                },
                success: function (response) {

                }
            })

        });
    }

    /**
     * start script
     */
    Members.prototype.init = function () {
        /**
         * event
         */
        this.addEventListener();
    };

    (new Members).init();
});