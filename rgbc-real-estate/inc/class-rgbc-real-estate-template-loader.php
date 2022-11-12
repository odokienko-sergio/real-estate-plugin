<?php

class real_estate_object_Template_Loader extends Gamajo_Template_Loader {

	protected $filter_prefix = 'real_estate_object';

	protected $theme_template_directory = 'real_estate_object';

	protected $plugin_directory = RGBC_RE_PATH;

	protected $plugin_template_directory = 'templates';
	/*додаємо нові кастом темплейти для плагіну*/
	public $templates;

	public function register() {
		add_filter( 'template_include', [
			$this,
			'real_estate_objects_templates',
		] );

		$this->templates = array(
			'tpl/template-add-re-object.php' => 'Add Real Estate Object', /*ми створюємо змінну де прописуемо шлях*/
		);

		add_filter( 'theme_page_templates', [
			$this,
			'custom_template',
		] );/*ми чіпляємось до theme_page_templates, зараз ми указали щр в нас є custom_template*/

		add_filter( 'template_include', [
			$this,
			'load_template',
		] );
	}


	public function load_template( $template ) {

		global $post;

		$template_name = get_post_meta(
			$post->ID,
			'_wp_page_template',
			true
		);

		if ( $template_name && $this->templates[ $template_name ] ) { /*робимо перевірку, якщо на поточному файлі у нас відповідний нєобхідний для нас темплейт значить ми грузимо саме цей файл*/
			$file = RGBC_RE_PATH . $template_name;
			if ( file_exists( $file ) ) {
				return $file;
			}
		}
		return $template;
	}

	public function custom_template( $templates ) {/*оскільки це фільтр, усі темплейти які у нас є в вордпрессі ми повинні отримуємо тут*/

		$templates = array_merge( $templates, $this->templates );

		return $templates;  /*а потім в кінці повертаємо*/
	}

	public function real_estate_objects_templates( $template ) {

		if ( is_post_type_archive( 'real_estate_object' ) ) {
			$theme_files = [
				'archive-real_estate_object.php',
				'rgbc-real-estate/archive-real_estate_object.php',
			];
			$exist       = locate_template( $theme_files, false );
			if ( $exist != '' ) {
				return $exist;
			} else {
				return plugin_dir_path( __DIR__ ) . 'templates/archive-real_estate_object.php';
			}
		} elseif ( is_post_type_archive( 'agent' ) ) {
			$theme_files = [
				'archive-agent.php',
				'rgbc-real-estate/archive-agent.php',
			];
			$exist       = locate_template( $theme_files, false );
			if ( $exist != '' ) {
				return $exist;
			} else {
				return plugin_dir_path( __DIR__ ) . 'templates/archive-agent.php';
			}
		} elseif ( is_singular( 'real_estate_object' ) ) {
			$theme_files = [
				'single-real_estate_object.php',
				'rgbc-real-estate/single-real_estate_object.php',
			];
			$exist       = locate_template( $theme_files, false );
			if ( $exist != '' ) {
				return $exist;
			} else {
				return plugin_dir_path( __DIR__ ) . 'templates/single-real_estate_object.php';
			}
		} elseif ( is_singular( 'agent' ) ) {
			$theme_files = [
				'single-agent.php',
				'rgbc-real-estate/single-agent.php',
			];
			$exist       = locate_template( $theme_files, false );
			if ( $exist != '' ) {
				return $exist;
			} else {
				return plugin_dir_path( __DIR__ ) . 'templates/single-agent.php';
			}

		}

		return $template;
	}
}

$rgbc_real_estate_Template = new real_estate_object_Template_Loader();
$rgbc_real_estate_Template->register();