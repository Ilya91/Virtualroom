!$(function () {
    /**
     * @constructor
     */
    function Members() {
        this.$raiseHand = $('#raise_hand');
    }

    Members.prototype.addEventListener = function () {

        //var socket = new WebSocket("ws://127.0.0.1:8081");


        this.$raiseHand.on("click", function (event) {

            /*socket.onopen = function() {
                console.log("cоединение установлено");
            };
            socket.onmessage = function(event) {
                console.log(event);
            };*/
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