<?php
/**
 * Plugin Name: EduGorilla
 * Description: Add Lead and search.
 * Version: 1.0.0
 * Author: Tarun Kumar
 * Author URI: http://www.facebook.com/tjtarunkumar
 * */
	function create_edugorilla_lead_table()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name1 = $wpdb->prefix . 'edugorilla_lead_contact_log'; //Defining a table name.
		$sql1 = "CREATE TABLE $table_name1 (
											id int(11) NOT NULL AUTO_INCREMENT,
											name varchar(200) NOT NULL,
											contact_no varchar(50) NOT NULL,
											email varchar(200) NOT NULL,
											query text(500) NOT NULL,
                                            date_time varchar(200) NOT NULL,
											PRIMARY KEY id (id)
										) $charset_collate;"; //Defining query to create table.
    
    
    	$table_name2 = $wpdb->prefix . 'edugorilla_lead'; //Defining a table name.
		$sql2 = "CREATE TABLE $table_name2 (
											id int(11) NOT NULL AUTO_INCREMENT,
                                            contact_log_id int(11) NOT NULL,
                                            category_id text(500) NOT NULL,
											institute_name varchar(200) NOT NULL,
                                            institute_address text NOT NULL,
                                            email_status text NOT NULL,
                                            sms_status text NOT NULL,
                                            date_time varchar(200) NOT NULL,
											PRIMARY KEY id (id)
										) $charset_collate;"; //Defining query to create table.
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    	//Creating a table in cureent wordpress
		dbDelta($sql1);
    	dbDelta($sql2);
	}
	register_activation_hook( __FILE__, 'create_edugorilla_lead_table' );
	
	add_action("admin_menu","create_menus");
	
	function create_menus() 
	{
			add_object_page(
							'EduGorilla',
							'EduGorilla',
							'read',
							'edugorilla',
							'edugorilla'
						);
    
    	    add_submenu_page(
                             'edugorilla',
                             'EduGorilla',
                             'EduGorilla',
                             'read',
                             'edugorilla',
                             'edugorilla'
                            );
						
			add_submenu_page(
                             'edugorilla',
                             'EduGorilla | Listing',
                             'List',
                             'read',
                             'Listing',
                             'form_list'
                            );
    		add_submenu_page(
                          	 'edugorilla',
                         	 'EduGorilla | OTP',
                          	 'OTP',
                         	 'read',
                         	 'edugorilla-otp',
                         	 'edugorilla_otp'
                       	 );
	}


	include_once plugin_dir_path( __FILE__ )."otp.php";

	function edugorilla()
	{
		$caller = $_POST['caller'];
		
		if($caller=="self")
		{
        	/** Get Data From Form **/
			$name = $_POST['name'];
			$contact_no = $_POST['contact_no'];
        	$keyword = $_POST['keyword'];
			$email = $_POST['email'];
			$query = $_POST['query'];
			$category_id = $_POST['category_id'];
        	$edugorilla_institute_datas = $_POST['edugorilla_institute_datas'];
        
        	/** Error Checking **/
			$errors = array();
			if(empty($name))$errors['name']="Empty";
			elseif(!preg_match("/([A-Za-z]+)/", $name)) $errors['name']="Invalid";
        
        	if(empty($keyword)) $errors['keyword']="Empty";
			
			if(empty($contact_no))$errors['contact_no']="Empty";
			elseif(!preg_match("/([0-9]{10}+)/", $contact_no)) $errors['contact_no']="Invalid";
			
			if(empty($email))$errors['email']="Empty";
			elseif(filter_var($email, FILTER_VALIDATE_EMAIL) === false) $errors['email']="Invalid";
			
			if(empty($query))$errors['query']="Empty";
			
			
			
			if(empty($errors))
			{
            	$institute_emails_status = array();
            
            	$json_results =json_decode(str_replace("\\","",$edugorilla_institute_datas));
            
            	foreach($json_results as $json_result)
                {
                	 $institute_emails = explode(",",$json_result->emails);
               		 foreach($institute_emails as $institute_email)
               		 {
                		if(!empty($institute_email))
            			$institute_emails_status[$institute_email] = wp_mail($institute_email , "Hi", "Hi ".$name);
            	   	 }
                	
                	if(!empty($category_id)) $category = implode(",",$category_id);
                	else $category = "";
                
						global $wpdb;
                		$wpdb->insert( 
										$wpdb->prefix . 'edugorilla_lead_contact_log', 
									array( 
											'name' => $name,
											'contact_no' => $contact_no,
											'email' => $email,
											'query' => $query,
                            				'date_time' => current_time('mysql')
										)
						);
                		
                		$contact_log_id = $wpdb->insert_id;
                
						$result = $wpdb->insert( 
										$wpdb->prefix . 'edugorilla_lead', 
									array( 
                                    		'contact_log_id' => $contact_log_id,
                            				'category_id' => $category,
                           			 		'institute_name' => $json_result->title,
                         			   		'institute_address' => $json_result->address,
                            				'email_status' => json_encode($institute_emails_status),
                            				'date_time' => current_time('mysql')
										)
						);
                	
                }
            
            	if($result)
					$success="Saved Successfully";
            	else $success = $result;
            	
			//	foreach($_REQUEST as $var=>$val)$$var="";
			}
		}
?>
    <style>
  
     #map {
        width: 60%;		
        height: 500px;
        border:double;
       }
      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }
    </style>
	

		<div class="wrap">
				<h1>EduGorilla Leads</h1>
			<?php
			if($success)
			{
			?>
				<div class="updated notice">
					<p><?php echo $success;?></p>
				</div>
			<?php
			}
			?>
				<form  name=details method="post">
				 <table class="form-table">
					 <tr>
						 <th>Name<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="name" value="<?php echo $name;?>" placeholder="Type name here...">
							<font color="red"><?php echo $errors['name'];?></font>
						</td>
					</tr>
					 <tr>
						 <th>Contact No.<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="contact_no" value="<?php echo $contact_no;?>" placeholder="Type contact number here">
							<font color="red"><?php echo $errors['contact_no'];?></font>
						</td>
					</tr>
					<tr>
						 <th>Email<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="email" value="<?php echo $email;?>" placeholder="Type email here">
							<font color="red"><?php echo $errors['email'];?></font>
						</td>
					</tr>
					<tr>
						 <th>Query<sup><font color="red">*</font></sup></th>
						 <td>
							<textarea name="query" placeholder="Type your query here"><?php echo $query; ?></textarea>
							<font color="red"><?php echo $errors['query'];?></font>
						</td>
					</tr>
                 	<tr>
						 <th>Listing Type<sup><font color="red">*</font></sup></th>
						 <td>
							<select name="listing_type" id="edugorilla_listing_type">
                            	<option value="">Select</option>
                        	</select>
						</td>
					</tr>
                 		<tr>
						 <th>Category<sup><font color="red">*</font></sup></th>
						 <td>
							<select disabled name="category_id[]" multiple id="edugorilla_category" class="js-example-basic-single">
								<?php 
    									$temparray = array();
    									$categories = get_terms('listing_categories', array('hide_empty' => false));
    									
    									foreach ($categories as $category) {
                                        	if((int)$category->parent != 0)
                                            {
                                            	$temparray[$category->parent][$category->term_id] = $category->name;
                                            }
                                        }
    					
										foreach ($temparray as $var=>$vals ) {
                                    ?>
                                 
                                        <option value="<?php echo $var; ?>">
                                   	<?php 
                                    	$d = get_term_by('id', $var, 'listing_categories');
                                        echo $d->name;
                                   ?>
                                   		</option>
 										
                            		<?php
											foreach($vals as $index=>$val)
                                            {
                                     ?>
                                        		
                                                <option value="<?php echo $index; ?>">
                                   					<?php echo $val; ?>
                                        		</option>
                                     <?php
                                            }
                                       ?>
                                   		
                                <?php
										}
								?>
							</select>
							<font color="red"><?php echo $errors['category_id'];?></font>
						</td>
					</tr>
                 	 <tr>
						 <th>Keyword<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="keyword" id="edugorilla_keyword" disabled value="<?php echo $keyword;?>" placeholder="Type keyword here">
							<font color="red"><?php echo $errors['keyword'];?></font>
						</td>
					</tr>
                 	<tr>
						 <th>Location<sup><font color="red">*</font></sup></th>
						 <td>
                         	<input id="pac-input" name="location" class="controls" type="text" placeholder="Enter a location">
                             <div id="map"></div>
						</td>
					</tr>
					 <tr>
						<th>
                        	<input type="hidden" id="edugorilla_institute_datas" name="edugorilla_institute_datas">
							 <input type="hidden" name="caller" value="self">
						</th>
						<td>
                        	
                        	<a id ="save_details_button" href="#confirmation" rel="modal:open" class="button button-primary">Send Details</a>
						</td>
					 </tr>
				 </table>
			 </form>
		</div>

