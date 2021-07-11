<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PDF Certificate Title</title>
    <style type="text/css"><?php $this->pdf_style(); ?></style>
</head>
<body>

<div class="certificate-wrap">

    <table>
        <tr>
            <td class="first-col">
                <div class="certificate-content">
		            <?php
		            $hour_text = '';
		            $min_text = '';
		            if ($durationHours) {
                        $hour_text = $durationHours.' ';
                        $hour_text .= ($durationHours > 1) ? __('hours', 'tutor-pro') : __('hour', 'tutor-pro');
                    }
                    if ($durationMinutes) {
                        $min_text = $durationMinutes.' ';
                        $min_text .= ($durationMinutes > 1) ? __('minutes', 'tutor-pro') : __('minute', 'tutor-pro');
                    }
                    $duration_text = $hour_text.' '.$min_text;
                    ?>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <p><?php _e('This is to certify that', 'tutor-pro'); ?></p>
                    <h1><?php echo $user->display_name; ?></h1>
                    <br/>
                    <br/>
                    <p><?php echo __('has successfully completed', 'tutor-pro').' '.$duration_text.' '.__('online course of', 'tutor-pro'); ?></p>
                    <h2><?php echo $course->post_title; ?></h2>
                    <p><?php echo __('on', 'tutor-pro').' '.$completed_date; ?></p>
                </div>
            </td>
            <td>
                <div class="verifying-wrap">
                    <p><strong><?php _e('Valid Certificate ID', 'tutor-pro'); ?></strong></p>
                    <p><?php echo $completed->completed_hash; ?></p>
                </div>
                <div class="signature-wrap">
                    <img src="<?php echo $signature_image_url; ?>" />
                    <p class="certificate-author-name"> <strong><?php echo tutor_utils()->get_option('tutor_cert_authorised_name'); ?></strong> </p>
	                <?php echo tutor_utils()->get_option('tutor_cert_authorised_company_name'); ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="watermark">
    <img src="<?php echo $this->template['url'].'background.png'; ?>" height="100%" width="100%" />
</div>

</body>
</html>