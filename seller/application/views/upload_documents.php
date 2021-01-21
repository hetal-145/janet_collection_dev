<?php //echo "<pre>"; print_r($_SERVER); exit; ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Manage Uploaded Documents</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Uploaded Documents</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
	<?php $user_id = $this->session->userdata('user_id'); ?>
	<a href="upload_documents/edit?pid=<?php echo $user_id; ?>" style="margin-top:30px;" class="btn btn-primary pull-right add_brand">Add Document</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                
                <div class="ibox-title">
                    <h5>Manage Uploaded Documents</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">  
                    <span class="clearfix"></span>
                    <?php echo $content; ?>
                </div>            
            </div>
        </div>
    </div>
</div>