<!-------Modal------>
<div id="confirmation" style="display:none;">
  
</div>
<!---/Modal-------->
<script>

 function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -33.8688, lng: 151.2195},
          zoom: 13
        });
		
        var input = /** @type {!HTMLInputElement} */(
            document.getElementById('pac-input'));

        var types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
          map: map,
          anchorPoint: new google.maps.Point(0, -29)
        });
		        google.maps.event.addListener(map, 'center_changed', function () {
            var location = map.getCenter();
            document.getElementById("latitude").value = location.lat();

            document.getElementById("longitude").value = location.lng();          
        });

        autocomplete.addListener('place_changed', function() 
		{
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }
		  

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
		  
          marker.setIcon(/** @type {google.maps.Icon} */({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
          }));
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
          infowindow.open(map, marker);
		  
        });

			
        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
          var radioButton = document.getElementById(id);
          radioButton.addEventListener('click', function() {
            autocomplete.setTypes(types);
          });
        }

        setupClickListener('changetype-all', []);
        setupClickListener('changetype-address', ['address']);
        setupClickListener('changetype-establishment', ['establishment']);
        setupClickListener('changetype-geocode', ['geocode']);
      }

</script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6v5-2uaq_wusHDktM9ILcqIrlPtnZgEk&libraries=places&callback=initMap"
        async defer></script>
    
