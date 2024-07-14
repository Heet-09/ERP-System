<?php /* ?>		
		<section class="panel">
			<header class="panel-heading">
				
		
				<h2 class="panel-title"><?php echo isset($k_table_title)?$k_table_title : "Table Title"; ?></h2>
			</header>
			<div class="panel-body">
				<table class="table table-bordered table-striped mb-none" id="datatable-tabletools" data-swf-path="assets/vendor/jquery-datatables/extras/TableTools/swf/copy_csv_xls_pdf.swf">
					<thead>
							<?php echo isset($k_table_headings)?$k_table_headings:"<tr><th></th></tr>"; ?>
					</thead>
					<tbody>
							<?php echo isset($k_table_body)?$k_table_body:"<tr><td></td></tr>"; ?>
					</tbody>
				</table>
			</div>
		</section> 
		
		 <?php */ ?>	
		 <script>
   function exportTableToExcel(datatable, filename = '')
			{
				var downloadLink;
				var dataType = 'application/vnd.ms-excel';
				var tableSelect = document.getElementById(datatable);
				var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
				
				// Specify file name
				filename = filename?filename+'.xls':'excel_data.xls';
				
				// Create download link element
				downloadLink = document.createElement("a");
				
				document.body.appendChild(downloadLink);
				
				if(navigator.msSaveOrOpenBlob){
					var blob = new Blob(['\ufeff', tableHTML], {
						type: dataType
					});
					navigator.msSaveOrOpenBlob( blob, filename);
				}else{
					// Create a link to the file
					downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
				
					// Setting the file name
					downloadLink.download = filename;
					
					//triggering the function
					downloadLink.click();
				}
			}
</script>
		<section class="panel">
			<header class="panel-heading">
				<div class="panel-actions">
					<!--a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a-->
					<?php 
						$k_table_button_link2 = isset($k_table_button_link2)? $k_table_button_link2 : '#';
						if(isset($k_table_button2)){
							echo "<a href=". $k_table_button_link2 ."><button class='btn btn-danger' name='addnew'>". $k_table_button2 ."</button></a>";
						}
					?>
					<?php 
						$k_table_button_link = isset($k_table_button_link)? $k_table_button_link : '#';
						if(isset($k_table_button)){
							echo "<a href=". $k_table_button_link ."><button class='btn btn-danger' name='addnew'>". $k_table_button ."</button></a>";
						}
					?>
				    <button onclick="exportTableToExcel('datatable')" class="btn btn-primary bizbtn" style="font-size:10px;">EXCEL</button>	
				</div>
				<h2 class="panel-title"><?php echo isset($k_table_title)?$k_table_title : "Table Title"; ?></h2>
				<p class="panel-subtitle"><?php echo isset($k_table_subtitle) ? $k_table_subtitle : ""; ?></p>
			</header>
			<div class="panel-body" id="datatable">
					<!--table class="table table-bordered table-striped mb-none" id="datatable" data-swf-path="assets/vendor/jquery-datatables/extras/TableTools/swf/copy_csv_xls_pdf.swf"-->
					 <table class="table table-bordered table-striped mb-none" id="datatable-tabletools" data-swf-path="assets/vendor/jquery-datatables/extras/TableTools/swf/copy_csv_xls_pdf.swf">
					<!--table class="table table-bordered table-striped mb-none" id="datatable-default"-->
					<thead>
							<?php echo isset($k_table_headings)?$k_table_headings:"<tr><th></th></tr>"; ?>
					</thead>
					<tbody>
							<?php echo isset($k_table_body)?$k_table_body:"<tr><td></td></tr>"; ?>
					</tbody>
				</table>
			</div>
		</section>