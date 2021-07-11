<?php
/**
 * PaidMembershipsPro class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.3.5
 */

namespace TUTOR_WCS;

if ( ! defined( 'ABSPATH' ) )
	exit;

class WCSubscriptions {

	public function __construct() {
		add_filter('tutor_is_enrolled', array($this, 'tutor_is_enrolled'), 10, 3);
	}

	public function tutor_is_enrolled($getEnrolledInfo, $course_id, $user_id ) {
		$product_id = tutils()->get_course_product_id($course_id);
		if ($product_id) {
			$product = wc_get_product( $product_id );
			$type = $product->get_type();

			if ($type === 'subscription' || $type === 'variable-subscription' ){
				$subscriptions = $this->get_users_subscription( $user_id );
				$has_active_subscription = false;
				foreach ( $subscriptions as $subscription_id => $subscription ) {
					if ($subscription->has_product($product_id)){
						$has_active_subscription = true;
					}
				}
				if ($has_active_subscription){
					return $getEnrolledInfo;
				}else{
					return false;
				}
			}
		}

		return $getEnrolledInfo;
	}
	public function get_users_subscription( $user_id = 0 ) {
		$user_id = tutils()->get_user_id($user_id);

		$query = new \WP_Query();
		$subscription_ids = $query->query( array(
			'post_type'           => 'shop_subscription',
			'posts_per_page'      => -1,
			'post_status'         => 'wc-active',
			'orderby'             => array(
				'date' => 'DESC',
				'ID'   => 'DESC',
			),
			'fields'              => 'ids',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'meta_query'          => array(
				array(
					'key'   => '_customer_user',
					'value' => $user_id,
				),
			),
		) );

		$subscriptions = array();
		foreach ( $subscription_ids as $subscription_id ) {
			$subscription = wcs_get_subscription( $subscription_id );

			if ( $subscription ) {
				$subscriptions[ $subscription_id ] = $subscription;
			}
		}
		return $subscriptions;
	}

}