<?php
/**
 * Plugin Name: HP Custom Functions
 * Plugin URI: http://hostpress.ca
 * Description: HostPress custom funtions for SABM website. Dashboard changes and adding custom fields to profiles that work with member sign up through gravity form. Mostly user profile stuff. and a bit of branding.
 * Author: Todd Munro
 * Author URI: http://hostpress.ca
 * Version: 0.1.0
 */

/* Place custom code below this line. */

function myplugin_init() {
 $plugin_dir = basename(dirname(__FILE__));
 load_plugin_textdomain( 'hp-custom-functions', false, $plugin_dir . '/languages/' );
}
add_action('plugins_loaded', 'myplugin_init');


// French Datepicker

function french_datepicker_js() {
?>
<script type="text/javascript">
jQuery(function ($) {
$.datepicker.regional['fr'] = {clearText: 'Effacer', clearStatus: '',
    closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
    prevText: '<Préc', prevStatus: 'Voir le mois précédent',
    nextText: 'Suiv>', nextStatus: 'Voir le mois suivant',
    currentText: 'Courant', currentStatus: 'Voir le mois courant',
    monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
    'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
    monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
    'Jul','Aoû','Sep','Oct','Nov','Déc'],
    monthStatus: 'Voir un autre mois', yearStatus: 'Voir un autre année',
    weekHeader: 'Sm', weekStatus: '',
    dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
    dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
    dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
    dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: 'Choisir le DD, MM d',
    dateFormat: 'dd/mm/yy', firstDay: 0, 
    initStatus: 'Choisir la date', isRTL: false};
  $.datepicker.setDefaults($.datepicker.regional['fr']);
});
</script>
<?php
}
add_action('admin_head','french_datepicker_js');

// Get date to translate
add_filter('option_date_format', 'translate_date_format');
function translate_date_format($format) {
        if (function_exists('icl_translate'))
          $format = icl_translate('Formats', $format, $format);
       return $format;
}


// Remove WordPress version update nag
add_action('admin_menu','wphidenag');
function wphidenag() {
remove_action( 'admin_notices', 'update_nag', 3 );
}

// WPGrill - Remove WordPress Toolbar from Frontend
add_filter('show_admin_bar', '__return_false');

