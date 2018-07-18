var sess;
var KEY_RETURN = 13;
var channels = [];
var defaultChannels = ['channel:jmoz'];
$(function() {
// connect to WAMP server
    ab.connect("ws://{{ ws_domain }}:{{ ws_port }}",
        // WAMP session was established
        function (session) {
            // things to do once the session has been established
            console.log("ab: session connected")
            sess = session
            on_connect()
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
            add_channel(el);
        });
    }
    subscribe_to = function (chan) {
        if (!add_channel(chan)) {
            return false;
        }
        sess.subscribe(chan, function (channel, event) {
            console.log("ab: channel: " + channel + " event: " + event);
            add_response(event);
            notify("Message: " + event, 'info');
        });
        console.log("ab: subscribed to: " + chan);
        notify("Subscribed to channel " + chan, 'success');
        return true;
    }
    unsubscribe = function(channel) {
        remove_channel(channel)
        sess.unsubscribe(channel)
        console.log("ab: unsubscribed from: " + channel)
        notify('Unsubscribed from channel ' + channel, 'warning')
    }
    publish = function(channel, message) {
        sess.publish(channel, message);
    }
    redis_publish = function(message) {
        $.post('{{ path("pubsub") }}', {"pub": message, "channel":get_channel()}, function (data) {
            console.log("pubsub: ajax response: " + data);
        });
    }
    add_channel = function (channel) {
        if (channels.indexOf(channel) != -1) {
            return false;
        }
        channels.push(channel);
        $('ul.channels').append('<li>' + channel + '</li>');
        $('select.channels').append('<option>' + channel + '</option>');
        return channels;
    }
    remove_channel = function (channel) {
        i = channels.indexOf(channel)
        if (i == -1) {
            return false
        }
        channels.splice(i, 1)
        $('ul.channels li').filter(function() { return $.text([this]) === channel; }).remove();
        $('select.channels option').filter(function() { return $.text([this]) === channel; }).remove();
        return channels
    }
    get_channel = function () {
        return $('select.channels').val()
    }
    notify = function (message, type) {
        n = $('#notify')
        n.stop().text(message).css({opacity: 1}).removeClass().addClass('alert alert-' + type)
        n.delay(1000).fadeTo(2000, 0.3)
    }
    add_response = function (text) {
        $('#response').val(function (i, val) {
            return text + "\n" + val;
        });
    }
// publish to connected websockets
    $('#pub').keypress(function (e) {
        if (e.which == KEY_RETURN) {
            publish(get_channel(), this.value);
            $(this).val('');
        }
    });
// subscribe to a channel
    $('#sub').keypress(function (e) {
        if (e.which == KEY_RETURN) {
            if (subscribe_to(this.value)) {
                $(this).val('');
            }
        }
    });
// unsubscribe to channel
    $('#unsub').click(function () {
        channel = get_channel()
        unsubscribe(channel)
    });
// publish via ajax on server side
    $('#redispub').keypress(function (e) {
        if (e.which == KEY_RETURN) {
            redis_publish(this.value);
            $(this).val('');
        }
    });

});