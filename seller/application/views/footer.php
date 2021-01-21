<div class="clearfix"></div>
<div class="footer">
    <div class="" style="text-align: center">
        <strong>Copyright  © <?php echo date('Y'); ?></strong> Janet-Collection Seller Panel  All rights reserved.
    </div>
</div>

</div>
</div>


<script>
    function error_msg(msg) {
        return '<div class="error">' + msg + '</div>';
    }
    function hide_msg() {
        setTimeout(function () {
            $('.success').html('');
        }, 3000);
    }
</script>

<script src="assets/js/jquery.form.js"></script>

<!-- Mainly scripts -->
<!--<script src="assets/js/jquery.populate.js"></script>-->
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<!-- Custom and plugin javascript -->
<script src="assets/js/inspinia.js"></script>
<script src="assets/js/plugins/pace/pace.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="//cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.2/multiple-select.min.js"></script>
<script src="assets/js/plugins/switchery/switchery.js"></script>
<script src="assets/js/jquery-ui-1.10.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<script>
    var elem = document.querySelector('.js-switch');
    var switchery = new Switchery(elem, { color: '#1AB394' });
    $("#is_online_change").change(function (){
        if($(this).prop("checked") == true){
            var is_online = "1";
        }
        else if($(this).prop("checked") == false){
            var is_online = "0";
        }        
        var seller_id = $(this).data("seller");
        //alert(is_online);
        $.ajax({
            url: 'setting/change_status',
            data: "is_online="+ is_online + "&seller_id="+seller_id,
            type: 'post',
            success: function (data) {
                if (data == 'success') {
                    if(is_online == 1) {
                        $('.success_msg').html('Status Updated. You are Online.');
                    }
                    else if(is_online == 0) {
                        $('.success_msg').html('Status Updated. You are Offline.');
                    }
                } 
                else if (data == 'error') {
                    $('.error_msg').html('Status Not Updated.');
                } 
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
     
        
        var isMobile = {
            Android: function () {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function () {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function () {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function () {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function () {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function () {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };
        if ($(".date_picker").length) {
            if (isMobile.any()) {
                $('.date_picker').attr('type', 'date');
            } else {
                $('.date_picker').attr('type', 'text');
                $('.date_picker').datepicker({
                    startView: 3,
                    todayBtn: "linked",
                    keyboardNavigation: false,
                    forceParse: false,
                    autoclose: true,
                    format: "yyyy-mm-dd"
                });
            }
        }
	
	if ($(".time_picker").length) {
            if (isMobile.any()) {
                $('.time_picker').attr('type', 'date');
            } else {
                $('.time_picker').attr('type', 'text');
                $('.time_picker').timepicker({
                    keyboardNavigation: false,
                    forceParse: false,
                    autoclose: true,
                    timeFormat: "HH:mm:ss",
                });
            }
        }
	
	if ($(".stime_picker").length) {
            if (isMobile.any()) {
                $('.stime_picker').attr('type', 'time');
            } else {
                $('.stime_picker').attr('type', 'text');
                $('.stime_picker').timepicker({
                    autoclose: true,
                    timeFormat: "HH:mm:ss",
		    onSelect: function (selectedDateTime){
			$('.etime_picker').timepicker('option', 'minTime', $('.stime_picker').val() );
		    }
                });
            }
        }
	
	if ($(".etime_picker").length) {
            if (isMobile.any()) {
                $('.etime_picker').attr('type', 'time');
            } else {
                $('.etime_picker').attr('type', 'text');
                $('.etime_picker').timepicker({
                    autoclose: true,
                    timeFormat: "HH:mm:ss",
		    onSelect: function (selectedDateTime){
			$('.stime_picker').timepicker('option', 'maxTime', $('.etime_picker').val() );
		    }
                });
            }
        }
        
        allow_numeric();
        
        
    });
    
    function allow_numeric(){
        $(".numeric").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
               //display error message
               return false;
           }
        });
    }
</script>

</body>


</html>
