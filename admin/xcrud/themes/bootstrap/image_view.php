<div class="xcrud-container">
    
    <h2>Product<small> - View</small></h2>
    <div class="xcrud-view">
        <?php
            $data = $this->result_row;
            //print_r($this->result_row)
            $db = Xcrud_db::get_instance();
            $query = 'SELECT * FROM product_image WHERE product_id = ' . $data['primary_key'];
            $db->query($query);
            $result = $db->result();
        ?>
        <table class="table form-horizontal">
            <tbody>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>User Name</b></td>
                    <td class="col-sm-9"><?php echo $data['user.name']  ?></td>
                </tr>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>Title</b></td>
                    <td class="col-sm-9"><?php echo $data['product.title']  ?></td>
                </tr>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>Description</b></td>
                    <td class="col-sm-9"><?php echo $data['product.description']  ?></td>
                </tr>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>Starting Price</b></td>
                    <td class="col-sm-9"><?php echo $data['product.starting_price']  ?></td>
                </tr>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>Lowest Price</b></td>
                    <td class="col-sm-9"><?php echo $data['product.lowest_price']  ?></td>
                </tr>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>Location</b></td>
                    <td class="col-sm-9"><?php echo $data['product.location']  ?></td>
                </tr>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>Start Time</b></td>
                    <td class="col-sm-9"><?php echo $data['product.start_time']  ?></td>
                </tr>
                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>End Time</b></td>
                    <td class="col-sm-9"><?php echo $data['product.end_time']  ?></td>
                </tr>

                <tr class="form-group">
                    <td class="control-label col-sm-3"><b>Images</b></td>
                    <td class="col-sm-9">
                        <?php foreach ($result as $key => $value) {?>
                            <div class="col-md-2"><img src="../uploads/product/<?php echo $value['image'] ?>" height="100px"/></div>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
   
    <div class="xcrud-top-actions pull-right">
        <?php
        echo $this->render_button('save_return', 'save', 'list', 'btn btn-primary', '', 'create,edit') . '&nbsp;&nbsp;&nbsp;';
        echo $this->render_button('save_new', 'save', 'create', 'btn btn-default', '', 'create,edit') . '&nbsp;&nbsp;&nbsp;';
        echo $this->render_button('save_edit', 'save', 'edit', 'btn btn-default', '', 'create,edit') . '&nbsp;&nbsp;&nbsp;';
        echo $this->render_button('return', 'list', '', 'btn btn-warning');
        ?>
    </div>
    <div class="xcrud-nav">
        <?php echo $this->render_benchmark(); ?>
    </div>
</div>