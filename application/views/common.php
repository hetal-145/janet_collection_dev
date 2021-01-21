<?php 
if($type == "about_us") { 
?>
    <section class="page_section mt-66 bg-light-gray">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                    <h1 class="main_title">About Us</h1>
                </div>
            </div>
            <div class="container">        
                <?php echo $content["value"]; ?>
            </div>
        </div>
    </section>
<?php 
} 
else if($type == "privacy_policy") { 
?>
    <section class="page_section mt-66 bg-light-gray">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                    <h1 class="main_title">Privacy Policy</h1>
                </div>
            </div>
            <div class="container">        
                <?php echo $content["value"]; ?>
            </div>
        </div>
    </section>
<?php 
} 
else if($type == "term_n_condition") { 
?>
    <section class="page_section mt-66 bg-light-gray">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                    <h1 class="main_title">Terms Of Service</h1>
                </div>
            </div>
            <div class="container">        
                <?php echo $content["value"]; ?>
            </div>
        </div>
    </section>
<?php 
} 
else if($type == "cookies") { 
?>
    <section class="page_section mt-66 bg-light-gray">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                    <h1 class="main_title">Cookies</h1>
                </div>
            </div>
            <div class="container h-100vh">        
                <?php echo $content["value"]; ?>
            </div>
        </div>
    </section>
<?php 
}
else if($type == "alcohol_awareness") { 
?>
    <section class="page_section mt-66 bg-light-gray">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                    <h1 class="main_title">Alcohol Awareness</h1>
                </div>
            </div>
            <div class="container">        
                <?php echo $content["value"]; ?>
            </div>
        </div>
    </section>
<?php 
}