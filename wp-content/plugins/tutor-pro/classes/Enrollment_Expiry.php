<?php

namespace TUTOR_PRO;

if (!defined('ABSPATH'))
    exit;

class Enrollment_Expiry {
    function __construct() {
        add_filter( 'tutor_course_settings_tabs', array($this, 'settings_attr') );
        add_action( 'single_course_template_before_load', array($this, 'cancel_expired_enrolment'), 10, 1 );
        add_action( 'tutor_before_enrolment_check', array($this, 'cancel_expired_enrolment'), 10, 2 );

        add_action( 'tutor_course/single/add-to-cart/after', array($this, 'show_expires_info_not_enrolled') );
        add_action( 'tutor_enrolled_box_after', array($this, 'show_expires_info') );
        add_filter( 'tutor/options/attr', array($this, 'setting_field') );
    }

    private function is_enabled() {
        return (bool) get_tutor_option('enrollment_expiry_enabled');
    }

    public function setting_field($attr) {

        $attr['course']['sections']['general']['fields']['enrollment_expiry_enabled']=[
            'type'      => 'checkbox',
            'label'     => __('Enrollment Expiration', 'tutor-pro'),
            'label_title'   => __('Enable', 'tutor-pro'),
            'desc' => __('Enable to allow enrollment expiration feature in all courses', 'tutor-pro'),
        ];

        return $attr;
    }

	public function settings_attr($args) {

        if(!$this->is_enabled()) {
            return $args;
        }

		$args['general']['fields']['enrollment_expiry'] = array(
            'type' => 'number',
            'label' => __('Enrollment Expiration', 'tutor-pro'),
            'default' => '0',
            'desc' => __('Student\'s enrollment will be removed after this number of days. Set 0 for lifetime enrollment.', 'tutor-pro')
		);
		return $args;
    }

    public function cancel_expired_enrolment($course_id, $user_id=null) {

        if(!$this->is_enabled()) {
            return;
        }

        if(!$user_id && !is_user_logged_in(  )) {
            return;
        }

        global $wpdb;

        $expiry = get_tutor_course_settings($course_id, 'enrollment_expiry');
        if(!is_numeric($expiry) || $expiry<1) {
            return;
        }

        $expired_date = tutor_time() - ((60*60*24)*$expiry);
        $current_id = $user_id ? $user_id : get_current_user_id(  );

        $ids = $wpdb->get_col(
            "SELECT ID FROM {$wpdb->posts} 
            WHERE post_author={$current_id} 
                AND post_parent={$course_id} 
                AND post_type='tutor_enrolled' 
                AND post_status='completed' 
                AND UNIX_TIMESTAMP(post_date)<{$expired_date}");

        if( is_array( $ids ) && count( $ids ) ) {

            $wpdb->query("UPDATE {$wpdb->posts} SET post_status='cancel' WHERE ID IN ( " . implode(',', $ids) . " )");

            foreach($ids as $id) {
                do_action( 'tutor_enrollment/after/expired', $id );
            }
        }
    }

    public function show_expires_info_not_enrolled() {

        if(!$this->is_enabled()) {
            return;
        }

        $course_id = get_the_ID();
        $expiry = get_tutor_course_settings($course_id, 'enrollment_expiry');
        $is_limited = is_numeric( $expiry ) && $expiry>=1;
        
        $validity = $is_limited ? $expiry . ' ' . ($expiry>1 ? __('days', 'tutor-pro') : __('day', 'tutor-pro')) : __('Lifetime', 'tutor-pro');

        echo '<p class="enrolment-expire-info"><i class="tutor-icon-calendar"></i> ', __('Enrolment validity', 'tutor-pro'), ': <b>', $validity, '</b></p>';
    }

    public function show_expires_info($ret=false) {
        
        if(!$this->is_enabled()) {
            return;
        }

        $course_id = get_the_ID();
        $enrolment = tutils()->is_enrolled($course_id, get_current_user_id());
        
        if ( $enrolment ) {

            $expiry = get_tutor_course_settings($course_id, 'enrollment_expiry');
            if(!is_numeric( $expiry ) || $expiry<1) {
                echo '<p class="enrolment-expire-info"><i class="tutor-icon-calendar"></i> ', __('Enrolment validity', 'tutor-pro'), ': <b>', __('Lifetime', 'tutor-pro'), '</b></p>';
                return;
            }

            $date = date_create( $enrolment->post_date );
            date_add( $date, date_interval_create_from_date_string($expiry . ' days') );
    
            $validity = date_format($date, get_option('date_format'));
            $text = __('Enrollment valid until', 'tutor-pro');

            echo '<p class="enrolment-expire-info"><i class="tutor-icon-calendar"></i> '.$text . ' <b>' . $validity .'</b></p>';
        }
    }
}