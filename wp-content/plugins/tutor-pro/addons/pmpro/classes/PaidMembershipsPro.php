<?php
/**
 * PaidMembershipsPro class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.3.5
 */

namespace TUTOR_PMPRO;

if ( ! defined( 'ABSPATH' ) )
    exit;

class PaidMembershipsPro {

    public function __construct() {
        add_action('pmpro_membership_level_after_other_settings', array($this, 'display_courses_categories'));
        //add_action('pre_get_posts', array($this, 'course_pre_get_posts'));
        add_action('tutor_action_pmpro_settings', array($this, 'pmpro_settings'));
        add_filter('tutor_course/single/add-to-cart', array($this, 'tutor_course_add_to_cart'));
        add_filter('tutor_course_price', array($this, 'tutor_course_price'));
        add_filter( 'tutor-loop-default-price', array($this, 'add_membership_required'));

        add_action('template_redirect', array($this, 'check_active_subscriptions'));
    }

    public function course_pre_get_posts($query){
        $membershipCats = $this->get_hidden_categories();

        if (tutils()->count($membershipCats)){
            //$query->set('category__not_in', $membershipCats);
            $taxquery = array(
                array(
                    'taxonomy' => 'course-category',
                    'field' => 'id',
                    'terms' => $membershipCats,
                    'operator'=> 'NOT IN'
                )
            );
            $query->set( 'tax_query', $taxquery );
        }

        return $query;
    }


    public function display_courses_categories(){
        include_once TUTOR_PMPRO()->path."views/pmpro-content-settings.php";
    }

    /**
     * @return array
     */
    public function get_hidden_categories($use_filter=true){
        global $current_user, $wpdb;

        //get page ids that are in my levels
        if(!empty($current_user->ID))
            $levels = pmpro_getMembershipLevelsForUser($current_user->ID);
        else
            $levels = false;

        //get categories that are filtered by level, but not my level
        global $pmpro_my_cats;
        $pmpro_my_cats = array();

        if($levels) {
            foreach($levels as $key => $level) {
                $member_cats = pmpro_getMembershipCategories($level->id);
                $pmpro_my_cats = array_unique(array_merge($pmpro_my_cats, $member_cats));
            }
        }

        //get hidden cats
        if(!empty($pmpro_my_cats) && $use_filter)
            $sql = "SELECT category_id FROM $wpdb->pmpro_memberships_categories WHERE category_id NOT IN(" . implode(',', $pmpro_my_cats) . ")";
        else
            $sql = "SELECT category_id FROM $wpdb->pmpro_memberships_categories";

        $hidden_cat_ids = array_values(array_unique($wpdb->get_col($sql)));

        return $hidden_cat_ids;
    }

    /**
     * pmpro settings
     */
    public function pmpro_settings(){
        $tutor_pmpro_membership_model = sanitize_text_field(tutils()->array_get('tutor_pmpro_membership_model', $_POST));
        if ($tutor_pmpro_membership_model){
            update_option('tutor_pmpro_membership_model', $tutor_pmpro_membership_model);
        }
    }

    /**
     * @return bool
     *
     * Just check if has membership access
     *
     * @since v.1.7.5
     */
    private function has_membership_access($check_only_if_required=false){
        global $current_user, $wpdb;

        $monetize_by = get_tutor_option('monetize_by');
        $has_pmpro = tutils()->has_pmpro();

        if ($monetize_by !== 'pmpro' || ! $has_pmpro){
            return true;
        }
        $tutor_pmpro_membership_model = get_option('tutor_pmpro_membership_model');

        $has_membership_access = false;
        $is_required_membership = false;

        //get page ids that are in my levels
        $levels = false;
        if(!empty($current_user->ID)){
            $levels = pmpro_getMembershipLevelsForUser($current_user->ID);
        }

        !is_array($levels) ? $levels=array() : 0;

        if ($tutor_pmpro_membership_model === 'full_website_membership'){
            $is_required_membership=true;


            $has_full_site = array_filter($levels, function($level) {
                return strtolower($level->name)=='full site membership';
            });
            
            if (count($has_full_site)){
                $has_membership_access = true;
            }

        }elseif ($tutor_pmpro_membership_model === 'category_wise_membership'){
            //Check this course attached category has membership
            $membershipCats = $this->get_hidden_categories(!$check_only_if_required);

            $attached_categories = get_tutor_course_categories();

            if (tutils()->count($attached_categories)){
                foreach ($attached_categories as $category){
                    if (in_array($category->term_id, $membershipCats)){
                        $is_required_membership = true;
                        break;
                    }
                }
            }

            !$is_required_membership ? $has_membership_access=true : 0;
        }

        return $check_only_if_required ? $is_required_membership : (is_user_logged_in() ? $has_membership_access : !$is_required_membership);
    }

    /**
     * @param $html
     *
     * @return mixed|void
     *
     * Enrolment main logic for Membership
     *
     * @since v.1.7.5
     */
    public function add_membership_required($price){
        
        if($this->has_membership_access(true)){
            $price = ((is_admin() && !wp_doing_ajax()) || !$this->has_membership_access()) ? __('Membership Required', 'tutor-pro') : __('Free', 'tutor-pro');
        }
        
        return $price;
    }

    /**
     * @param $html
     *
     * @return mixed|void
     *
     * Enrolment main logic for Membership
     *
     * @since v.1.3.6
     */
    public function tutor_course_add_to_cart($html){

        if($this->has_membership_access()){
            return $html;
        }
        else {
        
            $level_page_id = apply_filters('tutor_pmpro_level_page_id', pmpro_getOption("levels_page_id"));
            $level_page_url = get_the_permalink($level_page_id);

            $msg = '<div class="tutor-notice-warning no-memberhsip-msg-wrap">';
            $msg.= '<p>'.sprintf(__('You must have a %s membership plan %s to enrol in this course.', 'tutor-pro'), "<a href='{$level_page_url}'>", "</a>").'</p>';
            $msg .= '</div>';

            return apply_filters('tutor_enrol_no_membership_msg', $msg);
        }
    }

    /**
     * @param $html
     *
     * @return string
     *
     * Remove the price if Membership Plan activated
     *
     * @since v.1.3.6
     */
    public function tutor_course_price($html){
        $monetize_by = get_tutor_option('monetize_by');
        if ($monetize_by === 'pmpro'){
            return '';
        }

        return $html;
    }

    /**
     * Check active subscription
     * If there is no active subscription, then cancel enrolment.
     */
    public function check_active_subscriptions(){
        global $current_user, $wpdb;

        $monetize_by = get_tutor_option('monetize_by');
        $has_pmpro = tutils()->has_pmpro();

        if ( ! is_single_course() && $monetize_by !== 'pmpro' || ! $has_pmpro){
            return;
        }

        //Check level conditions.

        $has_membership = false;
        $course_id = get_the_ID();
        $user_id = $current_user->ID;

        if ( ! tutils()->is_enrolled($course_id)){
            return;
        }

        $levels = pmpro_getMembershipLevelsForUser($current_user->ID);

        if (tutils()->count($levels)){
            foreach ($levels as $key => $level){
                $endtime = (int) $level->enddate;
                if (0 < $endtime && $endtime < tutor_time()){
                    unset($levels[$key]);
                }
            }
        }

        /**
         * If there is no active subscription, cancel enrollment
         */

        if ( ! tutils()->count($levels) && $this->has_membership_access(true)){
            tutils()->cancel_course_enrol($course_id, $user_id);
        }
    }
}