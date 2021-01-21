
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Product Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">home</a>
            </li>
            <li class="active">
                <strong>Product Details</strong>
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
                    <h5>Manage Category</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                    <div class="ibox-content">

                        <div class="row">
                            <div class="col-md-5" role="toolbar">


                                <slick dots="true" class="ng-isolate-scope slick-initialized slick-slider"><button type="button" data-role="none" class="slick-prev slick-arrow" aria-label="Previous" role="button" style="">Previous</button>

                          



                                    <button type="button" data-role="none" class="slick-next slick-arrow" aria-label="Next" role="button" style="">Next</button><ul class="slick-dots" style="" role="tablist"><li class="slick-active" aria-hidden="false" role="presentation" aria-selected="true" aria-controls="navigation00" id="slick-slide00"><button type="button" data-role="none" role="button" aria-required="false" tabindex="0">1</button></li><li aria-hidden="true" role="presentation" aria-selected="false" aria-controls="navigation01" id="slick-slide01"><button type="button" data-role="none" role="button" aria-required="false" tabindex="0">2</button></li><li aria-hidden="true" role="presentation" aria-selected="false" aria-controls="navigation02" id="slick-slide02"><button type="button" data-role="none" role="button" aria-required="false" tabindex="0">3</button></li></ul></slick>

                            </div>
                            <div class="col-md-7">

                                <h2 class="font-bold m-b-xs">
                                    Desktop publishing software
                                </h2>
                                <small>Many desktop publishing packages and web page editors now.</small>
                                <div class="m-t-md">
                                    <h2 class="product-main-price">$406,602 <small class="text-muted">Exclude Tax</small> </h2>
                                </div>
                                <hr>

                                <h4>Product description</h4>

                                <div class="small text-muted">
                                    It is a long established fact that a reader will be distracted by the readable
                                    content of a page when looking at its layout. The point of using Lorem Ipsum is

                                    <br>
                                    <br>
                                    There are many variations of passages of Lorem Ipsum available, but the majority
                                    have suffered alteration in some form, by injected humour, or randomised words
                                    which don't look even slightly believable.
                                </div>
                                <dl class="small m-t-md">
                                    <dt>Description lists</dt>
                                    <dd>A description list is perfect for defining terms.</dd>
                                    <dt>Euismod</dt>
                                    <dd>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</dd>
                                    <dd>Donec id elit non mi porta gravida at eget metus.</dd>
                                    <dt>Malesuada porta</dt>
                                    <dd>Etiam porta sem malesuada magna mollis euismod.</dd>
                                </dl>
                                <hr>

                                <div>
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm"><i class="fa fa-cart-plus"></i> Add to cart</button>
                                        <button class="btn btn-white btn-sm"><i class="fa fa-star"></i> Add to wishlist </button>
                                        <button class="btn btn-white btn-sm"><i class="fa fa-envelope"></i> Contact with author </button>
                                    </div>
                                </div>



                            </div>
                        </div>

                    </div>
       

           
            </div>
        </div>
    </div>
</div>






<script>
    $(document).ready(function ()
    {
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $('.form-group').removeClass('input_error');
            var valid = true;
            var frm = $('form[name="frm_category"]');
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
                $('form[name="frm_category"]').ajaxForm({
                    success: function (data)
                    {
                        $('.submit_btn').html('Save Changes');
                        if (data == '1') {
                            Xcrud.reload();
                            $('form[name="frm_category"]')[0].reset();
                            $('form[name="frm_category"]').find('[type="hidden"]').val('');
                            $('[data-dismiss="modal"]').trigger('click');
                        } else
                        {

                        }
                    }
                }).submit();
            }

        });

        $(document).on('click', '.btn_add', function () {
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $('.form-group').removeClass('input_error');
            var formdata = {
                'category_id': '',
                'category': '',
                'icon': '',
                'status': '1'
            };
            console.log(formdata);
            $('[name="frm_category"]').populate(formdata);
        });


        $(document).on('click', '.edit_data', function () {
            var category_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $('.form-group').removeClass('input_error');
            $.ajax({
                url: 'category/get_category',
                data: 'category_id=' + category_id,
                type: 'post',
                success: function (category) {
                    if (category) {
                        category = JSON.parse(category);
                        var formdata = {
                            'category_id': category.category_id,
                            'category': category.category,
                            'category_type': category.category_type,
                            'status': category.status
                        };
                        console.log(formdata);
                        $('[name="frm_category"]').populate(formdata);

                    } else
                    {

                    }
                }
            });
        });
    });
</script>
