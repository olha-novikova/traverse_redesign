(function($){

    $(document).ready(function () {
        $('.jmfe-date-picker').removeClass('hasDatepicker');
        $('.jmfe-date-picker').datepicker({
            onSelect: function(dateText, inst) {
                $(this).addClass("has-value");
            }
        });
    });
})(jQuery)