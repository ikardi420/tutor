<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Tutor general Functions
 */

if ( ! function_exists('tutor_withdrawal_methods')){
	function tutor_withdrawal_methods(){
		$withdraw = new \TUTOR\Withdraw();

		return $withdraw->available_withdraw_methods;
	}
}

if ( ! function_exists('tutor_placeholder_img_src')) {
	function tutor_placeholder_img_src() {
		$src = tutor()->url . 'assets/images/placeholder.jpg';
		return apply_filters( 'tutor_placeholder_img_src', $src );
	}
}

/**
 * @return string
 *
 * Get course categories selecting UI
 *
 * @since v.1.3.4
 */

if ( ! function_exists('tutor_course_categories_dropdown')){
	function tutor_course_categories_dropdown($post_ID = 0, $args = array()){

		$default = array(
			'classes'  => '',
			'name'  => 'tax_input[course-category]',
			'multiple' => true,
		);

		$args = apply_filters('tutor_course_categories_dropdown_args', array_merge($default, $args));

		$multiple_select = '';

		if (tutor_utils()->array_get('multiple', $args)){
			if (isset($args['name'])){
				$args['name'] = $args['name'].'[]';
			}
			$multiple_select = "multiple='multiple'";
		}

		extract($args);

		$classes = (array) $classes;
		$classes = implode(' ', $classes);

		$categories = tutor_utils()->get_course_categories();

		$output = '';
		$output .= "<select name='{$name}' {$multiple_select} class='{$classes}' data-placeholder='". __('Search Course Category. ex. Design, Development, Business', 'tutor') ."'>";
		$output .= "<option value=''>". __('Select a category', 'tutor') ."</option>";
		$output .= _generate_categories_dropdown_option($post_ID, $categories, $args);
		$output .= "</select>";

		return $output;
	}
}

/**
 * @param $categories
 * @param string $parent_name
 *
 * @return string
 *
 * Get selecting options, recursive supports
 *
 * @since v.1.3.4
 */

if ( ! function_exists('_generate_categories_dropdown_option')){
	function _generate_categories_dropdown_option($post_ID = 0, $categories, $args = array(), $depth = 0){
		$output = '';

		if (tutor_utils()->count($categories)) {
			foreach ( $categories as $category_id => $category ) {
				if ( ! $category->parent){
					$depth = 0;
				}

				$childrens = tutor_utils()->array_get( 'children', $category );
				$has_in_term = has_term( $category->term_id, 'course-category', $post_ID );

				$depth_seperator = '';
				if ($depth){
					for ($depth_i = 0; $depth_i < $depth; $depth_i++){
						$depth_seperator.='-';
					}
				}

				$output .= "<option value='{$category->term_id}' ".selected($has_in_term, true, false)." >   {$depth_seperator} {$category->name}</option> ";

				if ( tutor_utils()->count( $childrens ) ) {
					$depth++;
					$output .= _generate_categories_dropdown_option($post_ID,$childrens, $args, $depth);
				}
			}
		}
		return $output;
	}
}

/**
 * @param array $args
 *
 * @return string
 *
 * Generate course categories checkbox
 * @since v.1.3.4
 */

if ( ! function_exists('tutor_course_categories_checkbox')){
	function tutor_course_categories_checkbox($post_ID = 0, $args = array()){
		$default = array(
			'name'  => 'tax_input[course-category]',
		);

		$args = apply_filters('tutor_course_categories_checkbox_args', array_merge($default, $args));

		if (isset($args['name'])){
			$args['name'] = $args['name'].'[]';
		}

		extract($args);

		$categories = tutor_utils()->get_course_categories();
		$output = '';
		$output .= __tutor_generate_categories_checkbox($post_ID, $categories, $args);

		return $output;
	}
}

/**
 * @param $categories
 * @param string $parent_name
 * @param array $args
 *
 * @return string
 *
 * Internal function to generate course categories checkbox
 *
 * @since v.1.3.4
 */
