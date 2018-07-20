!$(function () {
    /**
     * @constructor
     */
    function Members() {
        this.$raiseHand = $('#raise_hand');
    }

    Members.prototype.addEventListener = function () {

        //var socket = new WebSocket("ws://virtualroom:8081/classroom");

        var ws = new Wampy('ws://localhost:8081/classroom');

        console.log(ws);

        this.$raiseHand.on("click", function (event) {

            /*socket.send(JSON.stringify({"command": "subscribe","identifier":"{\"channel\":\"classroom\"}"}));

            socket.onmessage = function(msg) {
                console.log(JSON.parse(msg.data).message);
            }*/


            event.preventDefault();
            $.ajax({
                url: 'site/raise',
                type: 'post',
                data: {
                    data: 'raise',
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