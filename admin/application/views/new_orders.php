
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage New Orders</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage New Orders</strong>
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
                    <h5>Manage New Orders</h5>
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

<script>
    $(document).ready(function(){
        var ftd = $('tr').find('td:eq(1)');
        $( "th:eq(1)" ).css( "display", "none" );
        
        $.each(ftd, function(i, v){
            //console.log(v);
            $(v).css( "display", "none" );
        });
    });
</script>