<?php
function form_list()
{
    global $wpdb;
	$table_name = $wpdb->prefix . 'edugorilla_lead ';
    $count_query = $wpdb->get_results( "SELECT * FROM $table_name" );
    $num_rows = count($count_query); //PHP count()
	$cpage = $_REQUEST['cpage'];
	$list_caller = $_REQUEST['list_caller'];
	$current_page=1;
	$page_size=10;
    if($num_rows%$page_size==0)
         $total_pages=$num_rows/$page_size;
    else
         $total_pages=intval($num_rows/$page_size)+1;
         $index=($current_page-1)*$page_size;
     global $wpdb;
	$search_from_date_form = $_POST['search_from_date_form'];
	if($search_from_date_form == "self")
    {
    	$edugorilla_list_date_from = $_POST['edugorilla_list_date_from'];
    	$edugorilla_list_date_to = $_POST['edugorilla_list_date_to'];
    	$q = "select * from {$wpdb->prefix}edugorilla_lead WHERE (date_time BETWEEN '$edugorilla_list_date_from%' AND '$edugorilla_list_date_to%') order by id";	
    }
	else
    {
	 $q = "select * from {$wpdb->prefix}edugorilla_lead order by id";
    }
	 $leads_datas = $wpdb->get_results( $q, 'ARRAY_A' );
	 
	 for($i=1; $i<=$total_pages; $i++)
    {
	 if($i==$current_page)
	 $p.= "<option value='$i' selected> $i </option>";
	 else
	 $p.= "<option value='$i'>$i</option>";
    }
    ?>
    <div class="wrap">
	     <h1>Leads List <a href="admin.php?page=edugorilla"  class="button button-primary">Add</a></h1>
	     <center><h4><?php echo $_REQUEST['success']; ?></h4></center>
	<table class="widefat fixed" cellspacing="0">
    <thead>
    	<div class="alignleft actions bulkactions">
    		<form method="post">
    			 <select name="choice">
	     			<option value="">Select</option>
	     			<option value="delete">Delete</option>
	 			</select>&nbsp;&nbsp;
            	<input type="hidden" name="list_caller" value="self">
				<input class="button action" type="submit" value="OK" id="delete">
    		</form>
    	`</div>
    		<form method="post">
        		<label>Date From</label><input name="edugorilla_list_date_from" id="edugorilla_list_date_from">
        		<label>Date To</label><input name="edugorilla_list_date_to" id="edugorilla_list_date_to">
            	<input type="hidden" name="search_from_date_form" value="self">
        		<input type="submit" class="button action" value="OK">
       		</form>
    	<div class="alignright actions bulkactions">
        
        		<form name=f10 action="admin.php?page=Listing&current_page=$cpage&page_size=$page_size">
                	<label>Page No. </label>
    					<select name="cpage" onchange='document.f10.submit();'>
                	    	<?php echo $p; ?>
    					</select>
    				</form>
    	</div>
    <tr>
		<th id="cb" class="manage-column column-cb check-column" scope="col"><input id="cb-select-all-1" style="margin-top:16px;" type="checkbox"></th> 
		<th id="columnname" class="manage-column column-columnname" scope="col">Institute Name</th>
		<th id="columnname" class="manage-column column-columnname" scope="col">Phone No./Status</th> 
		<th id="columnname" class="manage-column column-columnname" scope="col">Email/Status</th> 
    	<th id="columnname" class="manage-column column-columnname" scope="col">Email Count(s)</th>
    	<th id="columnname" class="manage-column column-columnname" scope="col">Date Time</th>
    </tr>
    </thead>
    <tfoot>
     <tr>
		<th id="cb" class="manage-column column-cb check-column" scope="col"><input id="cb-select-all-1" style="margin-top:16px;" type="checkbox"></th> 
		<th id="columnname" class="manage-column column-columnname" scope="col">Institute Name</th>
		<th id="columnname" class="manage-column column-columnname" scope="col">Phone No./Status</th> 
		<th id="columnname" class="manage-column column-columnname" scope="col">Email/Status</th>
     	<th id="columnname" class="manage-column column-columnname" scope="col">Email Count(s)</th>
    	<th id="columnname" class="manage-column column-columnname" scope="col">Date Time</th>
    </tr>
    </tfoot>
    <tbody>
    <?php
		$lead_names = array();
		foreach($leads_datas as $leads_data)
        {
        	$lead_names[] = $leads_data['institute_name'];
        }

		foreach($leads_datas as $leads_data)
      {
      	$category = "";
      	$term_ids = explode(",",$leads_data['category_id']);
      	$total_terms = count($term_ids);
      	if(!empty($term_ids))
      	{
      		foreach($term_ids as $index=>$term_id)
       		{
      	 		$category_data =  get_term_by('id', $term_id, 'listing_categories');
            	if($index == $total_terms-1)
        			$category .= $category_data->name;
            	else
                	$category .= $category_data->name.",";
        	}
        }
    ?>
	        <tr class="alternate" valign="top"> 
	            <th class="check-column" scope="row"><input id="cb-select-all-1"  type="checkbox" name="check_list[]" value="<?php echo $leads_data['id'];?>"></th>
	            <td class="column-columnname"><?php echo $leads_data['institute_name'];?>
	                <div class="row-actions">
	                    <span><a href="admin.php?page=edugorilla-edit&tid=<?php echo $leads_data['id'];?>">  Edit</a> | </span>
                    	<span><a href="admin.php?page=edugorilla-view&tid=<?php echo $leads_data['id'];?>">  View</a> | </span>
	                </div>
	            </td>
	            <td class="column-columnname"><?php echo $leads_data['sms_status'];?></td>
	            <td class="column-columnname">
                	<?php
    						$emails_confrms = json_decode($leads_data['email_status'],true);
        				
        					$email_count = array_count_values($lead_names);
    						if(!empty($emails_confrms))
                            {
    							foreach($emails_confrms as $email => $status)
                            	{
                            		if($status == true) $staus = "Success";
                            		else  $staus = "Unsuccess";
                            		echo $email."/".$staus.",<br>";
                            	}
                            }
                	?>
            	</td>
            	<td class="column-columnname"><?php echo $email_count[$leads_data['institute_name']];?></td>
            	<td class="column-columnname"><?php echo $leads_data['date_time'];?></td>
	        </tr>
 <?php } ?>
    </tbody>
  </table>
  <?php 
if($list_caller == "self")
{
$option = isset($_POST['choice']) ? $_POST['choice'] : false;
if ($option) 
{
   $checkbox = $_POST['check_list']; 
   for($i=0;$i<count($checkbox);$i++)
   {
       $del_id = $checkbox[$i];
       global $wpdb;
       $result = $wpdb->delete( 
       wp_edugorilla_lead,   
       array( 'id' => $del_id ), 
       array( '%d' ));
    }
}
}
?>
</div>
<?php
}
?>