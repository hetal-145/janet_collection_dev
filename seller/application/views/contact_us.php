<?php //echo "<pre>"; print_r($documents); exit; ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Contact Us</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Contact Us</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                
                <div class="ibox-title">
                    <h5>Contact Us</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="panel-body">
                        <form name="frm_add_user" class="frm_add_user form-horizontal">
                            <div  class="row">   
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <span class="help-block m-b-none"><b>Name</b></span>
                                        <input type="text" class="form-control name" placeholder="Name" name="name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <span class="help-block m-b-none"><b>Email</b></span>
                                        <input type="email" class="form-control email" placeholder="Email" name="email">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <span class="help-block m-b-none"><b>Message</b></span>
                                        <div class="summernote" style="width: 100%; height: 250px;" id="t_c" name="message" >
                                        </div>
                                        <input type="hidden" class="t_c" name="t_c" />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <a class="form-control btn submit_btn btn-primary pull-right" style="background-color: #1ab394;border-color: 1ab394;color: #FFFFFF;margin-top: 10px;margin-bottom: 10px;">Send</a>
                                    <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                                    <span class="error_msg pull-right" style="color:red; padding: 7px;"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('.summernote').summernote({
            height: 250,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['view', ['fullscreen', 'codeview']],
            ]
        });
    });
    var edit = function () {
        $('.click2edit').summernote({focus: true});
    };
    var save = function () {
        var aHTML = $('.click2edit').code(); //save HTML If you need(aHTML: array).
        $('.click2edit').destroy();
    };
</script>
<script>
    $(document).ready(function () {
        
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_user"]');
            var in_tc = $('.summernote').code();
            
            if (valid) {
                $(this).html('Processing...');
//                var data = new FormData(frm[0]);
//                data.append('message', in_tc);
                var data = frm.serialize();
                //console.log(data);
                $.ajax({
                    url: "contact_us/save",
                    type: "post",
                    data: data + '&message='+in_tc,
//                    contentType: false,
//                    cache: false,
//                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp === 'success'){
                            $('.submit_btn').html('Send');
                            window.location = "contact_us";
                        }
                        else if (resp === 'error'){
                           $(".error_msg").append(error_msg('Error.'));
                        }
                    }
                });
            }
        });
    });
</script>
