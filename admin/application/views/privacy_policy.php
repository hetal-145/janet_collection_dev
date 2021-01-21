
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
        <h2> Privacy Policy</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong> Privacy Policy</strong>
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
                    <h5> Privacy Policy</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <a class="btn btn-primary save_btn" > Save</a>
                      <span class="success_msg" style="color:green; padding: 7px;"></span>
                    <form name="privacy_policy_frm" class="privacy_policy_frm">
                        <div class="summernote" style="width: 100%; height: 250px;" id="privacy_policy" name="privacy_policy" >
                            <?php echo $res['value'];
                            ?>
                        </div>
                        <input type="hidden" class="privacy_policy" name="privacy_policy" />

                    </form> 
                </div>


            </div>
        </div>
    </div>
</div>











<script>
    $(document).on('click', '.save_btn', function () {
                    $('.success_msg').html('');

        $('.error').html('');
        $('#msg').html('');
        var valid = true;

        //var in_pp = $('.frm_pp textarea[name="term_condition"]');
        var in_pp = $('.privacy_policy_frm .summernote').code();

//        if (!in_pp) {
//            $('.error.error_term_condition').html('Content required');
//            valid = false;
//        }

        if (valid == true) {
            var btn_txt = $('.save_btn').html();
            $('.save_btn').html('Saving...');


            $('#term_condition').val(in_pp);


            $.ajax({
                url: 'privacy_policy/add_privacy_policy',
                data: {
                    'privacy_policy': in_pp
                },
                type: 'post',
                success: function (data) {
                    if (data == 'success') {
                         $('.save_btn').html('Save');
                               $('.success_msg').html('Saved successfully ');
                      
                    }
                }
            });
        } else {
            return false;
        }

    });
</script>
