<?php
if ( ! class_exists( 'RGBC_RE' ) ) {
	class RGBC_RE {
		/* визиваєм хукі */
		function register() {
			add_action( 'admin_enqueue_scripts', [
				$this,
				'enqueue_admin',
			] );
			add_action( 'wp_enqueue_scripts', [
				$this,
				'enqueue_front',
			] );

			add_action( 'plugins_loaded', [
				$this,
				'load_text_domain',
			] );/* підключаєм метод в register к хукам */

			add_action( 'widgets_init', [
				$this,
				'register_widget',
			] );

			/*add_action( 'admin_menu', [
				$this,
				'add_menu_item',
			] );*/
			add_action( 'admin_menu', [
				$this,
				'real_estate_object_submenu_page',
			] );

			// додавання Settings page link у назву плагіна
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [
				$this,
				'add_plugin_settings_link',
			] );

			add_action( 'admin_init', [
				$this,
				'settings_init',
			] ); // хук
		}

		public function settings_init() {
			$option_name = 'filter_title';

			register_setting( 'real_estate_settings', $option_name );// реэструем налаштування для сторінки з налаштуваннями плагіна

			add_settings_section(
				'real_estate_settings_section',
				esc_html__( 'Settings', 'real_estate_object' ),
				[
					$this,
					'real_estate_settings_html',
				],
				'real_estate_settings_page'
			);

			//add_submenu_page() todo

			add_settings_field(
				$option_name, // ID опции
				esc_html__( 'Title for FIlter', 'real_estate_object' ),
				[
					$this,
					'true_option_callback',
				],
				'real_estate_settings_page',
				'real_estate_settings_section',
				array(
					'label_for' => $option_name,
					// можно оставить только label_for кстати
					'name'      => $option_name,
				)
			);
		}

		public static function true_option_callback( $args ) {
			printf(
				'<input type="text" id="%s" name="%s" placeholder="Укажите значение..." value="%s" />',
				$args['label_for'],
				$args['name'],
				esc_attr( get_option( $args['name'] ) )
			);
		}

		//echo $options['filter_title'] ?: '';/*перевірка що значення не пусте (якщо це false, 0,'' - виконається права сторона '' )*/
		//echo $options['filter_title'] ?? 'simple text';/*перевірка на існування як isset() якщо не існує то береться права сторона, якщо існує то ліва*/

		//Створюэмо метод
		public function filter_title_html() {
			$options = get_option( 'filter_title' );
			var_dump( $options );
			$filter_title = isset( $options['filter_title'] ) && $options['filter_title'] ? $options['filter_title'] : '';
			?>
			<input
				type="text"
				name="real_estate_settings_options[filter_title]"
				value="<?php echo esc_attr( $filter_title ); ?>"
			/>
			<?php
		}

		public function real_estate_settings_html() {
			esc_html_e( 'Settings for Real Estate Plugin' );
		}

		public function add_plugin_settings_link( $link ) {
			$real_estate_link =
				/*'<a href="admin.php?page=real_estate_settings">'*/
				'<a href="options-general.php?page=real_estate_object_submenu_page">'
				. esc_html__( 'Settings Page', 'real_estate_object' ) .
				'</a>';
			array_push( $link, $real_estate_link );

			return $link;
		}

		/*public function add_menu_item() {
			add_menu_page(
				esc_html__( 'Real Estate Settings Page', 'real_estate_object' ),
				esc_html__( 'Real Estate Settings', 'real_estate_object' ),
				'manage_options',
				'real_estate_settings',
				[
					$this,
					'main_admin_page',
				],
				'dashicons-editor-kitchensink',
				50
			);
		}*/
		public function real_estate_object_submenu_page() {
			add_submenu_page(
				'options-general.php',
				'real_estate_settings_page',
				'Real Estate Settings',
				'manage_options',
				'real_estate_object_submenu_page',
				[
					$this,
					'real_estate_object_submenu_page_callback',
				]

			);
		}

		public function real_estate_object_submenu_page_callback() {
			// контент страницы
			echo '<div class="wrap">';
			require_once RGBC_RE_PATH . 'admin/welcome.php';
			echo '</div>';
		}

		/*public function main_admin_page() {
			require_once RGBC_RE_PATH . 'admin/welcome.php';
		}*/

		public function register_widget() {
			register_widget( 'real_estate_filter_widget' );
		}

		public function get_terms_hierarchical( $tax_name, $current_term ) {

			$taxonomy_terms = get_terms( $tax_name, [
				'hide_empty' => 'false',
				'parent'     => 0,
			] );

			$html = '';
			if ( ! empty( $taxonomy_terms ) ) {
				foreach ( $taxonomy_terms as $term ) {
					if ( $current_term == $term->term_id ) {
						$html .= '<option value="' . $term->term_id . '" selected >' . $term->name . '</option>';
					} else {
						$html .= '<option value="' . $term->term_id . '" >' . $term->name . '</option>';
					}

					$child_terms = get_terms( $tax_name, [
						'hide_empty' => false,
						'parent'     => $term->term_id,
					] );

					if ( ! empty( $child_terms ) ) {
						foreach ( $child_terms as $child ) {
							if ( $current_term == $child->term_id ) {
								$html .= '<option value="' . $child->term_id . '" selected > - ' . $child->name . '</option>';
							} else {
								$html .= '<option value="' . $child->term_id . '" > - ' . $child->name . '</option>';
							}
						}
					}
				}
			}

			return $html;
		}

		/* додаєм метод роботу плагіну load_plugin_textdomain */
		function load_text_domain() {
			load_plugin_textdomain( 'real_estate_object', false, RGBC_RE_DOMAIN );
		}

		/* підключення стилей та скриптів */
		public function enqueue_admin() {
			wp_enqueue_style( 'rgbc_real_estate_style_admin', RGBC_RE_URL . 'assets/css/admin/style.css' );
			wp_enqueue_script( 'rgbc_real_estate_script_admin', RGBC_RE_URL . 'assets/js/admin/script.js', array( 'jquery' ), RGBC_RE_VERSION, true );/* array('jequery') - можно и не писати, true тому що в футері підключаєм */
		}

		public function enqueue_front() {
			wp_enqueue_style( 'rgbc_real_estate_style', RGBC_RE_URL . 'assets/css/front/style.css' );
			wp_enqueue_script( 'rgbc_real_estate_script', RGBC_RE_URL . 'assets/js/front/script.js', array( 'jquery' ), RGBC_RE_VERSION, true );/* array('jequery') - можно и не писати, true тому що в футері підключаєм */
		}

		/* функції активації\деактивації плагіна */
		static function activation() {
			flush_rewrite_rules();
		}

		static function deactivation() {
			flush_rewrite_rules();
		}
	}
}
