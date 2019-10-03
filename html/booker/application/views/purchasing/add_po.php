<?php $this->load->view('templates/header');?>
<div class="container">

<!-- ==================== ROW ==================== -->
<div class="row">
    <div class="col-md-12">
        <!-- ==================== SPAN12 FLOATING BOX ==================== -->
        <div class="widget">
            <div class="widget-head">Create P.O</div>
            <div class="widget-content">
              	<form id="cs_form" method="post" action="<?php echo base_url()?>purchasing/save_po" data-validate="parsley">
                    <div class="col-md-12 clearfix">
                    	<div class="col-md-6 top10">
                    		<input type="text" autocomplete="off" class="form-control" placeholder="Select Supplier" id="selcustomer" />
                    		<div class="selectHolder" id="selectHolderSup"></div>
                    	</div>
                    </div>
                    <div class="col-md-12 clearfix">
                    	<div class="col-md-4">
                    		<address class="filled alert alert-success top10" style="<?php echo !isset($invoice_details)?'display:none':''?>">
                              <strong id="customer_name"><?php echo isset($invoice_details->name)?$invoice_details->name:''?></strong><br/>
                              <span id='customer_address'><?php echo isset($invoice_details->address)?$invoice_details->address:''?></span><br/>
                              <span id='customer_phone'><?php echo isset($invoice_details->phone)?$invoice_details->phone:''?></span><br/>
                              <span id='customer_email'><?php echo isset($invoice_details->email)?$invoice_details->email:''?></span><br/>
                              
                              <i class="icon-map-marker pull-right"></i>
                            </address>
                    	</div>
                        <input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo isset($invoice_details->po_id)?$invoice_details->po_id:''?>">
                        <input type="hidden" id="supplier_id" name="supplier_id" value="<?php echo isset($invoice_details->supplier_id)?$invoice_details->supplier_id:''?>">

                    </div>
                	<div class="col-md-12 top10 clearfix">
                		<div class="widget">
                        	<div class="widget-head">
								Items				
							</div>
							<div class="widget-content">
                                	<table class="table table-bordered">
                                		<thead>
                                			<th>Description</th>
                                			<th>Quantity</th>
                                			<th></th>
                                		</thead>
                                		<tbody id="i_items">
                                			<tr id="tr_newitem">
                                				<td>
                                					<input id="description" autocomplete="off" class="form-control" type="text" placeholder="Search by item description" />
                                					<div class="selectHolder" id="selectHolderDesc"></div>
                                				</td>
                                				<td><input class="inputsmall form-control" id="item_qty" type="text" /></td>
                                				<td class="actions">
                                					<input type="hidden" id="item_id">
                                					<a class="btn btn-xs btn-success" id="additem" role="button">
                                          				<i class="fa fa-check"></i>
                                        			</a>
                                        		</td>
                                			</tr>
                                			<?php if(isset($invoice_items)){
                                					foreach($invoice_items as $i_item):?>
                                					<tr class="iitems" id="iitem_<?php echo $i_item->iitem_id?>">
														<td><?php echo $i_item->description?></td>
														<td><?php echo $i_item->quantity?></td>
														</td>
														<td class="actions">
															<a class="btn btn-xs btn-danger deleteitem" id="deleteitem_<?php echo $i_item->iitem_id?>" role="button">
												                <i class="fa fa-times delete"></i>
												            </a>
												        </td>
													</tr>
                                			<?php 	endforeach;
                                				  } ?>
                                		</tbody>
                                	</table>	
                            </div>
                        </div>
                        <p class='error'></p>
                        
	                    <div class="pull-right submit form-group">
		                    <input type="submit" class="btn btn-default" name="save" value="Save">&nbsp;
		                    <input type="submit" class="btn btn-success" name="publish" id="publish" value="Proceed to P.I">
	                    </div>
                    </div>
                    <input type="hidden" name="draft" id="draft"/>
                </form>
            </div>
        </div>
        <!-- ==================== END OF SPAN12 FLOATING BOX ==================== --> 

    </div>

</div>
<!-- ==================== END OF ROW ==================== -->
               

