<?php
/*
  Plugin Name: Testimonails Custom Post Type
  Plugin URI: https://github.com/monkishtypist
  Description: This `mu-plugin` class creates a new custom post type for Testimonails.
  Version: 1.0
  Author: Tim Spinks @monkishtypist
  Author URI: https://github.com/monkishtypist
*/

if ( ! class_exists( 'CPT_Testimonials' ) ) :

	class CPT_Testimonials {

		private $textdomain = '';

		public function __construct() {

			add_action( 'init', array( $this, 'testimonial_post_type') );

			if ( is_admin() ) {
				add_action( 'load-post.php',     array( $this, 'init_meta_box' ) );
				add_action( 'load-post-new.php', array( $this, 'init_meta_box' ) );
			}
			add_action( 'admin_head', array( $this, 'style_meta_box' ) );

		}

		/**
		 * Register "Testimonials" post type
		 */
		public function testimonial_post_type() {

			$labels = array(
				'name'                  => _x( 'Testimonials', 'testimonial_post_type', $this->textdomain ),
				'singular_name'         => _x( 'Testimonial', 'testimonial_post_type', $this->textdomain ),
				'menu_name'             => _x( 'Testimonials', 'testimonial_post_type', $this->textdomain ),
				'name_admin_bar'        => _x( 'Testimonials', 'testimonial_post_type', $this->textdomain ),
				'archives'              => __( 'Testimonial Archives', $this->textdomain ),
				'parent_item_colon'     => __( 'Parent Item:', $this->textdomain ),
				'all_items'             => __( 'All Testimonials', $this->textdomain ),
				'add_new_item'          => __( 'Add New Testimonial', $this->textdomain ),
				'add_new'               => __( 'Add New', $this->textdomain ),
				'new_item'              => __( 'New Testimonial', $this->textdomain ),
				'edit_item'             => __( 'Edit Testimonial', $this->textdomain ),
				'update_item'           => __( 'Update Testimonial', $this->textdomain ),
				'view_item'             => __( 'View Testimonial', $this->textdomain ),
				'search_items'          => __( 'Search Testimonials', $this->textdomain ),
				'not_found'             => __( 'Not found', $this->textdomain ),
				'not_found_in_trash'    => __( 'Not found in Trash', $this->textdomain ),
				'featured_image'        => __( 'Featured Image', $this->textdomain ),
				'set_featured_image'    => __( 'Set featured image', $this->textdomain ),
				'remove_featured_image' => __( 'Remove featured image', $this->textdomain ),
				'use_featured_image'    => __( 'Use as featured image', $this->textdomain ),
				'insert_into_item'      => __( 'Insert into item', $this->textdomain ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', $this->textdomain ),
				'items_list'            => __( 'Items list', $this->textdomain ),
				'items_list_navigation' => __( 'Items list navigation', $this->textdomain ),
				'filter_items_list'     => __( 'Filter items list', $this->textdomain )
			);
			$rewrite = array(
				'slug'                  => 'testimonials',
				'with_front'            => false,
				'pages'                 => true,
				'feeds'                 => false
			);
			$args = array(
				'label'                 => __( 'Testimonial', $this->textdomain ),
				'description'           => __( '', $this->textdomain ),
				'labels'                => $labels,
				'supports'              => array( 'title', 'editor', 'thumbnail' ),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 10,
				'menu_icon'             => 'dashicons-testimonial',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => true,
				'publicly_queryable'    => true,
				'rewrite'               => $rewrite,
				'capability_type'       => 'page',
				'show_admin_column'     => true
			);
			register_post_type( 'cpt-testimonials', $args );
		}


		/**
		 * Meta box initialization.
		 */
		public function init_meta_box() {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box'  )        );
			add_action( 'save_post',      array( $this, 'save_meta_box' ), 10, 2 );
		}

		/**
		 * Add the meta-box.
		 */
		public function add_meta_box() {
			//this will add the metabox for the member post type
			$screens = array( 'cpt-testimonials' );

			foreach ( $screens as $screen ) {

				add_meta_box(
					'testimonials_settings',
					__( 'Testimonial Settings', $this->textdomain ),
					array($this, 'render_meta_box'),
					$screen,
					'side',
					'high'
				);
			}
		}

		/**
		 * Renders the meta-box.
		 *
		 * @param WP_Post $post The object for the current post/page.
		 */
		public function render_meta_box( $post ) {

			// Add a nonce field so we can check for it later.
			wp_nonce_field( 'testimonials_nonce_action', 'testimonials_nonce' );

			/*
			 * Use get_post_meta() to retrieve an existing value
			 * from the database and use the value for the form.
			 */
			$rating            = get_post_meta( $post->ID, 'testimonial_rating', true );
			$verified_buyer    = get_post_meta( $post->ID, 'testimonial_verified_buyer', true );
			$verified_reviewer = get_post_meta( $post->ID, 'testimonial_verified_reviewer', true );

			?>
			<div id="testimonials-settings">
				<div class="testimonials-settings-section testimonials-setting-rating">
					<?php _e( 'Rating' ); ?>: <span class="star-rating">
						<?php for ($i = 1; $i <= 5 ; $i++) {
							printf( '<input type="radio" name="testimonial_rating" class="star-%1$d" id="star-%1$d" value="%1$d" %2$s /><label class="star-%1$d" for="star-%1$d">%1$d</label>', $i, ( ( $i == $rating ) ? 'checked="checked"' : '' ) );
						} ?>
					</span>
				</div>
				<div class="testimonials-settings-section testimonials-setting-verified-reviewer">
					<?php _e( 'Verified Reviewer' ); ?> <span><?php printf( '<input type="checkbox" name="testimonial_verified_reviewer" value="1" %1$s />', ( $verified_reviewer ? 'checked="checked"' : '' ) ); ?></span>
				</div>
				<div class="testimonials-settings-section testimonials-setting-verified-buyer">
					<?php _e( 'Verified Buyer' ); ?> <span><?php printf( '<input type="checkbox" name="testimonial_verified_buyer" value="1" %1$s />', ( $verified_buyer ? 'checked="checked"' : '' ) ); ?></span>
				</div>
			</div>
		<?php }

		/**
		 * Saves the meta-box.
		 *
		 * @param int     $post_id Post ID.
		 * @param WP_Post $post    Post object.
		 * @return null
		 */
		public function save_meta_box( $post_id, $post ) {
			// Add nonce for security and authentication.
			$nonce_name   = isset( $_POST['testimonials_nonce'] ) ? $_POST['testimonials_nonce'] : '';
			$nonce_action = 'testimonials_nonce_action';

			// Check if nonce is set.
			if ( ! isset( $nonce_name ) ) {
				return;
			}

			// Check if nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
				return;
			}

			// Check if user has permissions to save data.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Check if not an autosave.
			if ( wp_is_post_autosave( $post_id ) ) {
				return;
			}

			// Check if not a revision.
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}

			update_post_meta( $post_id, 'testimonial_rating', $_POST['testimonial_rating'] );
			update_post_meta( $post_id, 'testimonial_verified_buyer', $_POST['testimonial_verified_buyer'] );
			update_post_meta( $post_id, 'testimonial_verified_reviewer', $_POST['testimonial_verified_reviewer'] );
		}

		/**
		 * Style the meta-box
		 */
		public function style_meta_box() {
			?>
			<style type="text/css">
			#testimonials_settings .inside {
				margin: 0;
				padding: 0;
			}
			.testimonials-settings-section {
				padding: 6px 10px 8px;
			}
			.testimonials-settings-section:before {
				color: #82878c;
				display: inline-block;
				font: 400 20px/1 dashicons;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: -1px;
				padding-right: 3px;
				speak: none;
				vertical-align: top;
			}
			.testimonials-settings-section.testimonials-setting-rating:before {
				content: "\f459";
			}
			.testimonials-settings-section.testimonials-setting-verified-reviewer:before {
				content: "\f147";
			}
			.testimonials-settings-section.testimonials-setting-verified-buyer:before {
				content: "\f174";
			}
			.star-rating {
				background: url('data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDIwIDIwIiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSIjREREREREIiBwb2ludHM9IjEwLDAgMTMuMDksNi41ODMgMjAsNy42MzkgMTUsMTIuNzY0IDE2LjE4LDIwIDEwLDE2LjU4MyAzLjgyLDIwIDUsMTIuNzY0IDAsNy42MzkgNi45MSw2LjU4MyAiLz48L3N2Zz4=');
				background-size: contain;
				display: inline-block;
				font-size: 0;
				position: relative;
				white-space: nowrap;
			}
			.star-rating input {
				-moz-appearance: none;
				-webkit-appearance: none;
				display: inline-block;
				height: 22px;
				margin: 0;
				opacity: 0;
				padding: 0;
				position: relative;
				z-index: 2;
			}
			.star-rating label {
				background: url('data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDIwIDIwIiB4bWw6c3BhY2U9InByZXNlcnZlIj48cG9seWdvbiBmaWxsPSIjRkZERjg4IiBwb2ludHM9IjEwLDAgMTMuMDksNi41ODMgMjAsNy42MzkgMTUsMTIuNzY0IDE2LjE4LDIwIDEwLDE2LjU4MyAzLjgyLDIwIDUsMTIuNzY0IDAsNy42MzkgNi45MSw2LjU4MyAiLz48L3N2Zz4=');
				background-size: contain;
				height: 100%;
				left: 0;
				opacity: 0;
				position: absolute;
				top: 0;
				z-index: 1;
			}
			.star-rating input:hover + label,
			.star-rating input:checked + label {
				opacity: 1;
			}
			.star-rating {
				width: calc(22px * 5);
			}
			.star-rating input,
			.star-rating label {
				width: 20%;
			}
			.star-rating label ~ label {
				width: 40%;
			}
			.star-rating label ~ label ~ label {
				width: 60%;
			}
			.star-rating label ~ label ~ label ~ label {
				width: 80%;
			}
			.star-rating label ~ label ~ label ~ label ~ label {
				width: 100%;
			}
			</style>
		<?php }

	}

	$CPT_Testimonials = new CPT_Testimonials();

endif;
