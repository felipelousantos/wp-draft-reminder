<?php

/**
 * Create the option page.
 * Hook: admin_menu
 *
 * @return void
 */
function draftreminder_options_page() {

	add_options_page(
		'Draft Reminder Options',
		'Draft Reminder',
		'manage_options',
		'draft-reminder',
		'draftreminder_options_page_html'
	);

}
add_action( 'admin_menu', 'draftreminder_options_page' );

/**
 * Create the option page HTML.
 *
 * @return void
 */
function draftreminder_options_page_html() {
	?>
	<div class="wrap">
		<h1>Draft Reminder Options</h1>
		<form action="options.php" method="post">
			<?php settings_fields('draftreminder_options') ?>
			<?php do_settings_sections('draftreminder_options') ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Registering the plugin options.
 * Hook: admin_init
 *
 * @return void
 */
function register_options() {

	register_setting(
		'draftreminder_options',
		'draftreminder_posts_total',
		[
			'type' => 'number',
			'sanitize_callback' => function ($value) {
				if ($value < 1 || $value > 200) {
					add_settings_error(
						'draftreminder_posts_total',
						'invalid_value',
						'The total of posts for Draft Reminder needs to be more than 1 and less than 200.',
						'error'
					);
					return get_option('draftreminder_posts_total');
				}

				return $value;
			},
		]
	);

	register_setting(
		'draftreminder_options',
		'draftreminder_post_types',
		[
			'type' => 'array',
			'sanitize_callback' => function($value) {
				return $value;
			}
		]
	);

	register_setting(
		'draftreminder_options',
		'draftreminder_day_of_the_week',
		[
			'type' => 'string',
			'sanitize_callback' => function ($value) {
				$days_of_the_week = get_days_of_the_week();
				if ( key_exists( $value , $days_of_the_week ) ) {
					return $value;
				} else {
					add_settings_error(
						'draftreminder_day_of_the_week',
						'invalid_value',
						'The value is not valid.',
						'error'
					);
				}
			},
		]
	);

}
add_action( 'admin_init', 'register_options');

/**
 * Show the options for the plugin.
 * Hook: admin_init
 *
 * @return void
 */
function show_options() {

	// Show Post Total field
	add_settings_field(
		'draftreminder_posts_total',
		'Posts total',
		function ($args) {
			$value = get_option('draftreminder_posts_total', 50);
			?>
			<input type="number" name="draftreminder_posts_total" id="<?php echo $args['label_for']; ?>" value="<?php echo $value; ?>">
			<?php
		},
		'draftreminder_options',
		'draftreminder_options_section',
		[
			'label_for' => 'draftreminder_posts_total',
			'class' => 'draftreminder draftreminder_posts_total'
		]
	);

	// Show Post Type field
	add_settings_field(
		'draftreminder_post_types',
		'Post Types',
		function ($args) {
			$custom_post_types = get_post_types(
				[
					'public'   => true,
					'_builtin' => false
				]
			);
			$native_post_types = [
				'post' => 'post',
				'page' => 'page'
			];
			$post_types = array_merge($native_post_types, $custom_post_types);
			$post_types_selected = get_option( 'draftreminder_post_types', ['post'] );
			foreach($post_types as $option) {
				$checked = in_array($option , $post_types_selected) ? 'checked' : '' ;

				?>
				<div class="draftreminder_post_types_options">
					<input type="checkbox" id="<?php echo $option ?>" name="draftreminder_post_types[]" value="<?php echo $option ?>"<?php echo $checked ?>>
					<label for="<?php echo $option ?>"><?php echo $option ?></label>
				</div>
				<?php
			}
		},
		'draftreminder_options',
		'draftreminder_options_section'
	);


	// Show Day of the week field
	add_settings_field(
		'draftreminder_day_of_the_week',
		'Day of the week',
		function ($args) {
			$days_of_the_week = get_days_of_the_week();
			$selected_day = get_option( 'draftreminder_day_of_the_week' );
			?>
				<select name="draftreminder_day_of_the_week" id="draftreminder_day_of_the_week">
					<?php foreach($days_of_the_week as $key => $value  ) { ?>
						<option value="<?php echo $key; ?>" <?php selected( $key , $selected_day ) ?>><?php echo $value; ?></option>
					<?php } ?>
				</select>
			<?php
		},
		'draftreminder_options',
		'draftreminder_options_section'
	);
}
add_action( 'admin_init', 'show_options');

/**
 * Creates the section for the option page.
 * Hook: admin_init
 *
 * @return void
 */
function section_creation(){
	add_settings_section(
		'draftreminder_options_section',
		'',
		function () {},
		'draftreminder_options'
	);
}
add_action( 'admin_init', 'section_creation' );
