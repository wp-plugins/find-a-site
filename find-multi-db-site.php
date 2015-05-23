<?php
/*  Plugin Name: Find a Site
 *  Plugin URL: http://twhittakerdev.com/wp-content/uploads/2015/05/find-multi-db-site.zip
 *  Version: 1.0
 *  Network: true
 *  License: GPLv2 or later
 *  License URI: http://www.opensource.org/licenses/gpl-license.php
 *  Description: This plugin required Multi-DB Version-3.2.4 and above to work. It shows the information about a site in site.php page. Multi-DB plugin is plugin that scale your standard Multisite install to allow for millions of blogs and vastly improve the performance of your site. Multi-db plugin can be download from https://premium.wpmudev.org/project/multi-db/
 *  Author: Thomas Whittaker
 *  Author URI: http://www.twhittakerdev.com
 *  Requires at least: 4.2.0
 *  Tested up to: 4.2.2

    Copyright: © 2015 Thomas Whittaker.
*/
   
//this part of the plugin add the location of the blog under wp-admin/network/site-info.php?id= page
function fmds_show_blog_table(){
    global $pagenow;
    if( 'site-info.php' == $pagenow ) {
        ?><table><tr id="fmds_show_blog_table">
            <th scope="row">Database Info</th>

            <td>
            	<?php    
            			global $table_prefix; //this is pre-defined in wp-config.php file   
            			//use to find the md5 of current blog id that is being viewed by super admin    		 
            			$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
            			//use to determine the site table prefix
            			$blog_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
						if ( ! $id )
							wp_die( __('Invalid site ID.') );
						$details = get_blog_details( $id );
						if ( !can_edit_network( $details->site_id ) )
							wp_die( __( 'You do not have permission to access this page.' ), 403 );
						$parsed = parse_url( $details->siteurl );
						$is_main_site = is_main_site( $id ); 
						//check user pre-define database scaling in multi-db, db-config.php file
						if(constant("DB_SCALING")=="16"){
							 	 print('<b>Name :</b>  '.constant('DB_NAME').'_ <b>'.$id = substr(md5($id), 0,1).'</b>');
							 	 print('<br/><b> Table Prefix : </b>'.$table_prefix.$blog_id); 
						}else if (constant("DB_SCALING")=="256"){
 								print($id= substr(md5($id), 0,2));
						}else if (constant("DB_SCALING")=="4096"){
							 	 print($id= substr(md5($id), 0,3));
						}else{
							 	wp_die( __("Sorry. I do not recognize this  scaling. Please ensure its either 16,256 or 4096"),403);
						}
             	?>
             
            </td>
        </tr></table>
        <script>jQuery(function($){
            $('.form-table tbody').append($('#fmds_show_blog_table'));
        });</script><?php
    }
}
add_action('admin_footer', 'fmds_show_blog_table');

//hooks to columns on the network sites listing page

 add_filter('wpmu_blogs_columns','fmds_add_blog_id_column');


//take one parameter
//what about we also show the site id. since wpmu doesn't have it by default
function fmds_add_blog_id_column ($sites_columns){
	//ensure that ID is the first item listed 
	$columns_1 = array_slice( $sites_columns, 0, 1 );
    $columns_2 = array_slice( $sites_columns, 1 );
    //assign to an array call blogid
    $sites_columns = $columns_1 + array( 'blogid' => 'ID' ) + $columns_2; 
    return $sites_columns;
}

// Hook to manage column data on network sites listing
add_action( 'manage_sites_custom_column', 'fmds_show_blog_id_sites', 10, 2 );
 
/**
* Show blog id
*
* @param string
* @param integer
*
* @return void
*/
function fmds_show_blog_id_sites($column_name, $blog_id)
{
    if ( $column_name == 'blogid' ) {
        echo $blog_id;
    }
}

//create a new column name multi-db
add_filter('wpmu_blogs_columns','fmds_add_multi_db_column');
function fmds_add_multi_db_column ($sites_columns){ 
    //columns to show multi-db information 
	$columns_1 = array_slice( $sites_columns, 0, 2 );
    $columns_2 = array_slice( $sites_columns, 1 );
    //assign to an array call multidb
    $sites_columns = $columns_1 + array( 'multidb_id' => 'Multi-DB' ) + $columns_2; 
    return $sites_columns;
}
// Hook to manage column data on network sites listing
add_action( 'manage_sites_custom_column', 'fmds_show_multi_db_site_detail', 10, 2 );
 
/**
* Show blog id
*
* @param string
* @param integer
*
* @return void
*/
function fmds_show_multi_db_site_detail($column_name, $blog_id)
{
	global $table_prefix;
    //assigned blog_id to id, to help in finding the table prefix
    $id = $blog_id;
    if ( $column_name == 'multidb_id' ) {
    	if(constant("DB_SCALING") =="16"){
    	  	print('<b>Name : </b> '.constant('DB_NAME').'_ <b>'.$blog_id = substr(md5($blog_id), 0,1).'</b>');
		  	print('<br/><b> Table Prefix : </b>'.$table_prefix.$id);

    	}elseif(constant("DB_SCALING") =="256"){
    	  	print('<b>Name : </b> '.constant('DB_NAME').'_ <b>'.$blog_id = substr(md5($blog_id), 0,2).'</b>');
		  	print('<br/><b> Table Prefix : </b>'.$table_prefix.$id);

    	}elseif(constant("DB_SCALING") =="4096"){
    	  	print('<b>Name : </b> '.constant('DB_NAME').'_ <b>'.$blog_id = substr(md5($blog_id), 0,3).'</b>');
		  	print('<br/><b> Table Prefix : </b>'.$table_prefix.$id);

    	}else{
			wp_die( __("Please ensure multi-db is setup"),403);
		}
    }
}