if ( ! function_exists('__tutor_generate_categories_checkbox')){
	function __tutor_generate_categories_checkbox($post_ID = 0, $categories, $args = array()){
		$output = '';
		$input_name = tutor_utils()->array_get('name', $args);

		if (tutor_utils()->count($categories)) {
			$output .= "<ul class='tax-input-course-category'>";
			foreach ( $categories as $category_id => $category ) {
				$childrens = tutor_utils()->array_get( 'children', $category );
				$has_in_term = has_term( $category->term_id, 'course-category', $post_ID );

				$output .= "<li class='tax-input-course-category-item tax-input-course-category-item-{$category->term_id} '><label class='course-category-checkbox'> <input type='checkbox' name='{$input_name}' value='{$category->term_id}' ".checked($has_in_term, true, false)." /> <span>{$category->name}</span> </label>";

				if ( tutor_utils()->count( $childrens ) ) {
					$output .= __tutor_generate_categories_checkbox($post_ID,$childrens, $args);
				}
				$output .= " </li>";
			}
			$output .= "</ul>";
		}
		return $output;
	}
}

/**
 * @param string $content
 * @param string $title
 *
 * @return string
 *
 * Wrap course builder sections within div for frontend
 *
 * @since v.1.3.4
 */

if ( ! function_exists('course_builder_section_wrap')) {
	function course_builder_section_wrap( $content = '', $title = '', $echo = true ) {
		ob_start();
		?>
        <div class="tutor-course-builder-section">
            <div class="tutor-course-builder-section-title">
                <h3><i class="tutor-icon-down"></i> <span><?php echo $title; ?></span></h3>
            </div>
            <div class="tutor-course-builder-section-content">
				<?php echo $content; ?>
            </div>
        </div>
		<?php
		$html = ob_get_clean();

		if ($echo){
			echo $html;
		}else{
			return $html;
		}
	}
}


if ( ! function_exists('get_tutor_header')){
	function get_tutor_header($fullScreen = false){
		$enable_spotlight_mode = tutor_utils()->get_option('enable_spotlight_mode');

		if ($enable_spotlight_mode || $fullScreen){
			?>
            <!doctype html>
            <html <?php language_attributes(); ?>>
            <head>
                <meta charset="<?php bloginfo( 'charset' ); ?>" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <link rel="profile" href="https://gmpg.org/xfn/11" />
				<?php wp_head(); ?>
            </head>
            <body <?php body_class(); ?>>
            <div id="tutor-page-wrap" class="tutor-site-wrap site">
			<?php
		}else{
			get_header();
		}

	}
}

if (! function_exists('get_tutor_footer')){
	function get_tutor_footer($fullScreen = false){
		$enable_spotlight_mode = tutor_utils()->get_option('enable_spotlight_mode');
		if ($enable_spotlight_mode || $fullScreen){
			?>
            </div>
			<?php wp_footer(); ?>

            </body>
            </html>
			<?php
		}else{
			get_footer();
		}
	}
}

/**
 * @param int $parent_id
 * @param array $level_categories
 *
 * Generate Courses categories for Paid Memberships Pro
 *
 * @since v.1.3.6
 */
if ( ! function_exists('generate_categories_for_pmpro')) {
	function generate_categories_for_pmpro( $parent_id = 0, $level_categories = array() ) {
		$args = array(
			'taxonomy'   => 'course-category',
			'parent'     => $parent_id,
			'hide_empty' => false,
		);
		$cats = get_categories( apply_filters( 'course_categories_pmpro_args', $args ) );
		if ( $cats ) {
			foreach ( $cats as $cat ) {
				$name = 'membershipcategory_' . $cat->term_id;
				if ( ! empty( $level_categories ) ) {
					$checked = checked( in_array( $cat->term_id, $level_categories ), true, false );
				} else {
					$checked = '';
				}
				echo "<ul><li class=membershipcategory><input type=checkbox name={$name} id={$name} value=yes {$checked}><label for={$name}>{$cat->name}</label>";
				generate_categories_for_pmpro( $cat->term_id, $level_categories );
				echo '</li></ul>';
			}
		}
	}
}

/**
 * @param null $key
 * @param bool $default
 *
 * @return array|bool|mixed
 *
 * Get tutor option by this helper function
 *
 * @since v.1.3.6
 */
if ( ! function_exists('get_tutor_option')){
	function get_tutor_option($key = null, $default = false){
		return tutils()->get_option($key, $default);
	}
}