</div>
<div id="saved" class="label label-default">Saved</div>
<script>
$(document).ready(function(){
	$('#selcustomer').keyup(function(event) {
		var customer = $('#selcustomer').val();
		$('.selectHolder').width($('#selcustomer').width()+24);
		$('#selectHolderSup').show();
		$.ajax({
	        type: "POST",
	        url: "<?php echo base_url() ?>purchasing/searchSupplier",
	        data: {customer:customer}
	      }).done(function( msg ) {
	      		var content='<div><ul id="searchcustomer">';
	      		msg = $.parseJSON(msg);
	      		$.each(msg,function(key,value) {
	      			content+='<li data-selected='+value.supplier_id+' class="searchcustomeropt">'+value.name+'</li>';
	      		});
	      		content+='</ul></div>';
	         	$('#selectHolderSup').html(content);
	      });
	});

	<?php if(!$this->uri->segment(3)) {?>
    $('#draft').val('draft');
	    setInterval(function(){
	      $.ajax({
	        type: "POST",
	        url: $('#cs_form').attr('action'),
	        data: $('#cs_form').serialize()
	      }).done(function( msg ) {
	          if(msg>0) {
	            $('#saved').show();
	          }
	      });
	      $('#saved').hide();
	    },10000);
	<?php }?>


	$('.submit :submit').click(function(){
		if(!$('#supplier_id').val())
		{
			$('.error').html('Please select a supplier.');
			return false;
		}
		if(!($('#i_items tr').length>1))
		{
			$('.error').html('You have to select at least one item!');
			return false;
		}
	});

	$('#selcustomer').keypress(function(){
		$('.error').html('');
	});

	$('#selcustomer').change(function(){
		$('.error').html('');
		
	});

	$('input[id^=supplier_]').click(function(){
		$('.error').html('');
	});

	$('body').on('click','.searchcustomeropt', function (){
		var selected = $(this).data('selected');
		$.ajax({
		  type: "POST",
		  url: "<?php echo base_url() ?>system_setup/getSuppliersDetails/"+selected
		}).done(function( msg ) {
			var data=JSON.parse(msg);
			$('.selectHolder').hide();
			$('.filled').show();
		  	$('#customer_name').html(data.name);
		  	$('#customer_email').html(data.email);
		  	$('#customer_address').html(data.address);
	  		$('#customer_phone').html(data.phone);
	  		$('#supplier_id').val(data.supplier_id);
		});
	});

	$('#stock_num').change(function(){
		var stocknum=$(this).val();
		if(stocknum){
			$.ajax({
			  type: "POST",
			  url: "<?php echo base_url() ?>retail/searchByStockNum/"+stocknum
			}).done(function( msg ) {
				var data=JSON.parse(msg);
				$('#item_id').val(data.item_id);
			  	$('#description').val(data.description);
			  	$('#item_cost').val(data.cost_price);
			  	$('#item_sell').val(data.sell_price);
			  	$('#item_cost_val').val(data.cost_price);
			});
		}
	});

	$('#stock_num').click(function(){
		$('#selectHolderDesc').hide();
	});

	$('#description').keyup(function(){
		var description=$(this).val();
		if(description){
			$('.selectHolder').width($('#description').width()+24);
			$('#selectHolderDesc').show();
				$.ajax({
		            type: "POST",
		            url: "<?php echo base_url() ?>retail/searchByDescription/",
		            data: {description:description}
		          }).done(function( msg ) {
		            $('#searchdesc').html(msg);
					$('#modalSearchStock').modal('show');
		        });
	    }
	});

	$('body').on('click','.searchdescopt', function (){
		var selected = $(this).data('selected');
		$.ajax({
		  type: "POST",
		  url: "<?php echo base_url() ?>retail/searchByItemId/",
		  data: {selected:selected}
		}).done(function( msg ) {
			var data=JSON.parse(msg);
			$('#modalSearchStock').modal('hide');
			$('#item_id').val(data.item_id);
		  	$('#description').val(data.description);
		  	$('#item_cost').val(data.cost_price);
		  	$('#item_cost_val').val(data.cost_price);
		});
	});

	$('#additem').click(function(){
		var item_id = $('#item_id').val();
		if(item_id!='') {
			$('.error').html('');
			$.ajax({
				  type: "POST",
				  url: "<?php echo base_url() ?>purchasing/addPOItem/",
				  data: {invoice_id:$('#invoice_id').val(),item_id:$('#item_id').val(),qty:$('#item_qty').val()}
				}).done(function( msg ) {
					if(msg=="All fields are mandatory!")
					{
						$('.error').html('All fields are mandatory!');
						return false;
					}
					else{
						var result=JSON.parse(msg);
						$('#i_items').append(result.content);
						$('#invoice_id').val(result.po_id);
					  	$('#tr_newitem input').val('');
				    }
				});

		} else {
			$('.error').html('Please select an item.');
		}
		
	});


	$('body').on('click','.deleteitem',function(){
		var id=$(this).attr('id');
		var arr=id.split('_');
		$.ajax({
		  url: "<?php echo base_url() ?>purchasing/deletePoItem/"+arr[1],
		}).done(function( remove_id ) {
			$('#iitem_'+remove_id).remove();
		});
	});

});

</script>
<?php $this->load->view('templates/footer');?>