<?php
/**
 * Class REST Controller
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Core
 * 
 */
namespace AMem\REST;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Controller {

	const route_base = 'advanced-members/v1';

	public function register_routes() {
		register_rest_route( self::route_base,
			'/forms',
			array(
				array(
					'methods' => \WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_forms' ),
					'permission_callback' => function () {
						if ( current_user_can( 'read' ) ) {
							return true;
						} else {
							return new \WP_Error( 'amem_forbidden',
								__( "You are not allowed to access forms.", 'advanced-members' ),
								array( 'status' => 403 )
							);
						}
					},
				)
			)
		);

		register_rest_route( self::route_base,
			'/forms/(?P<id>\d+)',
			array(
				array(
					'methods' => \WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_form' ),
					'permission_callback' => function ( WP_REST_Request $request ) {
						$id = (int) $request->get_param( 'id' );

						if ( current_user_can( 'edit_post', $id ) ) {
							return true;
						} else {
							return new \WP_Error( 'amem_forbidden',
								__( "You are not allowed to access the requested form.", 'advanced-members' ),
								array( 'status' => 403 )
							);
						}
					},
				)
			)
		);
	}

	public function get_forms( \WP_REST_Request $request ) {
		$args = array();

		$per_page = $request->get_param( 'per_page' );

		if ( null !== $per_page ) {
			$args['posts_per_page'] = (int) $per_page;
		}

		$offset = $request->get_param( 'offset' );

		if ( null !== $offset ) {
			$args['offset'] = (int) $offset;
		}

		$order = $request->get_param( 'order' );

		if ( null !== $order ) {
			$args['order'] = (string) $order;
		}

		$orderby = $request->get_param( 'orderby' );

		if ( null !== $orderby ) {
			$args['orderby'] = (string) $orderby;
		}

		$search = $request->get_param( 'search' );

		if ( null !== $search ) {
			$args['s'] = (string) $search;
		}

		$items = amem()->rest->get_forms($args);

		return rest_ensure_response( $items );
	}

	public function get_form( \WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );
		$item = amem()->rest->get_form( $id );

		if ( ! $item ) {
			return new \WP_Error( 'amem_not_found',
				__( "The requested form was not found.", 'advanced-members' ),
				array( 'status' => 404 )
			);
		}

		// $response = array(
		// 	'id' => $item->id(),
		// 	'slug' => $item->name(),
		// 	'title' => $item->title(),
		// 	'locale' => $item->locale(),
		// 	'properties' => $this->get_properties( $item ),
		// );

		return rest_ensure_response( $item );
	}

}
