<?php
$absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $absolute_path[0] . 'wp-load.php';
require_once($wp_load);
?>

<?php
  // Retrieves the stored value from the database
  $meta_image = get_post_meta( get_the_ID(), 'meta-image', true );
  $meta_cta = get_post_meta( get_the_ID(), 'meta-cta', true );
  console_log($meta_image);
?>

<?php header("Content-type: text/css; charset: UTF-8"); ?>
<?php header("Cache-control: must-revalidate"); ?>

.jb-custom-header {
  background: url(" <?php echo "$meta_image" ?> ") no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
