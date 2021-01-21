
<script src="http://webapplayers.com/inspinia_admin/js/plugins/chosen/chosen.jquery.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen-sprite.png" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.proto.min.js"></script>

<style>
    .chosen-container{
        width: 100% !important;
    }
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2> Category Allocation</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong> Category Allocation</strong>
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
                    <h5> Assign Category</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">


                    <form name="frm_sub_category" class="frm_sub_category form-horizontal"  action="category/add_category" method="post" enctype='multipart/form-data' >
                        <div class="col-sm-12">



                            <span class="help-block m-b-none"><b>Product Category </b></span>


                            <select multiple class="chzn-select">
                                <?php foreach ($product_category as $row) { ?>
                                    <option  value="<?php echo $row['category_id']; ?>"> <?php echo $row['category']; ?></option>
                                <?php } ?>               
                            </select>
                            <span class="help-block m-b-none"><b>Service Categories </b></span>


                            <select multiple class="chzn-select">
                                <?php foreach ($product_category as $row) { ?>
                                    <option  value="<?php echo $row['category_id']; ?>"> <?php echo $row['category']; ?></option>
                                <?php } ?>               
                            </select>
                            <span class="help-block m-b-none"><b>Jobs Categories </b></span>


                            <select multiple class="chzn-select">
                                <?php foreach ($product_category as $row) { ?>
                                    <option  value="<?php echo $row['category_id']; ?>"> <?php echo $row['category']; ?></option>
                                <?php } ?>               
                            </select>
                            <span class="help-block m-b-none"><b>Housing Categories </b></span>


                            <select multiple class="chzn-select">
                                <?php foreach ($product_category as $row) { ?>
                                    <option  value="<?php echo $row['category_id']; ?>"> <?php echo $row['category']; ?></option>
                                <?php } ?>               
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>






<script>

    $('.chzn-select').chosen();
    $(document).ready(function ()
    {

        var parent_category_id = $('.parent_category_id').val();
        $('.submit_btn').click(function (e) {
            $('.error_msg').hide();
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_sub_category"]');
            var parent_category_id = frm.find('[name="parent_category_id"]').val();
            if (!parent_category_id || !parent_category_id.trim()) {
                frm.find('[name="parent_category_id"]').addClass('input_error').parents('.form-group').append(error_msg('please select the parent category'));
                valid = false;
            }
            var category = frm.find('[name="category"]').val();
            if (!category || !category.trim()) {
                frm.find('[name="category"]').addClass('input_error').parents('.form-group').append(error_msg('please enter the category'));
                valid = false;
            }

            var category_id = $('.category_id').val();

            if (!category_id)
            {
                var imgVal = $('.icon').val();

                if (imgVal == '')
                {
                    frm.find('[name="icon"]').addClass('input_error').parents('.form-group').append(error_msg('please select the category icon'));
                    valid = false;
                }

            }


            if (valid) {
                $(this).html('Processing..');
                $('form[name="frm_sub_category"]').ajaxForm({
                    success: function (data)
                    {
                        $('.submit_btn').html('Save Changes');
                        if (data == '1') {
                            Xcrud.reload();
                            $('form[name = "frm_sub_category"]')[0].reset();
                            $('form[name = "frm_sub_category"]').find('[type = "hidden"]').val('');
                            $('.parent_category_id').val(parent_category_id);
                            $('[data-dismiss = "modal"]').trigger('click');
                        } else
                        {

                        }
                    }
                }).submit();
            }



        });





        $(document).on('click', '.edit_data', function () {

            var category_id = $(this).attr('data-primary');

            $.ajax({
                url: 'category/get_category',
                type: 'post',
                data: {
                    'category_id': category_id
                },
                success: function (sub_category) {
                    if (sub_category) {
                        sub_category = JSON.parse(sub_category);
                        var formdata = {
                            'parent_category_id': sub_category.parent_category_id,
                            'category_id': sub_category.category_id,
                            'category': sub_category.category,
                            'status': sub_category.status
                        };
                        console.log(formdata);
                        $('[name = "frm_sub_category"]').populate(formdata);
                    } else
                    {

                    }
                }
            });
        });
    });
</script>
