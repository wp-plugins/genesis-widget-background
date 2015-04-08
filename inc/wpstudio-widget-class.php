<?php

defined( 'WPINC' ) or die;

/**
 * Advanced Featured Page Widget class
 * 
 * Original Author: Studiopress
 * Modifications & Enchancements by: Frank Schrijvers
 *
 */

// Enqueue stylesheet
function wpstudio_name_scripts() {
	wp_enqueue_style( 'wpstudio-style', plugin_dir_url( __FILE__ ) . '../css/wpstudio-style.css' );
}

add_action( 'wp_enqueue_scripts', 'wpstudio_name_scripts' );

class Genesis_Widget_Background extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 */
	function __construct() {

		add_image_size( 'featured-image', 2000, 1000, TRUE ); // creates a featured image size for the banner

		//* Setup widget default options
		$this->defaults = array(
			'title'           			=> '',
			'feature_type'				=> 'page',	
			'page_id'         			=> '',
			'show_image'				=> 1,
			'custom_image'				=> '',
			'attachment_id'				=> 0,
			'image_size'      			=> '',
			'background_fixed'			=> '',
			'background_color'			=> '',
			'show_title'      			=> 0,
			'show_content'    			=> 0,
			'content_limit'   			=> '',
			'more_text'       			=> '',
		);

		$widget_ops = array(
			'classname'   => 'widget_background featuredpage',
			'description' => __( 'Displays featured page with images and content.' , 'genesis-widget-background' ),
		);

		$control_ops = array(
			'id_base' => 'genesis-widget-background',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'genesis-widget-background', __( 'Genesis - Widget Background', 'genesis-widget-background' ), $widget_ops, $control_ops );

		//* Enqueue Admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'wpstudio_admin_scripts_enqueue' ) );

	}

	/**
	 * Echo the widget content on the frontend.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		global $wp_query;

		extract( $args );

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;
			

		$wp_query = new WP_Query( array( 'page_id' => $instance['page_id'] ) );

		if (!defined('NO_BG')) define('NO_BG', '1');
		if (!defined('FEATURED_BG')) define('FEATURED_BG', '2');
		if (!defined('CUSTOM_BG')) define('CUSTOM_BG', '3');
		if (!defined('COLOR_BG')) define('COLOR_BG', '4');

		$image = genesis_get_image( array( 
			'format' => 'url', 
			'size' => $instance['image_size'],
			'context' => 'featured-page-widget',
			'attr'    => genesis_parse_attr( 'entry-image-widget' ),
		) );

		if ( have_posts() ) : while ( have_posts() ) : the_post();

			if ( $instance['feature_type'] == 'page' && $instance['show_image'] == FEATURED_BG && $instance['background_fixed'] && $image ) {
				genesis_markup( array(
					'html5'   => '<article class="image-section fixed" style="background-image:url(' . $image . ')"><div class="hook-img-fix">',
					'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
					'context' => 'entry',
				) );
			}
			elseif ( $instance['feature_type'] == 'page' && $instance['show_image'] == FEATURED_BG && !$instance['background_fixed'] && $image ) {
				genesis_markup( array(
					'html5'   => '<article class="image-section non-fixed" style="background-image:url(' . $image . ')"><div class="hook-img">',
					'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
					'context' => 'entry',
				) );
			}

			elseif ( $instance['show_image'] == CUSTOM_BG  && $instance['background_fixed'] ) {
				genesis_markup( array(
					'html5'   => '<article class="image-section fixed" style="background-image:url(' . $instance['custom_image'] . ')"><div class="hook-img-fix">',
					'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
					'context' => 'entry',
				) );
			}

			elseif ( $instance['show_image'] == CUSTOM_BG && !$instance['background_fixed'] ) {
				genesis_markup( array(
					'html5'   => '<article class="image-section non-fixed" style="background-image:url(' . $instance['custom_image'] . ')"><div class="hook-img">',
					'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
					'context' => 'entry',
				) );
			}

			elseif ( $instance['show_image'] == COLOR_BG ) {
				genesis_markup( array(
					'html5'   => '<article class="image-section bg_color" style="background-color:' . $instance['background_color'] . '"><div class="hook-color">',
					'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
					'context' => 'entry',
				) );
			}

			else {
				genesis_markup( array(
					'html5'   => '<article class="image-section"><div class="hook-blank">',
					'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
					'context' => 'entry',
				) );
			}
	
			
			//* Display page content
			if ( $instance['feature_type'] == 'page' && ! empty( $instance['show_content'] ) ) {
				
				echo genesis_html5() ? '<div class="wrap">' : '';
				echo $instance['title'];
				if ( ! empty( $instance['show_title'] )) {
					if ( genesis_html5() )
				printf( '<header class="entry-header"><h2 class="entry-title"><a href="%s" title="%s">%s</a></h2></header>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() );
				else
				printf( '<h2><a href="%s" title="%s">%s</a></h2>', 
					get_permalink(), the_title_attribute( 'echo=0' ), get_the_title());
				}
				
				
				if ( empty( $instance['content_limit'] ) ) {
					the_content( $instance['more_text'] );
				} else {
					the_content_limit( (int) $instance['content_limit'], esc_html( $instance['more_text'] ) );
				}
				
				echo genesis_html5() ? '</div>' : '';

			}

			genesis_markup( array(
				'html5' => '</div></article>',
				'xhtml' => '</div></div>',
			) );

			endwhile;
		endif;

		//* Restore original query
		wp_reset_query();

		echo $after_widget;

	}


	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$new_instance['title']     			= strip_tags( $new_instance['title'] );
		$new_instance['custom_link'] 		= strip_tags( $new_instance['custom_link'] );
		$new_instance['custom_image']       = strip_tags( $new_instance['custom_image'] );
		$new_instance['custom_content']     = $new_instance['custom_content'];
		$new_instance['more_text'] 			= strip_tags( $new_instance['more_text'] );
		$new_instance['fixed_background'] 	= $new_instance['fixed_background'];
		$new_instance['background_color'] 	= $new_instance['background_color'];
		return $new_instance;

	}


	/**
	 * Echo the settings update form on admin widget page.
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		//* Merge with defaults 
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		//* Gets widget id prefix, very important for image uploader
		$id_prefix = $this->get_field_id('');

		?>
		
		<!--Widget Title Block-->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis-widget-background' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
	
		<hr class="div" />
		
		<!--Featured Page Selection-->
		<div class="<?php if ( $instance['feature_type'] != 'page' ) echo ('hidden');  ?>" id="<?php echo $this->get_field_id('wpstudio_widget_background'); ?>" >
			<p>
				<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php _e( 'Select Page', 'genesis-widget-background' ); ?>:</label>
				<?php wp_dropdown_pages( array( 'name' => $this->get_field_name( 'page_id' ), 'selected' => $instance['page_id'] ) ); ?>
			</p>
		</div>
		<hr class="div" />
		
		<!--Image Type Selection-->
		<div class="wpstudio-show-image wpstudio-radio">
			<label for="<?php echo $this->get_field_id( 'Select Background:' ); ?>"><?php _e( 'Select Background Image', 'genesis-widget-background' ); ?>:</label><br>
			<label for="<?php echo $this->get_field_id( 'show_no_image' ); ?>">
				<input type="radio" id="<?php echo $this->get_field_id( 'show_no_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="1" <?php checked( 1, $instance['show_image'] ); ?> />
				<span><?php _e( 'No Image', 'genesis-widget-background' ); ?></span>
			</label><br />
			<label for="<?php echo $this->get_field_id( 'show_featured_image' ); ?>">
				<input type="radio" id="<?php echo $this->get_field_id( 'show_featured_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" <?php if ( $instance['feature_type'] != 'page' ) echo ('disabled'); ?> value="2" <?php checked( 2, $instance['show_image'] ); ?> />
				<span class="<?php if ( $instance['feature_type'] != 'page' ) echo ('wpstudio-disabled'); ?>" id="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Show Featured Image', 'genesis-widget-background' ); ?></span>
			</label><br />
			<label for="<?php echo $this->get_field_id( 'show_custom_image' ); ?>">
				<input type="radio" id="<?php echo $this->get_field_id( 'show_custom_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="3" <?php checked( 3, $instance['show_image'] ); ?> />
				<span><?php _e( 'Show Custom Image', 'genesis-widget-background' ); ?></span>
			</label>
			<label for="<?php echo $this->get_field_id( 'show_background_color' ); ?>">
				<input type="radio" id="<?php echo $this->get_field_id( 'show_background_color' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="4" <?php checked( 4, $instance['show_image'] ); ?> />
				<span><?php _e( 'Background Color', 'genesis-widget-background' ); ?></span>
			</label>
		</div>
		
		<!--Show Featured Image-->
		<div class="wpstudio-image-size <?php if ( $instance['show_image'] != 2 ) echo ('hidden');  ?>" id="<?php echo $this->get_field_id('toggle_image_size'); ?>" >
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Image Size', 'genesis-widget-background' ); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'image_size' ); ?>" class="genesis-image-size-selector" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
				<option value="thumbnail" <?php selected( 'thumbnail', $instance[ 'image_size' ] ); ?>>Thumbnail (<?php echo absint( get_option( 'thumbnail_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'thumbnail_size_h' ) ); ?>)</option>
				<option value="medium" <?php selected( 'medium', $instance[ 'image_size' ] ); ?>>Medium (<?php echo absint( get_option( 'medium_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'medium_size_h' ) ); ?>)</option>
				<option value="large" <?php selected( 'large', $instance[ 'image_size' ] ); ?>>Large (<?php echo absint( get_option( 'large_size_w' ) ); ?>&#x000D7;<?php echo absint( get_option( 'large_size_h' ) ); ?>)</option>
				<option value="full" <?php selected( 'full', $instance[ 'image_size' ] ); ?>>Full (<?php _e( 'Original Image Size', 'genesis-widget-background' ); ?>)</option>
				<?php
				$sizes = genesis_get_additional_image_sizes();
				foreach ( (array) $sizes as $name => $size )
					echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $instance['image_size'], FALSE ) . '>' . esc_html( $name ) . ' (' . absint( $size['width'] ) . 'x' . absint( $size['height'] ) . ')</option>';
				?>
			</select>
		</div>
		
		<!--Show Custom Image-->		
		<div class="<?php if ( $instance['show_image'] != 3 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_uploader'); ?>"  >
			<input type="submit" class="button fpa-uploader-button" name="<?php echo $this->get_field_name('uploader_button'); ?>" id="<?php echo $this->get_field_id('uploader_button'); ?>" value="<?php _e( 'Select an Image', 'genesis-widget-background' ); ?>" onclick="fpa_imageUpload.uploader( '<?php echo $this->id; ?>', '<?php echo $id_prefix; ?>' ); return false;" />
			<div class="wpstudio-image-preview-wrapper">
				<div class="wpstudio-image-preview-inner">
				<?php if ( !empty( $instance['custom_image'] ) ) {?>
					<img id="<?php echo $this->get_field_id('preview'); ?>" src="<?php echo $instance['custom_image']; ?>" /> 
				<?php } else {?>
					<img id="<?php echo $this->get_field_id('preview'); ?>" src="<?php echo plugin_dir_url( __FILE__ ) ?>../images/default.jpg" /> 
				<?php }?>
				</div>
			</div>
			<input type="hidden" id="<?php echo $this->get_field_id('attachment_id'); ?>" name="<?php echo $this->get_field_name('attachment_id'); ?>" value="<?php echo abs($instance['attachment_id']); ?>" />
			<input type="hidden" id="<?php echo $this->get_field_id('custom_image'); ?>" name="<?php echo $this->get_field_name('custom_image'); ?>" value="<?php echo $instance['custom_image']; ?>" />
		</div>

		<!--Background Color-->
		<div class="wpstudio-background_color <?php if ( $instance['background_color'] != 4 ) echo ('show');  ?>" id="<?php echo $this->get_field_id('toggle_background_color'); ?>" >
		<label for="<?php echo $this->get_field_id( 'background_color' ); ?>"><?php _e( 'Background Color', 'genesis-widget-background' ); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id( 'background_color' ); ?>" name="<?php echo $this->get_field_name( 'background_color' ); ?>" value="<?php echo esc_attr( $instance['background_color'] ); ?>" />
		</div>

		<!--Set Background Fixed-->
		<div class="wpstudio-background_fixed <?php if ( $instance['background_fixed'] != 2 ) echo ('show');  ?>" id="<?php echo $this->get_field_id('toggle_background_fixed'); ?>" >
			<hr class="div" />
			<input class="checkbox" type="checkbox" <?php checked($instance['background_fixed'], 'on'); ?> id="<?php echo $this->get_field_id('background_fixed'); ?>" name="<?php echo $this->get_field_name('background_fixed'); ?>" />
			<label for="<?php echo $this->get_field_id( 'background_fixed' ); ?>"><?php _e( 'Background Fixed', 'genesis-widget-background' ); ?></label>
		</div>

		<hr class="div" />

		<!--Featured Page Specific Settings - Hide if using Custom Link-->
		<div class="<?php if ( $instance['feature_type'] != 'page' ) echo ('hidden');  ?>" id="<?php echo $this->get_field_id('wpstudio_widget_background_settings'); ?>" >

			<!--Page Title Block-->
			<p class="wpstudio-toggle-page-settings">
				<input id="<?php echo $this->get_field_id( 'show_title' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>" value="1" <?php checked( $instance['show_title'] ); ?> />
				<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Show Page Title', 'genesis-widget-background' ); ?></label>
			</p>
		
			<hr class="div" />
		
			<!--Page Content Block-->
			<p class="wpstudio-toggle-content-limit">
				<input id="<?php echo $this->get_field_id( 'show_content' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_content' ); ?>" value="1" <?php checked( $instance['show_content'] ); ?> />
				<label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php _e( 'Show Page Content', 'genesis-widget-background' ); ?></label>
			</p>
			<p class="<?php if ( $instance['show_content'] != 1 ) echo ('hidden'); ?>" id="<?php echo $this->get_field_id('toggle_content_limit'); ?>">
				<label for="<?php echo $this->get_field_id( 'content_limit' ); ?>"><?php _e( 'Content Character Limit', 'genesis-widget-background' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'content_limit' ); ?>" name="<?php echo $this->get_field_name( 'content_limit' ); ?>" value="<?php echo esc_attr( $instance['content_limit'] ); ?>" size="3" />
			</p>
		
		</div>
		
		<hr class="div" />

		<!--Read More Button/Text-->
		<p class="wpstudio-read-more">
			<label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php _e( 'More Text', 'genesis-widget-background' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
		</p>
		<?php

	}
	
	
	/**
	 * Enqueue Admin scripts and styles
	 */
	function wpstudio_admin_scripts_enqueue( $hook ) {

		//* Do no enqueue scripts & styles if we are not on either the Widget or Customizer pages	
		if ( 'widgets.php' == $hook || 'customize.php' == $hook ) {
	
			//* Enqueues all media scripts so we can use the media uploader
			wp_enqueue_media(); 

			wp_register_script( 'wpstudio-admin-scripts', plugin_dir_url( __FILE__ ) . '../js/wpstudio-admin-scripts.js', array( 'jquery' ) );
			wp_enqueue_script( 'wpstudio-admin-scripts' );
		
			wp_register_style( 'wpstudio-admin-styles', plugin_dir_url( __FILE__ ) . '../css/wpstudio-admin-styles.css' );
			wp_enqueue_style( 'wpstudio-admin-styles' );


		} else {
			return;
		}
	}
	
}