<?php
	}


	function script() 
	{  
    wp_enqueue_style( 'select2-css', plugins_url( '/css/select2.css', __FILE__ ));
    wp_enqueue_style( 'modal-css', plugins_url( '/css/jquery.modal.css', __FILE__ ));
    wp_enqueue_style('jquery-ui-styles', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css");

     wp_enqueue_script( 
        'select2-script',                         // Handle
        plugins_url( '/js/select2.js', __FILE__ ),  // Path to file
        array( 'jquery' )                             // Dependancies
    );
    wp_enqueue_script( 
        'modal-script',                         // Handle
        plugins_url( '/js/jquery.modal.js', __FILE__ ),  // Path to file
        array( 'jquery' )                             // Dependancies
    );
    wp_enqueue_script( 
        'script',                         // Handle
        plugins_url( '/js/script.js', __FILE__ ),  // Path to file
        array( 'jquery','jquery-ui-datepicker' )                             // Dependancies
    );
   
   }

add_action( 'admin_enqueue_scripts', 'script', 2000 );


function edugorilla_show_location() {
	$term = strtolower($_REQUEST['term']);
	$ptype = strtolower($_REQUEST['ptype']);
	$address = $_REQUEST['address'];
	$category = $_REQUEST['category'];
	
	$args = array();
	
	$args['post_status'] = 'publish';
	if(!empty($ptype))  $args['post_type'] = $ptype;
	if(!empty($term))  $args['s'] = $term;
	if(!empty($address))
    {	
    	//$address = "%".$address."%";
    	$args['meta_query'] = array(
    									array(
												'key'     => 'listing_address',
												'value'   => $address,
												'compare' => 'LIKE'
											)
    								);
	}

	if(!empty($category)) $args['cat'] = $category;  
	//var_dump($args);
	$eduction_posts = array();
	$the_query = new WP_Query( $args );
	if($the_query->have_posts() )
	{
    	while ( $the_query->have_posts() )
        {
        	$the_query->the_post();
        	$emails = array();
			$phones = array();
        	$eduction_post = array();
        	$eduction_post['title'] = get_the_title();
        	if(get_post_meta( get_the_ID(), 'listing_address', true )) $eduction_post['address'] = get_post_meta( get_the_ID(), 'listing_address', true );
        	else  $eduction_post['address'] = "Unavailable";
        
        //check whether email values ara available or not.
        	if(get_post_meta( get_the_ID(), 'listing_email', true )) $emails[]= get_post_meta( get_the_ID(), 'listing_email', true );
        	if(get_post_meta( get_the_ID(), 'listing_alternate_email', true )) $emails[]= get_post_meta( get_the_ID(), 'listing_alternate_email', true );
        	        
        	$eduction_post['emails'] = implode(", ",$emails);
        
        //check whether phone numbers are available or not.
        	if(get_post_meta( get_the_ID(), 'listing_phone', true ))  $phones[]= get_post_meta( get_the_ID(), 'listing_phone', true );
        	if(get_post_meta( get_the_ID(), 'listing_phone2', true )) $phones[]= get_post_meta( get_the_ID(), 'listing_phone2', true );
        	if(get_post_meta( get_the_ID(), 'listing_phone3', true )) $phones[]= get_post_meta( get_the_ID(), 'listing_phone3', true );
        	if(get_post_meta( get_the_ID(), 'listing_phone4', true )) $phones[]= get_post_meta( get_the_ID(), 'listing_phone4', true );
        	if(get_post_meta( get_the_ID(), 'listing_phone5', true )) $phones[]= get_post_meta( get_the_ID(), 'listing_phone5', true );
            
        	$eduction_post['phones'] = implode(", ",$phones);
        	
        	$eduction_post['lat'] = get_post_meta( get_the_ID(), 'listing_map_location_latitude', true );
            $eduction_post['long'] = get_post_meta( get_the_ID(), 'listing_map_location_longitude', true );
        
        	$eduction_posts[] = $eduction_post;
        }
    }
		wp_reset_query();
		
    	//$response = json_encode(array_values($eduction_posts));
		$response = json_encode($eduction_posts);
    	echo $response;
    	exit();

}

add_action( 'wp_ajax_edugorilla_show_location', 'edugorilla_show_location' );
add_action( 'wp_ajax_nopriv_edugorilla_show_location', 'edugorilla_show_location' );

include_once plugin_dir_path( __FILE__ )."list.php";
?>