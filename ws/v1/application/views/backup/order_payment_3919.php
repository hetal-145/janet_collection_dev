<?php
$config_data = $this->db->where('key', 'client_key')->get('setting')->row_array();
//print_r($config_data); exit;
?>
<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <meta charset="UTF-8" />
    <script src="https://cdn.worldpay.com/v1/worldpay.js"></script>        

    <script type='text/javascript'>
    window.onload = function() {
      Worldpay.useTemplateForm({
        //'clientKey':'T_C_558f2186-488b-4b92-a269-ac82b41f7b6d',
        'clientKey':'<?php echo $config_data["value"]; ?>',
        'form':'paymentForm',
        'paymentSection':'paymentSection',
        'display':'inline',
        'reusable':true,
        'callback': function(obj) {
          if (obj && obj.token) {
            var _el = document.createElement('input');
            _el.value = obj.token;
            _el.type = 'hidden';
            _el.name = 'token';
            document.getElementById('paymentForm').appendChild(_el);
            document.getElementById('paymentForm').submit();
          }
        }
      });
    }
    </script>
  </head>
  <body>
    <form action="save_transation" id="paymentForm" method="post"> 
        <?php
        //print_r($_GET); 
        ?>
        <input type="hidden" name="order_id" value="<?php if(isset($_GET["order_id"])){ echo $_GET["order_id"]; } ?>">
        <input type="hidden" name="promocode_id" value="<?php if(isset($_GET["promocode_id"])){ echo $_GET["promocode_id"]; } ?>">
      <!-- all other fields you want to collect, e.g. name and shipping address -->
      <div id='paymentSection'>
        
      </div>
      <div>
        <!--<input type="submit" value="Place Order" onclick="Worldpay.submitTemplateForm()" />-->
      </div>
    </form>
  </body> 
</html> 


<script>
    $(document).load('form', function(){
        $("#token_container-button").trigger("click");
        
    });
    </script>