// Remove Unused Admin Bar Elements
function remove_admin_bar_links() {
global $wp_admin_bar;
$wp_admin_bar->remove_menu('wp-logo');
$wp_admin_bar->remove_menu('new-link');
$wp_admin_bar->remove_menu('updates');
$wp_admin_bar->remove_menu('themes');
$wp_admin_bar->remove_menu('customize');
$wp_admin_bar->remove_menu('new-content');  
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' ); 

/* 
 * Add a simple menu & link that opens in a new window 
 * Change the Menu 'title' name and 'href' link. 
 */  
function custom_adminbar_menu( $meta = TRUE ) {  
    global $wp_admin_bar;  
        if ( !is_user_logged_in() ) { return; }  
        if ( !is_super_admin() || !is_admin_bar_showing() ) { return; }  
    $wp_admin_bar->add_menu( array(  
        'id' => 'members-menu',  
        'title' => __( 'Members Profiles', 'hp-custom-functions' ),   
        'href' => admin_url( 'users.php' ) )
    );
    $wp_admin_bar->add_menu( array(  
        'parent' => 'members-menu',  
        'id'     => 'gf-entries',  
        'title' => __( 'Form Entries', 'hp-custom-functions'),            /* Set the link title */  
        'href' => admin_url( 'admin.php?page=gf_entries' ) )      /* Set the link a href */  
    );  
   
}  
add_action( 'admin_bar_menu', 'custom_adminbar_menu', 100 );  
/* The add_action # is the menu position: 
10 = Before the WP Logo 
15 = Between the logo and My Sites 
25 = After the My Sites menu 
100 = End of menu 
*/ 


//Visit from Admin site in new tab or window
function add_to_admin_bar () {
global $wp_admin_bar;
$wp_admin_bar->add_menu(array(
  'id' => 'view-site',  
   'meta' => array('target' => '_blank'),
));
$wp_admin_bar->add_menu(array(
  'id' => 'site-name', 
   'meta' => array('target' => '_blank'),
));
}
add_action('admin_bar_menu', 'add_to_admin_bar', 1);


/**
 * Plugin Name: Icon for Admin Bar Site Link
 * Plugin URI:  http://wpengineer.com/?p=2366
 * Description: Add Icon to Site Link in the WordPress Admin Bar
 * Version: 1.0.0
 * Author:      Sergej Müller
 * Author URI:  http://ebiene.de
 * License:     GPLv3
 */

// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;
// If the function exists this file is called as comments template.
// We don't do anything then.
if ( ! function_exists( 'sm_add_adminbar_site_icon' ) ) {

  // add to admin area, inside head
  add_action( 'admin_head', 'sm_add_adminbar_site_icon' );
  // add to frontend, inside head
  add_action( 'wp_head', 'sm_add_adminbar_site_icon' );
  
  function sm_add_adminbar_site_icon() {
    
    if ( ! is_admin_bar_showing() ) {
      return;
    }
    
    echo '<style>
      #wp-admin-bar-site-name > a.ab-item:before {
        float: left;
        width: 30px;
        height: 30px;
        margin: 3px -2px 0 -2px;
        display: block;
        content: "";
        opacity: 0.6;
        background-image: url(' . get_bloginfo('stylesheet_directory') . '/images/logo-dash.png) !important;
       background-repeat: no-repeat;
      }
      #wp-admin-bar-site-name:hover > a.ab-item:before {
        opacity: 1;
      }
    </style>';
  }

}

//   User Profile - remove unnessasary info

function admin_del_options() {
   global $_wp_admin_css_colors;
   $_wp_admin_css_colors = 0;
}

add_action('admin_head', 'admin_del_options');

//   User Profile - remove unnessasary social fields

function add_my_userprofile_contactmethod( $contactmethods ) {

  unset($contactmethods['aim']);
  unset($contactmethods['jabber']);
  unset($contactmethods['yim']);
  unset($contactmethods['reddit']);
  unset($contactmethods['delicious']);
  unset($contactmethods['googleplus']);
  unset($contactmethods['gplus']);
  unset($contactmethods['website']);
  unset($contactmethods['url']);
  unset($contactmethods['description']);
  unset($contactmethods['googleplus']);
  unset($contactmethods['user_url']);

  return $contactmethods;

}

add_filter('user_contactmethods','add_my_userprofile_contactmethod',10,1);

/* hide some profile fields - javascript */

// removes the `profile.php` admin color scheme options
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

if ( ! function_exists( 'cor_remove_personal_options' ) ) {
  /**
   * Removes the leftover 'Visual Editor', 'Keyboard Shortcuts' and 'Toolbar' options.
   */
  function cor_remove_personal_options( $subject ) {

 if (ICL_LANGUAGE_CODE == 'en') {
     $subject = preg_replace ( '#<h3>Personal Options</h3>.+?/table>#s', '', $subject, 1 );

    return $subject;
  } elseif (ICL_LANGUAGE_CODE == 'fr') {
     $subject = preg_replace ( '#<h3>Options personnelles</h3>.+?/table>#s', '', $subject, 1 );

    return $subject;
  }

  }

  function cor_profile_subject_start() {
    ob_start( 'cor_remove_personal_options' );
  }

  function cor_profile_subject_end() {
    ob_end_flush();
  }
}
add_action( 'admin_head-profile.php', 'cor_profile_subject_start' );
add_action( 'admin_footer-profile.php', 'cor_profile_subject_end' );



add_action( 'personal_options', array ( 'T5_Hide_Profile_Bio_Box', 'start' ) );

/**
 * Captures the part with the biobox in an output buffer and removes it.
 *
 * @author Thomas Scholz, <info@toscho.de>
 *
 */
