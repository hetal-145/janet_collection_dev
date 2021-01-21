<style type="text/css">
    ul.accordion{list-style-type: none;padding-left: 0;margin-bottom: 0;}
    ul.accordion li{box-shadow: 0 1px 10px 0px #ccc;text-align: left;}
    .accordion .card-header:after{font-family: 'FontAwesome';content: "\f068";position: absolute;top: 22px;right: 15px;color: #ff0074;font-size: 14px;}
    .accordion .card-header.collapsed:after{content: "\f067"; position: absolute;top: 22px;right: 15px;}
    .accordion .card-header{position: relative;background-color: #ffffff;padding: 0;border-bottom: 0px;}
    .accordion .card-header a{ padding: 20px 30px 20px 20px; display: block; position: relative; color: #ff0074; font: 16px/30px circularstd-medium,sans-serif; cursor: pointer; box-shadow: 0 0px 10px 0px rgba(204, 204, 204, 0.05); margin: 10px 0; font-weight: 600; letter-spacing: .5px; }
    .accordion .card-body{letter-spacing: .5px;text-align: justify;font: 17px/30px avenirnextLTpro-regular,sans-serif;color: #9c9c9c;padding: 20px;background: #fff;border-top: 1px solid #f1f1f1;}
    .accordion .card-body p span{font-size: 16px !important;color: #000 !important;}
</style>
<section class="page_section mt-66 prod_usr_ratings">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                <h2 class="main_title">FAQ List</h2>
            </div>
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12 prod_ratings mt-4">
                <?php if (!empty($content)) { ?>
                    <ul id="accordion" class="accordion">
                        <?php foreach ($content as $faq) { ?>
                            <li>
                                <div class="card-header collapsed" id="headingOne"  data-toggle="collapse" data-target="#<?php echo $faq["faq_id"]; ?>">
                                    <a class="mb-0">
                                        <?php echo $faq["faq_question"]; ?>
                                    </a>
                                </div>
                                <div id="<?php echo $faq["faq_id"]; ?>" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                        <?php echo $faq["faq_answer"]; ?>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>                                        
            </div>
        </div>
    </div>
</section>