!$(function () {
    /**
     * @constructor
     */
    function Members() {
        this.$raiseHand = $('#raise_hand');
    }

    Members.prototype.addEventListener = function () {


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