class T5_Hide_Profile_Bio_Box
{
    /**
     * Called on 'personal_options'.
     *
     * @return void
     */
    public static function start()
    {
        $action = ( IS_PROFILE_PAGE ? 'show' : 'edit' ) . '_user_profile';
        add_action( $action, array ( __CLASS__, 'stop' ) );
        ob_start();
    }

    /**
     * Strips the bio box from the buffered content.
     *
     * @return void
     */
    public static function stop()
    {
        $html = ob_get_contents();
        ob_end_clean();


        // remove the headline
        $headline = __( IS_PROFILE_PAGE ? 'About Yourself' : 'About the user' );
        $html = str_replace( '<h3>' . $headline . '</h3>', '', $html );

        // remove the table row
        $html = preg_replace( '~<tr>\s*<th><label for="description".*</tr>~imsUu', '', $html );
        print $html;
        

    }

}


add_action( 'personal_options', array ( 'HP_Hide_Profile_Bio_Box', 'start' ) );

/**
 * WEbsite.
 *
 * @author Thomas Scholz, <info@toscho.de>
 *
 */
class HP_Hide_Profile_Bio_Box
{
    /**
     * Called on 'personal_options'.
     *
     * @return void
     */
    public static function start()
    {
        $action = ( IS_PROFILE_PAGE ? 'show' : 'edit' ) . '_user_profile';
        add_action( $action, array ( __CLASS__, 'stop' ) );
        ob_start();
    }

    /**
     * Strips the bio box from the buffered content.
     *
     * @return void
     */
    public static function stop()
    {
        $html = ob_get_contents();
        ob_end_clean();


       // remove the table row
        $html = preg_replace( '~<tr>\s*<th><label for="url".*</tr>~imsUu', '', $html );
        print $html;
        

    }

}


//   User Profile - Contact area - maybe make things neater

/*
function extended_contact_info($user_contactmethods) {  

$user_contactmethods = array(
'sabm_status' => __('Your status'),
'sabm_membership_level' => __('Type of Membership'),
'sabm_phone' => __('Phone')
);  

return $user_contactmethods;
}  

add_filter('user_contactmethods', 'extended_contact_info');

*/


//   User Profile - more control over gravity forms membership profile info

add_action( 'show_user_profile', 'my_show_extra_profile_fields', 20 );

add_action( 'edit_user_profile', 'my_show_extra_profile_fields', 20 );



