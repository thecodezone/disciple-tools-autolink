<?php

namespace DT\Autolink\Services\Charts;

use DT\Autolink\Repositories\GroupTreeRepository;
use DT\Autolink\Services\Assets;
use function DT\Autolink\container;
use function DT\Autolink\get_plugin_option;
use function DT\Autolink\plugin_path;
use function DT\Autolink\plugin_url;
use function DT\Autolink\route_url;

require_once WP_PLUGIN_DIR . '/disciple-tools-genmapper/includes/charts/charts-base.php';

class GenmapChart extends \DT_Genmapper_Metrics_Chart_Base {
	public $title = 'Groups';
	public $slug = 'groups'; // lowercase
	public $js_object_name = 'wpApiGenmapper'; // This object will be loaded into the metrics.js file by the wp_localize_script.
	public $js_file_name = 'groups.js'; // should be full file name plus extension
	public $deep_link_hash = '#groups'; // should be the full hash name. #genmapper_of_hash
	public $permissions = [];
	public $namespace = "dt/v1/autolink";

	public function __construct() {
		parent::__construct();

		if ( ! $this->has_permission() ) {
			return;
		}

		// only load scripts if exact url
		add_action( 'rest_api_init', [ $this, 'add_api_routes' ], 99 );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );
	}

	/**
	 * Load scripts for the plugin
	 */
	public function scripts() {
		$genmapper_plugin_url  = plugins_url() . '/disciple-tools-genmapper';
		$genmapper_plugin_path = WP_PLUGIN_DIR . '/disciple-tools-genmapper';

		container()->get( Assets::class )->enqueue_mapbox( 0, false );

		wp_enqueue_style( "hint", "https://cdnjs.cloudflare.com/ajax/libs/hint.css/2.5.1/hint.min.css", [], "2.5.1" );
		wp_enqueue_style( "group-styles", $genmapper_plugin_url . "/includes/charts/church-circles/style.css", [], filemtime( $genmapper_plugin_path . "/includes/charts/church-circles/style.css" ) );
		wp_enqueue_style( "chart-styles", $genmapper_plugin_url . "/includes/charts/style.css", [], filemtime( $genmapper_plugin_path . "/includes/charts/style.css" ) );
		wp_register_script( 'd3', 'https://d3js.org/d3.v5.min.js', false, '5' );

		$group_fields = \DT_Posts::get_post_field_settings( "groups" );

		wp_enqueue_script( 'gen-template', $genmapper_plugin_url . "/includes/charts/church-circles/template.js", [
			'jquery',
			'jquery-ui-core',
		], filemtime( $genmapper_plugin_path . "/includes/charts/church-circles/template.js" ), true );

		wp_localize_script(
			'gen-template', 'genApiTemplate', [
				'plugin_uri'   => $genmapper_plugin_url,
				'group_fields' => $group_fields,
				'show_metrics' => get_option( "dt_genmapper_show_health_metrics", false ),
				'show_icons'   => get_option( "dt_genmapper_show_health_icons", true ),
			]
		);

        wp_enqueue_script( 'genmapper', $genmapper_plugin_url . "/includes/charts/genmapper.js", [
            'jquery',
            'jquery-ui-core',
            'd3',
            'gen-template'
        ], filemtime( $genmapper_plugin_path . "/includes/charts/genmapper.js" ), true );

        wp_localize_script(
            'genmapper', 'genApiTemplate', [
                'plugin_uri'   => $genmapper_plugin_url,
                'group_fields' => $group_fields,
                'show_metrics' => get_option( "dt_genmapper_show_health_metrics", false ),
                'show_icons'   => get_option( "dt_genmapper_show_health_icons", true )
            ]
        );

        wp_localize_script(
            'genmapper', 'wpApiGenmapper', [
                'plugin_uri'   => $genmapper_plugin_url
            ]
        );

		wp_enqueue_script( 'dt_' . $this->slug . '_script', plugin_url( 'resources/js/church-circles-genmap.js' ), [
			'jquery',
			'genmapper',
		], filemtime( plugin_path( 'resources/js/church-circles-genmap.js' ) ), true );

        wp_enqueue_script( 'genmapper-autolink', plugin_url( 'resources/js/genmapper-autolink.js' ), [
            'jquery',
            'jquery-ui-core',
            'genmapper'
        ], filemtime( plugin_path( 'resources/js/genmapper-autolink.js' ) ), true );

        wp_localize_script(
            'genmapper-autolink', 'genApiTemplate', [
                'show_metrics' => get_option( "dt_genmapper_show_health_metrics", false ),
                'show_icons'   => get_option( "dt_genmapper_show_health_icons", true ),
                'app_url'      => route_url()
            ]
        );

        // Vertical Genmap Support Scripts
        wp_enqueue_script( 'orgchart_js', 'https://cdnjs.cloudflare.com/ajax/libs/orgchart/3.7.0/js/jquery.orgchart.min.js', [
            'jquery',
        ], '3.7.0', true );

        $css_file_name = 'dt-metrics/common/jquery.orgchart.custom.css';
        $css_uri = get_template_directory_uri() . "/$css_file_name";
        $css_dir = get_template_directory() . "/$css_file_name";
        wp_enqueue_style( 'orgchart_css', $css_uri, [], filemtime( $css_dir ) );

		// Localize script with array data
		wp_localize_script(
			'dt_' . $this->slug . '_script', $this->js_object_name, [
				'name_key'           => $this->slug,
				'root'               => esc_url_raw( rest_url() ),
				'plugin_uri'         => $genmapper_plugin_url . '/includes',
				'nonce'              => wp_create_nonce( 'wp_rest' ),
				'current_user_login' => wp_get_current_user()->user_login,
				'current_user_id'    => get_current_user_id(),
				'spinner'            => '<img src="' . trailingslashit( plugin_dir_url( __DIR__ ) ) . 'ajax-loader.gif" style="height:1em;" />',
                'show_tree_genmap'   => get_plugin_option( 'show_tree_genmap', 0 ),
				'translation'        => [
					'string1'            => __( 'Group Generation Tree', 'disciple-tools-genmapper' ),
					'string2'            => __( 'This tree shows your groups and your descendants.', 'disciple-tools-genmapper' ),
					'string3'            => __( 'See descendants of a specific group', 'disciple-tools-genmapper' ),
					'string4'            => __( 'Reset', 'disciple-tools-genmapper' ),
					'parent_label'       => __( 'Parent', 'disciple-tools-autolink' ),
					'save_changes_label' => __( 'Save Changes', 'disciple-tools-autolink' ),
					'cancel_label'       => __( 'Cancel', 'disciple-tools-autolink' ),
					'open_label'         => __( 'Open Record', 'disciple-tools-autolink' ),
					'rebase_label'       => __( 'Center on this Node', 'disciple-tools-autolink' ),
					'ok_label'           => __( 'OK', 'disciple-tools-autolink' ),
					'edit_record'        => __( 'Edit Record', 'disciple-tools-autolink' ),
					'reset_label'        => __( 'Reset', 'disciple-tools-autolink' ),
					'zoom_in_label'      => __( 'Zoom In', 'disciple-tools-autolink' ),
					'zoom_out_label'     => __( 'Zoom Out', 'disciple-tools-autolink' ),
					'open_record_label'  => __( 'Open Record', 'disciple-tools-autolink' ),
				],
			]
		);
	}

	public function add_api_routes() {
		register_rest_route(
			$this->namespace, 'group-tree', [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'groups_tree' ],
					'permission_callback' => '__return_true',
				],
			]
		);
	}

	/**
	 * Retrieve the hierarchical tree of groups.
	 *
	 * @param array $params {
	 *     Optional. An associative array of parameters. Default empty array.
	 *
	 * @type string $node The ID of the node to retrieve its descendants. Default empty string.
	 * }
	 * @return array|\WP_Error The hierarchical tree of groups if successful, otherwise WP_Error object.
	 */
	public function groups_tree( $params ) {

		$groups    = \DT_Posts::list_posts( 'groups', [
			'assigned_to' => [ get_current_user_id() ],
		], false );
		$group_ids = array_map( function ( $group ) {
			return $group['ID'];
		}, $groups['posts'] );
		$args      = [ 'ids' => $group_ids ];

		$date_format = get_option( 'date_format' );

		$prepared_array = [
			[
				"id"       => 0,
				"parentId" => "",
				"name"     => "source",
			],
		];
		$groups = container()->get( GroupTreeRepository::class )->tree( 'groups', $args );

		if ( is_wp_error( $groups ) ) {
			return $groups;
		}

		if ( ! empty( $params["node"] ) && $params["node"] != "null" ) {
			$node = [];
			foreach ( $groups as $group ) {
				if ( $group["id"] === $params["node"] ) {
					$prepared_array    = [];
					$node              = $group;
					$node["parent_id"] = "";
				}
			}

			$groups = array_merge( [ $node ], $this->get_node_descendants( $groups, [ $params["node"] ] ) );
		}

        // Capture parent ids and their associated groups.
        $tree_parents_idx = [
            0 => []
        ];

        $tree_groups_idx = [
            0 => $prepared_array[0]
        ];

		foreach ( $groups as $group ) {
			$lines   = [];
			$lines[] = $group['name'];

			if ( $group["coach"] ) {
				$lines[] = $group['coach'];
			}

			if ( $group['location_name'] ) {
				$lines[] = $group['location_name'];
			}

			if ( $group['start_date'] ) {
				$lines[] = gmdate( $date_format, intval( $group['start_date'] ) );
			}

			$values           = [
				"object_type"               => 'group',
				"id"                        => $group["id"],
				"parentId"                  => $group["parent_id"] ?? 0,
				"name"                      => $group["name"],
				"line_1"                    => array_shift( $lines ),
				"line_2"                    => array_shift( $lines ),
				"line_3"                    => array_shift( $lines ),
				"line_4"                    => array_shift( $lines ),
				"church"                    => $group["group_type"] === "church",
				"active"                    => $group["group_status"] === "active",
				"group_type"                => $group["group_type"],
				"post_type"                 => "groups",
				"coach"                     => $group["coach"],
				"location"                  => $group["location_name"],
				"start_date"                => $group['start_date'] ? gmdate( $date_format, strtotime( $group['start_date'] ) ) : null,
				"attenders"                 => (int) $group['total_members'],
				"believers"                 => (int) $group['total_believers'],
				"baptized"                  => (int) $group['total_baptized'],
				"newlyBaptized"             => (int) $group['total_baptized_by_group'],
				"health_metrics_baptism"    => (bool) $group['health_metrics_baptism'],
				"health_metrics_bible"      => (bool) $group['health_metrics_bible'],
				"health_metrics_commitment" => (bool) $group['health_metrics_commitment'],
				"health_metrics_communion"  => (bool) $group['health_metrics_communion'],
				"health_metrics_giving"     => (bool) $group['health_metrics_giving'],
				"health_metrics_leaders"    => (bool) $group['health_metrics_leaders'],
				"health_metrics_fellowship" => (bool) $group['health_metrics_fellowship'],
				"health_metrics_praise"     => (bool) $group['health_metrics_praise'],
				"health_metrics_prayer"     => (bool) $group['health_metrics_prayer'],
				"health_metrics_sharing"    => (bool) $group['health_metrics_sharing'],
			];
			$prepared_array[] = $values;

            // Assign group to associated parent.
            if ( !isset( $tree_parents_idx[$values['parentId']] ) || !is_array( $tree_parents_idx[$values['parentId']] ) ) {
                $tree_parents_idx[$values['parentId']] = [];
            }
            $tree_groups_idx[$values['id']] = $values;
            $tree_parents_idx[$values['parentId']][] = $values;
		}

		if ( empty( $prepared_array ) ) {
			return new \WP_Error( 'failed_to_build_data', 'Failed to build data', [ 'status' => 400 ] );
		} else {
            return [
                'flat_connections' => $prepared_array,
                'tree_connections' => [
                    'groups' => $this->build_tree_connections( 0, $tree_parents_idx, $tree_groups_idx ),
                    'lookup_idx' => $tree_groups_idx
                ]
            ];
		}
	}

    private function build_tree_connections( $parent_id, $tree_parents_idx, $tree_groups_idx ): array {
        $children = [];
        if ( isset( $tree_parents_idx[ $parent_id ] ) && is_array( $tree_parents_idx[ $parent_id ] ) ) {
            foreach ( $tree_parents_idx[ $parent_id ] as $tree_parents_item ) {
                if ( isset( $tree_parents_item['id'] ) ) {
                    $children[] = $this->build_tree_connections( $tree_parents_item['id'], $tree_parents_idx, $tree_groups_idx );
                }
            }
        }

        // Package and return tree connections.
        $group = [];
        if ( isset( $tree_groups_idx[$parent_id] ) ) {
            $group = $tree_groups_idx[$parent_id];
            $group['children'] = $children;
        }

        return $group;
    }
}
