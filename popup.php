<?php
/**
 * Uploadscreen for selecting and uploading new media file
 *
 * @author      M�ns Jonasson  <http://www.mansjonasson.se>
 * @copyright   M�ns Jonasson 13 sep 2010
 * @version     $Revision: 2303 $ | $Date: 2010-09-13 11:12:35 +0200 (ma, 13 sep 2010) $
 * @package     wordpress
 * @subpackage  enable-media-replace
 *
 */

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.', 'enable-media-replace'));

global $wpdb;

$table_name = $wpdb->prefix . "posts";

$sql = "SELECT guid, post_mime_type FROM $table_name WHERE ID = " . (int) $_GET["attachment_id"];

list($current_filename, $current_filetype) = $wpdb->get_row($sql, ARRAY_N);

$current_filename = substr($current_filename, (strrpos($current_filename, "/") + 1));

?>
<div class="wrap">
	<h2><?php echo __("Replace Media Upload", "enable-media-replace"); ?></h2>

	<?php
	$url = admin_url( "upload.php?page=enable-media-replace/enable-media-replace.php&noheader=true&action=media_replace_upload&attachment_id=" . (int) $_GET["attachment_id"]);
	$action = "media_replace_upload";
    $formurl = wp_nonce_url( $url, $action );
	if (FORCE_SSL_ADMIN) {
			$formurl = str_replace("http:", "https:", $formurl);
		}
	?>

	<form enctype="multipart/form-data" method="post" action="<?php echo $formurl; ?>">
	<?php
		#wp_nonce_field('enable-media-replace');
	?>
		<input type="hidden" name="ID" value="<?php echo (int) $_GET["attachment_id"]; ?>" />
		<div id="message" class="updated fade"><p><?php echo __("NOTE: You are about to replace the media file", "enable-media-replace"); ?> "<?php echo $current_filename?>". <?php echo __("There is no undo. Think about it!", "enable-media-replace"); ?></p></div>

	<?php do_action( 'emr_before_replace_type_options' ); ?>

	<?php if ( apply_filters( 'emr_display_replace_type_options', true ) ) : ?>
	
		<h3><?php echo __("Select media replacement type", "enable-media-replace"); ?></h3>

		<p>
			<label for="replace_type_1"><input CHECKED id="replace_type_1" type="radio" name="replace_type" value="replace"> <?php echo __("Replace the file, keep old file name", "enable-media-replace"); ?></label>
		</p>


<?php 
	$suffix = substr($current_filename, (strlen($current_filename)-4));
	$imgAr = array(".png", ".gif", ".jpg");
	if (in_array($suffix, $imgAr)) : 
?>
		<p>
			<label for="replace_type_2"><input id="replace_type_2" type="radio" name="replace_type" value="replace_thumb"> <?php echo __("Replace a thumbnail", "enable-media-replace"); ?>:</label>
<?php 
	$metadata = wp_get_attachment_metadata($_GET["attachment_id"]);
	$sizes = $metadata['sizes'];
?>
		<select name="image_size">
<?php 
	foreach ($sizes as $size_name => $size):
		$width = $size['width'];
		$height = $size['height'];
		$file = $size['file'];
		$mime_type = $size['mime-type'];
?>
    		<option value="<?php echo ($file) ?>"><?php echo ($size_name.' : '.$file) ?></option>
<?php endforeach; ?>
		</select>
<?php  
	//var_dump( $metadata ); //debug
	//var_dump( $sizes ); //debug
?> 
		</p>
<?php endif; ?>
		
		<?php if ( apply_filters( 'emr_enable_replace_and_search', true ) ) : ?>
		<p>
			<label for="replace_type_3"><input id="replace_type_3" type="radio" name="replace_type" value="replace_and_search"> <?php echo __("Replace the file, use new file name and update links", "enable-media-replace"); ?></label>
		</p>
		
		<?php endif; ?>
	<?php else : ?>
		<input type="hidden" name="replace_type" value="replace" />
	<?php endif; ?>
	
		<br/>
	
		<h3><?php echo __("Choose file to upload", "enable-media-replace"); ?></h3>

		<p><input type="file" name="userfile" /></p>
		<p><input type="submit" class="button button-primary" value="<?php echo __("Upload", "enable-media-replace"); ?>" /> <a href="#" onclick="history.back();" class="button"><?php echo __("Cancel", "enable-media-replace"); ?></a></p>
	</form>
	
	<div class="wp-filter">	
		<h3><?php echo __("Instructions", "enable-media-replace"); ?></h3>
		<p class="howto"><b><?php echo __("Replace the file, keep old file name", "enable-media-replace"); ?></b> — <?php echo __("This option requires you to upload a file of the same type (", "enable-media-replace"); ?><?php echo $current_filetype; ?><?php echo __(") as the one you are replacing. The name of the attachment will stay the same (", "enable-media-replace"); ?><?php echo $current_filename; ?><?php echo __(") no matter what the file you upload is named.", "enable-media-replace"); ?></p>
<?php 
	if (in_array($suffix, $imgAr)) : 
?>
		<p class="howto"><b> <?php echo __("Replace a thumbnail", "enable-media-replace"); ?></b> — <?php echo __("(images only) This option requires you to upload a file of the same type (", "enable-media-replace"); ?><?php echo $current_filetype; ?><?php echo __(") and size as the one you are replacing. The name of the attachment will stay the same no matter what the file you upload is named.", "enable-media-replace"); ?></p>
<?php endif; ?>
		<p class="howto"><b> <?php echo __("Replace the file, use new file name and update links"); ?></b> — <?php echo __("If you select this option, the name and type of the file you upload will replace the old file. All links pointing to the current file (", "enable-media-replace"); ?><?php echo $current_filename; ?><?php echo __(") will be updated to point to the new file name. Please note that only embeds/links of the original size image will be replaced in your posts.", "enable-media-replace"); ?></p>
	</div>
</div>
