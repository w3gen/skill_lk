<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

$course_id = get_the_ID();
$show_certificate = (bool) tutils()->get_option('tutor_course_certificate_view');
$disable_certificate = get_post_meta($course_id, '_tutor_disable_certificate', true);

if ($disable_certificate == 'yes') {
	// No need to load html if certificate disabled for this course
	return;
}

?>

<a 
	id="tutor-download-certificate-pdf" 
	data-course_id="<?php echo $course_id; ?>" 
	data-cert_hash="<?php echo $certificate_hash; ?>" 
	href="#" 
	class="certificate-download-btn tutor-button bordered-button">

	<i class="tutor-icon-mortarboard"></i> <?php _e('Download Certificate', 'tutor-pro'); ?>
</a>

<?php if ($show_certificate) { ?>
	<style>
		.tutor-view-certificate { text-align:center; margin-top:10px; font-size:16px; text-transform:uppercase; }
	</style>
	<div class="tutor-view-certificate">
		<a id="tutor-view-certificate-image" href="#" data-href="<?php echo $certificate_url; ?>"><i class="tutor-icon-detail-link"></i> <?php _e('View Certificate', 'tutor-pro'); ?></a>
	</div>
<?php } ?>