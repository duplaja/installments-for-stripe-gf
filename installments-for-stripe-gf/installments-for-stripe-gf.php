<?php
/**
* Plugin Name: Installments for Stripe Gravity Forms
* Plugin URI: https://www.wptechguides.com
* Description: A plugin for Gravity Forms and Stripe Integration (official) to allow installment payments
* Version: 1.1
* Author: duplaja (Dan D.)
* Author URI: https://www.convexcode.com
**/


//checks to see if the GF Stripe official plugin is activated
function installments_stripe_gf_activate() {
  


    if( !class_exists( 'GF_Stripe_Bootstrap' ) ) {
	
		 deactivate_plugins( plugin_basename( __FILE__ ) );
         wp_die( __( 'Please activate Gravity Forms + the official Stripe Plugin for Gravity Forms.', 'installments-for-stripe-gf' ), 'Plugin dependency check', array( 'back_link' => true ) );
        
    }
}

register_activation_hook(__FILE__, 'installments_stripe_gf_activate');

//adds tab on GF Settings page
add_filter( 'gform_settings_menu', 'installments_stripe_gf_settings_tab' );
function installments_stripe_gf_settings_tab( $tabs ) {
    $tabs[] = array( 'name' => 'stripe_installments', 'label' => 'Stripe Installments' );
    return $tabs;
}

//adds content to gform settings
add_action( 'gform_settings_stripe_installments', 'installments_stripe_gf_display_settings', 10, 1 );


//on-load, sets up the following settings for the plugin
add_action( 'admin_init', 'gfstripe_installment_settings' );

function gfstripe_installment_settings() {
	register_setting( 'gfstripe-installment-settings-group', 'gfstripe_installment_feeds' ); //feeds desired to check.
	register_setting( 'gfstripe-installment-settings-group', 'gfstripe_installment_num_total' ); //number of times before canceling
}

//Returns all active stripe subscription feedNames as an array.
//returns No Feeds Found if none exist.
function installments_stripe_gf_feedNames_list(){

		GLOBAL $wpdb;
		$sql = $wpdb->prepare( "SELECT meta FROM {$wpdb->prefix}gf_addon_feed WHERE addon_slug='gravityformsstripe' AND is_active='1'",'foo');
		$all_meta = $wpdb->get_col( $sql );
	
		$count_feeds = count($all_meta);
		$inner_sel = '';
		
		for ($i=0;$i<$count_feeds;$i++){
		
			$meta_array_tmp = json_decode($all_meta[$i],true);

			if ($meta_array_tmp['transactionType'] == 'subscription') {
			
				$valid_feedNames_array[] = $meta_array_tmp['feedName'];
				$inner_sel = 'set';
			
			}
		
		}
	
		if (empty($inner_sel)) {
		
			$valid_feedNames_array[] = "No Feeds Found";
		}
		
		return $valid_feedNames_array;
}

//returns a select dropdown with the name (array), list of names, and the default option
function installments_stripe_gf_return_select_feedNames($select_name, $valid_feedNames_array, $preset_feedName){
	

	$select_feedNames = '<select name="'.$select_name.'[]"><option value=""></option>';
	
	$count_feeds = count($valid_feedNames_array);
	
	for ($i=0;$i<$count_feeds;$i++){
		
		if ($preset_feedName == $valid_feedNames_array[$i]) {
			
			$select_feedNames.= '<option value="'.$valid_feedNames_array[$i].'" selected="selected">'.$valid_feedNames_array[$i].'</option>';
			
		} else {
			$select_feedNames.= '<option value="'.$valid_feedNames_array[$i].'">'.$valid_feedNames_array[$i].'</option>';
		}
	}
	
	$select_feedNames.="</select>";
	
	return $select_feedNames;
	
}

//displays the settings page
function installments_stripe_gf_display_settings() {

	$valid_feedNames_array = installments_stripe_gf_feedNames_list();
	
	$no_default_select = installments_stripe_gf_return_select_feedNames('gfstripe_installment_feeds', $valid_feedNames_array,'none');
	

	//form to save feed names and number of iterations desired
	echo "<form method=\"post\" action=\"options.php\">";

	settings_fields( 'gfstripe-installment-settings-group' );
	do_settings_sections( 'gfstripe-installment-settings-group' );

	echo "<script>function addRow(nextnum,nextdisp){


	var toremove = 'addrowbutton';
	var elem = document.getElementById(toremove);
     elem.parentNode.removeChild(elem);

	var table = document.getElementById(\"gfstripe-installment-settings\");
	var row = table.insertRow(-1);
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);
	c1var = '<b>Sub Feed Name</b>';
	cell1.innerHTML = c1var;
	var newnextdisp= nextdisp+1;
	
	c2var = '".$no_default_select."';

	cell2.innerHTML = c2var;

	c3var = 'Times to Charge: <input type=\"number\" name=\"gfstripe_installment_num_total[]\" min=\"0\" value=\"0\">';

	cell3.innerHTML = c3var;

	c4var = '<button type=\"button\" id=\"addrowbutton\" onClick=\"addRow('+nextdisp+','+newnextdisp+')\">Add Row</button>';
	cell4.innerHTML = c4var;
}</script>

