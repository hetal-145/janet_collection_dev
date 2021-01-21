<?php //echo "<pre>"; print_r($faq_details); exit; ?>
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




<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2> FAQ'S</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong> FAQ'S</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> FAQ'S</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">                    
                    <form name="faq" class="faq">
                        <div class="form-group">
                            <span class="help-block m-b-none"><b>Faq Question(*)</b></span>
                            <input type="text" class="form-control faq_ques" placeholder="Faq Question" name="faq_ques" value="<?php echo $faq_details["faq_question"]; ?>">
                        </div>
                        
                        <div class="form-group">
                            <span class="help-block m-b-none"><b>Faq Answer(*)</b></span>
                            <div class="summernote faq_ans" style="width: 100%; height: 250px;" id="t_c" name="faq_ans"><?php echo $faq_details["faq_answer"]; ?></div>
                            <input type="hidden" class="t_c" name="t_c" />
                        </div>
                        
                        <input type="hidden" class="form-control faq_id" name="faq_id" value="<?php echo $faq_details["faq_id"]; ?>">

                        <div class="form-group">
                            <a class="btn btn-primary save_btn"> Save</a>
                            <span class="success_msg" style="color:green; padding: 7px;"></span>
                        </div>
                    </form> 
                </div>


            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(e){
    });
    
    $(document).on('click', '.save_btn', function () {
         $('.success_msg').html('');
        $('.error').html('');
        $('#msg').html('');
        var valid = true;
        var in_tc = $('.faq .summernote').code();
        
        var frm = $('form[name = "faq"]');
        
        var faq_ques = frm.find('[name = "faq_ques"]').val();
        if (faq_ques == '') {
            frm.find('[name = "faq_ques"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter faq question'));
            valid = false;
        } 
        
        if (in_tc == '<p><br></p>') {
            frm.find('[name = "faq_ans"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter faq answer'));
            valid = false;
        }

        if (valid == true) {
            var btn_txt = $('.save_btn').html();
            $('.save_btn').html('Saving...');
            $('#faq').val(in_tc);
            
            $.ajax({
                url: 'faq_list/save',
                data: {
                    'faq_id': $(".faq_id").val(),
                    'faq_answer': in_tc,
                    'faq_question' : $(".faq_ques").val()
                },
                type: 'post',
                success: function (data) {
                    if (data == 'success') {
                        window.location = "faq_list";
                    }
                }
            });
        } else {
            return false;
        }

    });
</script>
