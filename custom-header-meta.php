<?php

/**
 * Plugin Name:  Custom Header Meta
 * Plugin URI:   https://hornermillwork.com/
 * Description:  Provides a meta box for getting a user-selected page header.
 * Author:       James Boynton
 * Version:      0.1
 * Author URI:   http://xzito.com/
 */

/** No access **/
if ( !defined( 'ABSPATH' ) ) {
  die( 'Access denied.' );
}

/**
* Adds a meta box to the post editing screen
*/
function jb_custom_header() {
  if (get_post_type( get_the_ID() ) == 'page')
    add_meta_box( 'jb_meta_header', __( 'Page Header', 'jb-textdomain' ), 'jb_header_callback' );
}
add_action( 'add_meta_boxes', 'jb_custom_header' );

/**
 * Outputs the content of the meta box.
 */
function jb_header_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'jb_nonce' );
  $jb_stored_meta = get_post_meta( $post->ID );
  ?>

  <p>
    <label for="meta-image" class="jb-meta-label"><?php _e( 'Header image for this page', 'jb-textdomain' ) ?></label>
    <br />
    <input type="button" id="meta-image-button" class="button" value="<?php _e( 'Choose or Upload an Image', 'jb-textdomain' )?>" />
    <input hidden type="text" name="meta-image" id="meta-image" class="meta-input-text" value="<?php if ( isset ( $jb_stored_meta['meta-image'] ) ) echo $jb_stored_meta['meta-image'][0]; ?>" />
  </p>
  <?php if ( isset ( $jb_stored_meta['meta-image'] ) ) : ?>
    <p>
      <div class="header-image-preview" style="max-width: 100%;max-height: 170px;overflow: hidden;border-color: #f5f5f5;border-style: solid;border-top-width: 24px;border-bottom-width: 24px;border-left-width: 14px;border-right-width: 14px;background-color: #f5f5f5">
        <img src="<?php echo $jb_stored_meta['meta-image'][0] ?>" alt="current header image" style="top: 50%;left: 50%;"/>
      </div>
    </p>
  <?php endif; ?>

  <!-- WIP -->
  <!-- <form method="post">
    <input hidden type="text" name="unset_header_image" value="true" />
    <input type="submit" id="meta-header-delete-button" class="button" value="" />
  </form> -->

  <?php submit_button(); ?>
<?php   // this is on purpose, don't delete it
}

/**
 * Saves the custom meta input
 */
function jb_meta_save( $post_id ) {

  // Checks save status
  $is_autosave = wp_is_post_autosave( $post_id );
  $is_revision = wp_is_post_revision( $post_id );
  $is_valid_nonce = ( isset( $_POST[ 'jb_nonce' ] ) && wp_verify_nonce( $_POST[ 'jb_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

  // Exits script depending on save status
  if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
    return;
  }

  // Remove the current header image
  // if( isset( $_POST[ 'unset_header_image' ] ) ) {
  //   update_post_meta( $post_id, 'meta-image', null );
  // }
  // Checks for input and saves if needed
  if( isset( $_POST[ 'jb-meta-active' ] ) ) {
      update_post_meta( $post_id, 'jb-meta-active', ( $_POST[ 'jb-meta-active' ] ) );
  }
  if( isset( $_POST[ 'meta-cta' ] ) ) {
      update_post_meta( $post_id, 'meta-cta', ( $_POST[ 'meta-cta' ] ) );
  }
  if( isset( $_POST[ 'meta-image' ] ) ) {
    update_post_meta( $post_id, 'meta-image', $_POST[ 'meta-image' ] );
  }
}
add_action( 'save_post', 'jb_meta_save' );

/**
 * Loads the image management javascript
 */
function jb_image_enqueue() {
  global $typenow;
  if( $typenow == 'page' ) {
    wp_enqueue_media();

    // Registers and enqueues the required javascript.
    wp_register_script( 'meta-box-image', plugin_dir_url( __FILE__ ) . 'custom-header-image.js', array( 'jquery' ) );
    wp_localize_script( 'meta-box-image', 'meta_image',
      array(
        'title' => __( 'Choose or Upload an Image', 'jb-textdomain' ),
        'button' => __( 'Use this image', 'jb-textdomain' ),
      )
    );
    wp_enqueue_script( 'meta-box-image' );
  }
}
add_action( 'admin_enqueue_scripts', 'jb_image_enqueue' );

?>
