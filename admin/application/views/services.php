
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Services</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">home</a>
            </li>
            <li class="active">
                <strong>Manage Services</strong>
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
                    <h5>Services list</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <?php echo $content; ?>
                </div>


            </div>
        </div>
    </div>
</div>

<!--//modal-->
<div class="modal fade mld_product_video in" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Job Video</h4>
            </div>

            <div class="modal-body">

                <iframe class="video_frame" src="" width="100%"></iframe>
                <div class="clearfix"></div>
            </div>



        </div>
    </div>
</div>




<script>
    $(document).ready(function ()
    {
        $('.video_thumb_img').click(function (e) {
            $('.mld_product_video').toggle();
            var product_id = $(this).attr('video_url');
            $('.video_frame').attr('src',product_id);

        });
    
        $('.close').click(function (e) {
            $('.mld_product_video').toggle();
        });

        $(document).on('click', '.btn_add', function () {
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var formdata = {
                'Services_id': '',
                'status': '1'
            };
            console.log(formdata);
            $('[name="frm_Services"]').populate(formdata);
        });


        $(document).on('click', '.edit_data', function () {
            var Services_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'Services/get_Services',
                data: 'Services_id=' + Services_id,
                type: 'post',
                success: function (Services) {
                    if (Services) {
                        Services = JSON.parse(Services);
                        var formdata = {
                            'Services_id': Services.Services_id,
                            'status': Services.status
                        };
                        console.log(formdata);
                        $('[name="frm_Services"]').populate(formdata);

                    } else
                    {

                    }
                }
            });
        });
    });
</script>