function my_show_extra_profile_fields( $user ) { ?>



<h3><?php _e('SABM Membership Info', 'hp-custom-functions'); ?></h3>


<table class="form-table">

<tr>
    <th><label for="sabm_membership_level"><?php _e('Membership Type ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_membership_level" id="sabm_membership_level" value="<?php echo esc_attr( get_the_author_meta( 'sabm_membership_level', $user->ID ) ); ?>" class="regular-text" /><br />
        <span class="description"><?php _e('This is your Membership.', 'hp-custom-functions'); ?></span>
      </td>
</tr>
<tr>
    <th><label for="sabm_membership_card_user"><?php _e('Membership Card Number', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="number" name="sabm_membership_card_user" id="sabm_membership_card_user" value="<?php echo esc_attr( get_the_author_meta( 'sabm_membership_card_user', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="user_registered"><?php _e('Members since ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="user_registered" id="user_registered" value="<?php echo date( "j F Y - H:m:s", strtotime(esc_attr( get_the_author_meta( 'user_registered', $user->ID ) )) ); ?>" class="regular-text" /><br />
        <span class="description"><?php _e('This is when you registered', 'hp-custom-functions'); ?></span>
      </td>
</tr>
<tr>
  <th><label for="sabm_image_user"><?php _e('Profile image.', 'hp-custom-functions'); ?></label></th>
    <td>
       <a href="<?php echo esc_attr( get_the_author_meta( 'sabm_image_user', $user->ID ) ); ?>"><img style="height:80px;" src="<?php echo esc_attr( get_the_author_meta( 'sabm_image_user', $user->ID ) ); ?>"></a>
       <input type="text" name="sabm_image_user" id="sabm_image_user" value="<?php echo esc_attr( get_the_author_meta( 'sabm_image_user', $user->ID ) ); ?>" class="regular-text" /><input type='button' class="upload_image_button" value="<?php echo icl_t('hp-custom-functions','Upload Image', 'Upload Image'); ?>" id="button"/><br />
       <span class="description"><?php _e('Please upload your image for your profile.', 'hp-custom-functions'); ?></span>
    </td>
</tr>
</table>

<h3><?php _e('SABM Spouse Membership Info', 'hp-custom-functions'); ?></h3> 
<table class="form-table">
<tr>
    <th><label for="sabm_spouse_firstname"><?php _e('Spouse First Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_spouse_firstname" id="sabm_spouse_firstname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_spouse_firstname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_spouse_lastname"><?php _e('Spouse Last Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_spouse_lastname" id="sabm_spouse_lastname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_spouse_lastname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_membership_card_spouse"><?php _e('Membership Card Number Spouse', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="number" name="sabm_membership_card_spouse" id="sabm_membership_card_spouse" value="<?php echo esc_attr( get_the_author_meta( 'sabm_membership_card_spouse', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
  <th><label for="sabm_image_spouse"><?php _e('Profile image spouse.', 'hp-custom-functions'); ?></label></th>
    <td>
       <a href="<?php echo esc_attr( get_the_author_meta( 'sabm_image_spouse', $user->ID ) ); ?>"><img style="height:80px;" src="<?php echo esc_attr( get_the_author_meta( 'sabm_image_spouse', $user->ID ) ); ?>"></a>
       <input type="text" name="sabm_image_spouse" id="sabm_image_spouse" value="<?php echo esc_attr( get_the_author_meta( 'sabm_image_spouse', $user->ID ) ); ?>" class="regular-text" /><input type='button' class="upload_image_button" value="Upload Image" id="button"/><br />
       <span class="description"><?php _e("Please upload your image for your spouse's profile.", 'hp-custom-functions'); ?></span>
    </td>

</tr>
</table>
<h3><?php _e('SABM Children Membership Info', 'hp-custom-functions'); ?></h3> 
<table class="form-table">
  <tr>
    <th><label for="sabm_child1_firstname"><?php _e('First Child First Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_child1_firstname" id="sabm_child1_firstname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_child1_firstname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_child1_lastname"><?php _e('First Child Last Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_child1_lastname" id="sabm_child1_lastname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_child1_lastname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_membership_card_child1"><?php _e('Membership Card Number First Child', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="number" name="sabm_membership_card_child1" id="sabm_membership_card_child1" value="<?php echo esc_attr( get_the_author_meta( 'sabm_membership_card_child1', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
  <th><label for="sabm_birthday_child1"><?php _e('Birthday First Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       
       <input type="date" name="sabm_birthday_child1" id="sabm_birthday_child1" value="<?php echo esc_attr( get_the_author_meta( 'sabm_birthday_child1', $user->ID ) ); ?>" class="datepicker" /><br />
       
    </td>
</tr>
<tr>
<tr>
  <th><label for="sabm_image_child1"><?php _e('Profile image First Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       <a href="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child1', $user->ID ) ); ?>"><img style="height:80px;" src="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child1', $user->ID ) ); ?>"></a>
       <input type="text" name="sabm_image_child1" id="sabm_image_child1" value="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child1', $user->ID ) ); ?>" class="regular-text" /><input type='button' class="upload_image_button" value="Upload Image" id="button"/><br />
       <span class="description"><?php _e("Please upload your image for your first child's profile.", 'hp-custom-functions'); ?></span>
    </td>
</tr>
  <tr>
    <th><label for="sabm_child2_firstname"><?php _e('Second Child First Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_child2_firstname" id="sabm_child2_firstname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_child2_firstname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_child2_lastname"><?php _e('Second Child Last Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_child2_lastname" id="sabm_child2_lastname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_child2_lastname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_membership_card_child2"><?php _e('Membership Card Number Second Child', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="number" name="sabm_membership_card_child2" id="sabm_membership_card_child2" value="<?php echo esc_attr( get_the_author_meta( 'sabm_membership_card_child2', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
  <th><label for="sabm_birthday_child2"><?php _e('Birthday Second Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       
       <input type="date" name="sabm_birthday_child2" id="sabm_birthday_child2" value="<?php echo esc_attr( get_the_author_meta( 'sabm_birthday_child2', $user->ID ) ); ?>" class="datepicker" /><br />
       
    </td>
</tr>
<tr>
  <th><label for="sabm_image_child2"><?php _e('Profile image Second Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       <a href="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child2', $user->ID ) ); ?>"><img style="height:80px;" src="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child2', $user->ID ) ); ?>"></a>
       <input type="text" name="sabm_image_child2" id="sabm_image_child2" value="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child2', $user->ID ) ); ?>" class="regular-text" /><input type='button' class="upload_image_button" value="Upload Image" id="button"/><br />
       <span class="description"><?php _e("Please upload your image for your second child's profile.", 'hp-custom-functions'); ?></span>
    </td>
</tr>
  <tr>
    <th><label for="sabm_child3_firstname"><?php _e('Third Child First Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_child3_firstname" id="sabm_child3_firstname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_child3_firstname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_child3_lastname"><?php _e('Third Child Last Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_child3_lastname" id="sabm_child3_lastname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_child3_lastname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_membership_card_child3"><?php _e('Membership Card Number Third Child', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="number" name="sabm_membership_card_child3" id="sabm_membership_card_child3" value="<?php echo esc_attr( get_the_author_meta( 'sabm_membership_card_child3', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
  <th><label for="sabm_birthday_child3"><?php _e('Birthday Third Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       
       <input type="date" name="sabm_birthday_child3" id="sabm_birthday_child3" value="<?php echo esc_attr( get_the_author_meta( 'sabm_birthday_child3', $user->ID ) ); ?>" class="datepicker" /><br />
       
    </td>
</tr>
<tr>
<tr>
  <th><label for="sabm_image_child3"><?php _e('Profile image Third Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       <a href="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child3', $user->ID ) ); ?>"><img style="height:80px;" src="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child3', $user->ID ) ); ?>"></a>
       <input type="text" name="sabm_image_child3" id="sabm_image_child3" value="<?php echo esc_attr( get_the_author_meta( 'sabm_image_child3', $user->ID ) ); ?>" class="regular-text" /><input type='button' class="upload_image_button" value="Upload Image" id="button"/><br />
       <span class="description"><?php _e("Please upload your image for your third child's profile.", 'hp-custom-functions'); ?></span>
    </td>
</tr>


</table>
<h3><?php _e('SABM Additional Children Membership Info', 'hp-custom-functions'); ?></h3> 
<table class="form-table">

  <tr>
    <th><label for="sabm_additional1_firstname"><?php _e('First Additional Child First Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_additional1_firstname" id="sabm_additional1_firstname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_additional1_firstname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_additional1_lastname"><?php _e('First Additional Child Last Name ', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="text" name="sabm_additional1_lastname" id="sabm_additional1_lastname" value="<?php echo esc_attr( get_the_author_meta( 'sabm_additional1_lastname', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
    <th><label for="sabm_membership_card_additional1"><?php _e('Membership Card Number First Additional Child', 'hp-custom-functions'); ?></label></th>
      <td>
        <input type="number" name="sabm_membership_card_additional1" id="sabm_membership_card_additional1" value="<?php echo esc_attr( get_the_author_meta( 'sabm_membership_card_additional1', $user->ID ) ); ?>" class="regular-text" /><br />
      </td>
</tr>
<tr>
  <th><label for="sabm_birthday_additional1"><?php _e('Birthday First Additional Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       
       <input type="date" name="sabm_birthday_additional1" id="sabm_birthday_additional1" value="<?php echo esc_attr( get_the_author_meta( 'sabm_birthday_additional1', $user->ID ) ); ?>" class="datepicker" /><br />
       
    </td>
</tr>
<tr>
<tr>
  <th><label for="sabm_image_additional1"><?php _e('Profile image First Additional Child.', 'hp-custom-functions'); ?></label></th>
    <td style="float: left;">
       <a href="<?php echo esc_attr( get_the_author_meta( 'sabm_image_additional1', $user->ID ) ); ?>"><img style="height:80px;" src="<?php echo esc_attr( get_the_author_meta( 'sabm_image_additional1', $user->ID ) ); ?>"></a>
       <input type="text" name="sabm_image_additional1" id="sabm_image_additional1" value="<?php echo esc_attr( get_the_author_meta( 'sabm_image_additional1', $user->ID ) ); ?>" class="regular-text" /><input type='button' class="upload_image_button" value="Upload Image" id="button"/><br />
       <span class="description"><?php _e("Please upload your image for your first additional child's profile.", 'hp-custom-functions'); ?></span>
    </td>
</tr>
</table>

<?php }

/* Update custom profile fields */

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );
 
function my_save_extra_profile_fields( $user_id ) {
 
if ( !current_user_can( 'edit_user', $user_id ) )
return false;
/* Add them as you need them */
update_user_meta( $user_id, 'sabm_membership_level', $_POST['sabm_membership_level'] );
update_user_meta( $user_id, 'sabm_membership_card_user', $_POST['sabm_membership_card_user'] );
update_user_meta( $user_id, 'sabm_image_user', $_POST['sabm_image_user'] );
update_user_meta( $user_id, 'sabm_spouse_firstname', $_POST['sabm_spouse_firstname'] );
update_user_meta( $user_id, 'sabm_spouse_lastname', $_POST['sabm_spouse_lastname'] );
update_user_meta( $user_id, 'sabm_membership_card_spouse', $_POST['sabm_membership_card_spouse'] );
update_user_meta( $user_id, 'sabm_image_spouse', $_POST['sabm_image_spouse'] );
update_user_meta( $user_id, 'sabm_child1_firstname', $_POST['sabm_child1_firstname'] );
update_user_meta( $user_id, 'sabm_child1_lastname', $_POST['sabm_child1_lastname'] );
update_user_meta( $user_id, 'sabm_membership_card_child1', $_POST['sabm_membership_card_child1'] );
update_user_meta( $user_id, 'sabm_birthday_child1', $_POST['sabm_birthday_child1'] );
update_user_meta( $user_id, 'sabm_image_child1', $_POST['sabm_image_child1'] );
update_user_meta( $user_id, 'sabm_child2_firstname', $_POST['sabm_child2_firstname'] );
update_user_meta( $user_id, 'sabm_child2_lastname', $_POST['sabm_child2_lastname'] );
update_user_meta( $user_id, 'sabm_membership_card_child2', $_POST['sabm_membership_card_child2'] );
update_user_meta( $user_id, 'sabm_birthday_child2', $_POST['sabm_birthday_child2'] );
update_user_meta( $user_id, 'sabm_image_child2', $_POST['sabm_image_child2'] );
update_user_meta( $user_id, 'sabm_child3_firstname', $_POST['sabm_child3_firstname'] );
update_user_meta( $user_id, 'sabm_child3_lastname', $_POST['sabm_child3_lastname'] );
update_user_meta( $user_id, 'sabm_membership_card_child3', $_POST['sabm_membership_card_child3'] );
update_user_meta( $user_id, 'sabm_birthday_child3', $_POST['sabm_birthday_child3'] );
update_user_meta( $user_id, 'sabm_image_child3', $_POST['sabm_image_child3'] );
update_user_meta( $user_id, 'user_registered', $_POST['user_registered'] );
update_user_meta( $user_id, 'sabm_additional1_firstname', $_POST['sabm_additional1_firstname'] );
update_user_meta( $user_id, 'sabm_additional1_lastname', $_POST['sabm_additional1_lastname'] );
update_user_meta( $user_id, 'sabm_membership_card_additional1', $_POST['sabm_membership_card_additional1'] );
update_user_meta( $user_id, 'sabm_birthday_additional1', $_POST['sabm_birthday_additional1'] );
update_user_meta( $user_id, 'sabm_image_additional1', $_POST['sabm_image_additional1'] );
}



/* Make uploader image work */
function zkr_profile_upload_js() {
?><script type="text/javascript">
jQuery(document).ready(function() {
var formfield;
jQuery('.upload_image_button').click(function() {
jQuery('html').addClass('Image');
formfield = jQuery(this).prev().attr('name');
tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
return false;
});
window.original_send_to_editor = window.send_to_editor;
window.send_to_editor = function(html){
if (formfield) {
fileurl = jQuery('img',html).attr('src');
jQuery('#'+formfield).val(fileurl);
tb_remove();
jQuery('html').removeClass('Image');
} else {
window.original_send_to_editor(html);
}
};
});
</script>
<?php
}
add_action('admin_head','zkr_profile_upload_js');
 
// the following is the js and css for the upload functionality
function zkr_enque_scripts_init(){
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_style('thickbox');
}
add_action('init', 'zkr_enque_scripts_init');

/* Hide Welcome Panel. */
remove_action( 'welcome_panel', 'wp_welcome_panel' );


/* Remove dasboard widgets. */

function remove_dashboard_widgets() {
  global $wp_meta_boxes;

  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
  unset($wp_meta_boxes['dashboard']['side']['core']['espresso_news_dashboard_widget']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['espresso_news_dashboard_widget']);
  /* Remove Event Espresso news advertising (blue boxes)*/

  

}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );



// unregister all default WP Widgets
function unregister_default_wp_widgets() {
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Links');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Tag_Cloud');
}
add_action('widgets_init', 'unregister_default_wp_widgets', 1);

 
//* Remove eNews and Updates widget
add_action( 'widgets_init', 'remove_enews_updates_widget', 20 );
function remove_enews_updates_widget() {
  unregister_widget( 'Genesis_eNews_Updates' );
}
 
//* Remove Featured Page widget
add_action( 'widgets_init', 'remove_featured_page_widget', 20 );
function remove_featured_page_widget() {
  unregister_widget( 'Genesis_Featured_Page' );
}
 
//* Remove Featured Post widget
add_action( 'widgets_init', 'remove_featured_post_widget', 20 );
function remove_featured_post_widget() {
  unregister_widget( 'Genesis_Featured_Post' );
}
 
//* Remove Latest Tweets widget
add_action( 'widgets_init', 'remove_latest_tweets_widget', 20 );
function remove_latest_tweets_widget() {
  unregister_widget( 'Genesis_Latest_Tweets_Widget' );
}
 
//* Remove User Profile widget
add_action( 'widgets_init', 'remove_user_profile_widget', 20 );
function remove_user_profile_widget() {
  unregister_widget( 'Genesis_User_Profile_Widget' );
}

/* Event espresso custom shortcodes */


function registration_start_ee ( $atts, $content = null ) {
     global $post;
     $custom_content = get_post_custom($post->ID);
     if (isset($custom_content["event_registration_start"])) {
        $meta_content = $custom_content["event_registration_start"][0];
     }
     if (isset($custom_content["event_registration_start"])) {
        $meta_content = $custom_content["event_registration_start"][0];
     }
     $meta_content  = $custom_content["event_registration_start"][0];
     $displaystartdate = date ( 'l j F', strtotime ( $meta_content ) ) ;

     return $displaystartdate;
}
add_shortcode ('event_registration', 'registration_start_ee');


/* Remove WPML advertising (blue boxes)*/

define("ICL_DONT_PROMOTE", true);

/* Place custom code above this line. */
?>