";

	//paragraph giving plugin explanation, api setup instructions, and shortcode information
    	echo "	
	<div><h1>Installments For GF Stripe</h1>

	<p>Welcome! This is an add-on to allow installments / set length subscriptions for Gravity Forms + the official GF Stripe Add-on. <br><br>To delete an installments setting, simply change the number of times to charge to 0, and then save changes.
	<br>";


	//Settings to be saved
	echo "
	<table id=\"gfstripe-installment-settings\" class=\"form-table\" aria-live=\"assertive\">
		<tr><td colspan=\"2\"><h2>Stripe Feeds</h2></td></tr>";

	$gfstripe_installment_feeds = get_option('gfstripe_installment_feeds');
	$gfstripe_installment_num_total = get_option('gfstripe_installment_num_total');
	$num_feeds = 0;
	$num_feeds = count($gfstripe_installment_feeds);

	if ($num_feeds > 1){

		$showrows=$num_feeds; 
	} else {
		$showrows = 1;
	}

	for ($i=0;$i < $showrows; $i++) {
		$nextid = $i+1;
		$nextdisp = $i+2;
		$feednum = $i+1;
	
		if (($gfstripe_installment_num_total[$i] < 1 || $gfstripe_installment_num_total[$i] == '') && $showrows > 1) {

		} else {

			echo " 
     		<tr valign=\"top\">
     		<th scope=\"row\">Sub Feed Name</th>
        		<td>".installments_stripe_gf_return_select_feedNames('gfstripe_installment_feeds', $valid_feedNames_array, $gfstripe_installment_feeds[$i])."</td><td>Times to Charge: <input name=\"gfstripe_installment_num_total[]\" type=\"number\" min=\"0\" value=\"$gfstripe_installment_num_total[$i]\">

			</td>
			<td>";

			if (($showrows -1) == $i) {

				echo "<button type=\"button\" id=\"addrowbutton\" onClick=\"addRow($nextid,$nextdisp)\">Add Row</button>";

			}

			echo "</td></tr>";
		}
	}
       
	echo" </table>";
    
	submit_button();

	echo "</form>";
	
	
	
}



//action checks on payment recieved confirmation
add_action( 'gform_post_add_subscription_payment', function ( $entry ) {
    if ( rgar( $entry, 'payment_status' ) == 'Active' ) {
		
		$feed = gf_stripe()->get_payment_feed( $entry );

		$feed_name  = rgars( $feed, 'meta/feedName' );
		
		settings_fields( 'gfstripe-installment-settings-group' );
		
		$gfstripe_installment_feeds = get_option('gfstripe_installment_feeds');
		$gfstripe_installment_num_total = get_option('gfstripe_installment_num_total');
		$num_feeds = 0;
		$num_feeds = count($gfstripe_installment_feeds);
		
		for ($i=0;$i < $num_feeds; $i++) {
		
			$feed_name_to_check = array($gfstripe_installment_feeds[$i]); 
		
			if ( in_array( $feed_name, $feed_name_to_check ) ) {
				
				
				
				global $wpdb;
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(id) FROM {$wpdb->prefix}gf_addon_payment_transaction WHERE lead_id=%d", $entry['id'] ) );

				if ( $count >= $gfstripe_installment_num_total[$i]) { 
					$result = gf_stripe()->cancel( $entry, $feed );
					gf_stripe()->log_debug( "Installments / Subscription Paid Off: Cancelling subscription (feed #{$feed['id']} - {$feed_name}) for entry #{$entry['id']}.");

					$to = get_option( 'admin_email' );
					$subject = 'Installments / Subscription Paid Off';
					$message = "Installments / Subscription Paid Off: Cancelling subscription (feed #{$feed['id']} - {$feed_name}) for entry #{$entry['id']}.";

					wp_mail( $to, $subject, $message);

				}
			}
		}	
    }
} );
