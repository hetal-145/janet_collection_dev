<div class="clearfix"></div>
<div class="footer">
    <div class="" style="text-align: center">
        <strong>Copyright  © <?php echo date('Y'); ?></strong> Janet-Collection Admin Panel  All rights reserved.
    </div>
</div>

</div>
</div>

<a class="modal_alert" data-target="#modal_alert" data-toggle="modal" style="display:none"></a>
<div class="modal fade" id="modal_alert" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
<!--                <h3 class="mb-0">Are you sure you want to<br>submit review?</h3>-->
                <h3 class="desc mt-2 mb-3 display_msg"></h3>
                <div class="text-center">
                    <button type="button" class="btn btn-danger" id="confirmed" data-dismiss="modal">OK!</button>
                </div>
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

<!-- Custom and plugin javascript -->
<script src="assets/js/inspinia.js"></script>
<script src="assets/js/plugins/pace/pace.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="//cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.2/multiple-select.min.js"></script>
<script src="assets/js/jquery-ui-1.10.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>

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
	
	if ($(".stime_picker").length) {
            if (isMobile.any()) {
                $('.stime_picker').attr('type', 'time');
            } else {
                $('.stime_picker').attr('type', 'text');
                $('.stime_picker').timepicker({
                    autoclose: true,
                    timeFormat: "HH:mm",
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
                    timeFormat: "HH:mm",
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
