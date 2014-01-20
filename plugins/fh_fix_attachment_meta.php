<?php
/*
Plugin Name: FH Fix Attachment Metadata
Plugin URI: http://hoech.net/
Description: Re-generate attachment metadata if necessary.
Version: 1.0
Author: Florian Höch
Author URI: http://hoech.net/
*/

add_action('admin_menu', 'wmu_admin_menu');

function wmu_admin_menu() {
	add_submenu_page('tools.php', 'Fix Attachment Metadata', 'Fix Attachment Metadata', 'manage_options', 'fh-fix-attachment-meta', 'fh_fix_attachment_meta');
}

function fh_fix_attachment_meta() {
	global $wpdb;
	?>
	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h2><?php _e('Fix Attachment Metadata'); ?></h2>
	
		<?php
		if ($_GET['action'] == 'run') {
			$updated = 0;
			$skipped = 0;
			$errors = 0;
			$upload_dir = wp_upload_dir();

			$sql = sprintf("select * from %s where post_type = 'attachment'", $wpdb->posts);
			$attachments = $wpdb->get_results($sql);

			foreach ($attachments as $attachment) {
				$meta = get_post_meta($attachment->ID);
				if (array_key_exists('_wp_attached_file', $meta)) {
					$file = $meta['_wp_attached_file'][0];
					$file_path = $upload_dir['basedir'] . '/' . $file;
					if (file_exists($file_path)) {
						$adding_meta = false;
						if (!array_key_exists('_wp_attachment_metadata', $meta) || !empty($_POST['force'])) {
							echo '<div class="message"><p>Generating metadata for “' . $file . '”...</p></div>';
							$metadata = wp_generate_attachment_metadata($attachment->ID, $file_path);
							echo '<div class="updated"><p>...metadata successfully generated for “' . $file . '”.</p></div>';
							$adding_meta = wp_update_attachment_metadata($attachment->ID, $metadata);
						}
						if ($adding_meta) {
							echo '<div class="updated"><p>Updated metadata for “' . $file . '”.</p></div>';
							$updated++;
						}
						else {
							echo '<div class="updated"><p>Skipped “' . $file . '”.</p></div>';
							$skipped++;
						}
					}
					else {
						echo '<div class="error"><p>Error: “' . $file . '” does not exist.</p></div>';
						$errors ++;
					}
				}
				else {
					echo '<div class="error"><p>Error: Attachment ID ' . $attachment->ID . ' has no associated file.</p></div>';
					$errors ++;
				}
			}

			echo '<div id="message"><p>' . sprintf("%d attachments were updated, %d were skipped and %d had errors.", $updated, $skipped, $errors) . '</p></div>';
		}
		?>
		<p>
			<form name="updater" method="post" action="<?php echo add_query_arg(array('action' => 'run')); ?>">
				<input class='button-primary' type='submit' name='run' value='<?php _e('Run'); ?>' />
				<input class='button-primary' type='submit' name='force' value='<?php _e('Force Run'); ?>' />
			</form>
		</p>
	</div>	
	<?php
}
?>