/**
 * @param null $key
 * @param bool $value
 *
 * Update tutor option by this helper function
 *
 * @since v.1.3.6
 */
if ( ! function_exists('update_tutor_option')){
	function update_tutor_option($key = null, $value = false){
		tutils()->update_option($key, $value);
	}
}
/**
 * @param int $course_id
 * @param null $key
 * @param bool $default
 *
 * @return array|bool|mixed
 *
 * Get tutor course settings by course ID
 *
 * @since v.1.4.1
 */
if ( ! function_exists('get_tutor_course_settings')) {
	function get_tutor_course_settings( $course_id = 0, $key = null, $default = false ) {
		return tutils()->get_course_settings( $course_id, $key, $default );
	}
}

/**
 * @param int $lesson_id
 * @param null $key
 * @param bool $default
 *
 * @return array|bool|mixed
 *
 * Get lesson content drip settings
 */

if ( ! function_exists('get_item_content_drip_settings')){
	function get_item_content_drip_settings($lesson_id = 0, $key = null, $default = false){
		return tutils()->get_item_content_drip_settings( $lesson_id, $key, $default );
	}
}

/**
 * @param null $msg
 * @param string $type
 * @param bool $echo
 *
 * @return string
 *
 * Print Alert by tutor_alert()
 *
 * @since v.1.4.1
 */
if ( ! function_exists('tutor_alert')){
	function tutor_alert($msg = null, $type = 'warning', $echo = true){
	    if ( ! $msg){

	        if ($type === 'any'){
		        if ( ! $msg){
			        $type = 'warning';
			        $msg = tutor_flash_get($type);
		        }
		        if ( ! $msg){
			        $type = 'danger';
			        $msg = tutor_flash_get($type);
		        }
		        if ( ! $msg){
			        $type = 'success';
			        $msg = tutor_flash_get($type);
		        }

	        }else{
		        $msg = tutor_flash_get($type);
	        }


        }
        if ( ! $msg){
	        return $msg;
        }

		$html = "<div class='tutor-alert tutor-alert-{$type}'>{$msg}</div>";
		if ($echo){
			echo $html;
		}
		return $html;
	}
}


/**
 * @param bool $echo
 *
 * Simply call tutor_nonce_field() to generate nonce field
 *
 * @since v.1.4.2
 */

if ( ! function_exists('tutor_nonce_field')) {
	function tutor_nonce_field( $echo = true ) {
		wp_nonce_field( tutor()->nonce_action, tutor()->nonce, $echo );
	}
}

/**
 * @param null $key
 * @param string $message
 *
 * Set Flash Message
 */

if ( ! function_exists('tutor_flash_set')) {
	function tutor_flash_set( $key = null, $message = '' ) {
		if ( ! $key ) {
			return;
		}
		// ensure session is started
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			session_start();
		}
		$_SESSION[ $key ] = $message;
	}
}

/**
 * @param null $key
 *
 * @return array|bool|mixed|null
 *
 * @since v.1.4.2
 *
 * Get flash message
 */

if ( ! function_exists('tutor_flash_get')) {
	function tutor_flash_get( $key = null ) {
		if ( $key ) {
			// ensure session is started
			if ( session_status() !== PHP_SESSION_ACTIVE ) {
				@session_start();
			}
			if ( empty( $_SESSION ) ) {
				return null;
			}
			$message = tutils()->array_get( $key, $_SESSION );
			if ( $message ) {
				unset( $_SESSION[ $key ] );
			}
			return $message;
		}
		return $key;
	}
}

if ( ! function_exists('tutor_redirect_back')) {
	/**
	 * @param null $url
     *
     * Redirect to back or a specific URL and terminate
     *
     * @since v.1.4.3
	 */
	function tutor_redirect_back( $url = null ) {
		if ( ! $url ) {
			$url = tutils()->referer();
		}
		wp_safe_redirect( $url );
		exit();
	}
}

/**
 * @param string $action
 * @param bool $echo
 *
 * @return string
 *
 * @since v.1.4.3
 */

if ( ! function_exists('tutor_action_field')) {
	function tutor_action_field( $action = '', $echo = true ) {
		$output = '';
		if ( $action ) {
			$output = "<input type='hidden' name='tutor_action' value='{$action}'>";
		}

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}
}