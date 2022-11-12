<?php /* створюемо новий клас, рєеструємо 3 хуки: ініціалізуємо кастом пост тайп, додємо метабокси, зберігання метабоксів в бд */
if ( ! class_exists( 'Real_Estate_Object_Cpt' ) ) {
	class Real_Estate_Object_Cpt {
		public function register() {
			add_action( 'init', [
				$this,
				'custom_post_type',
			] );
			add_action( 'add_meta_boxes', [
				$this,
				'add_meta_box_real_estate',
			] );
			add_action(
				'save_post',
				[
					$this,
					'save_metabox',
				],
				10,/* преорітет 1-10 (1 - найперший пріорітет)*/
				2 /* кількість параметрів які приходять из єкшона у функцію */
			);
			add_action( 'manage_real_estate_object_posts_columns', [
				$this,
				'custom_columns_for_real_estate_object',
			] );
			add_action( 'manage_real_estate_object_posts_custom_column', [
				$this,
				'custom_real_estate_object_columns_data',
			],
				10,
				2
			);
			add_filter( 'manage_edit-real_estate_object_sortable_columns', [
				$this,
				'custom_real_estate_object_columns_sort',
			] );
			add_filter( 'pre_get_posts', [
				$this,
				'custom_real_estate_object_order',
			] );
		}

		/* створюємо функцію додавання метабоксів */
		public function add_meta_box_real_estate() {
			add_meta_box(
				'real_estate_settings',
				esc_html__( 'Real Estate Settings', 'real_estate_object' ),
				[
					$this,
					'metabox_real_estate_html',
				],
				'real_estate_object',
				'normal',
				'default'
			);
		}

		public function metabox_real_estate_html( $post ) {
			$price      = get_post_meta( $post->ID, 'real_estate_price', true );
			$period     = get_post_meta( $post->ID, 'real_estate_period', true );
			$type       = get_post_meta( $post->ID, 'real_estate_type', true );
			$agent_meta = get_post_meta( $post->ID, 'real_estate_object_agent', true );


			wp_nonce_field( 'real_estate_fields', 'real_estate_project' );

			$text = '<em>Price</em>';/* значення переменної(змінної) */
			wp_kses( $text, array( 'em' => array() ) );/* дозволяю тльки теги em, провести через санітізацію,
            ще є wp_kses_post() - не виведе неіснуючий тег, та wp_kses_data() - дозволяє увесь хтмл який в коментарях  */


			/* esc_html() - треба застосовувати при виводі якихось данних та брабрати html, замість хтмл виводить як символи*/
			/*.esc_attr() - використовуємо коли працюємо з хтмл тегами,
			esc_url()- використовуємо для урлів,
			esc_js() - для js*/
			/* вивод полей Price, Period, Type(це select) та агентів */

			//esc_html__  використовуємо коли нам треба покласти у переменную якесь значення
			//esc_html_e - echo використовувати не потрібно, відразу виводится на екран
			echo '
            <p>
                <label for="real_estate_price">' . esc_html( 'Price', 'real_estate_object' ) . '</label>     
                <input type="number" id="real_estate_price" name="real_estate_price" value="' . esc_attr( $price ) . '">      
            </p>

            <p>
                <label for="real_estate_period">' . esc_html__( 'Period', 'real_estate_object' ) . '</label>     
                <input type="text" id="real_estate_period" name="real_estate_period" value="' . esc_html( $period ) . '">      
            </p>

            <p>
                <label for="real_estate_type">' . esc_html__( 'Type', 'real_estate_object' ) . '</label>     
                <select id="real_estate_type" name="real_estate_type">
                    <option value="">Select Type</option>
                    <option value="sale" ' . selected( 'sale', $type, false ) . '>' . esc_html__( 'For Sale', 'real_estate_object' ) . '</option>
                    <option value="rent" ' . selected( 'rent', $type, false ) . '>' . esc_html__( 'For Rent', 'real_estate_object' ) . '</option>
                    <option value="sold" ' . selected( 'sold', $type, false ) . '>' . esc_html__( 'Sold', 'real_estate_object' ) . '</option>
                </select>      
            </p>
            ';

			$agents = get_posts( array(
				'post_type'   => 'agent',
				'numberposts' => - 1,
			) );/*new WP_Query(array('post_type'=>'agent','posts_per_page'=>-1));*/
			//print_r($agents);

			if ( $agents ) {
				echo '
                <p>
                <label for="real_estate_object_agent">' . esc_html( 'Agents', 'real_estate_object' ) . '</label>     
                <select id="real_estate_object_agent" name="real_estate_object_agent">
                    <option value="">' . esc_html( 'Select Agent', 'real_estate_object' ) . '</option>';

				foreach ( $agents as $agent ) {
					?>
					<option
						value="<?php echo esc_html( $agent->ID ); ?>"
						<?php selected( $agent->ID, $agent_meta ); ?>
					>
						<?php echo esc_html( $agent->post_title ); ?>
					</option>
					<?php
				}

				echo '</select>
            </p>';
			}
		}

		/* функція зберігання метабоксів */
		public function save_metabox( $post_id, $post ) {
			/* перевірки */
			if ( ! isset( $_POST['real_estate_project'] ) || ! wp_verify_nonce( $_POST['real_estate_project'], 'real_estate_fields' ) ) {
				return $post_id;
			}/* поле для перевірки чи існує (isset) чи дійсне значення у ньому (verify_nonce)*/

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			if ( $post->post_type !== 'real_estate_object' ) {
				return $post_id;
			}

			$post_type = get_post_type_object( $post->post_type );
			if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
				return $post_id;
			}
			/* перевірки на заповненість 4 полей, price, period, type та agent */
			if ( is_null( $_POST['real_estate_price'] ) ) {
				delete_post_meta( $post_id, 'real_estate_price' );
			} else {
				update_post_meta( $post_id, 'real_estate_price', sanitize_text_field( intval( $_POST['real_estate_price'] ) ) );
			}

			if ( is_null( $_POST['real_estate_period'] ) ) {
				delete_post_meta( $post_id, 'real_estate_period' );
			} else {
				update_post_meta( $post_id, 'real_estate_period', sanitize_text_field( $_POST['real_estate_period'] ) );
			}

			if ( is_null( $_POST['real_estate_type'] ) ) {
				delete_post_meta( $post_id, 'real_estate_type' );
			} else {
				update_post_meta( $post_id, 'real_estate_type', sanitize_text_field( $_POST['real_estate_type'] ) );
			}

			if ( is_null( $_POST['real_estate_object_agent'] ) ) {
				delete_post_meta( $post_id, 'real_estate_object_agent' );
			} else {
				update_post_meta( $post_id, 'real_estate_object_agent', sanitize_text_field( $_POST['real_estate_object_agent'] ) );
			}

			// ще є sanitize_textarea_field, sanitize_email_field - для відчищеня данних
			return $post_id;
		}

		/* рєестрація кастомного пост тайпів(двох, Real Estate та Agent) */
		public function custom_post_type() {
			register_post_type( 'real_estate_object',
				array(
					'public'      => true,
					'has_archive' => true,
					'rewrite'     => [ 'slug' => 'real_estate_objects' ],
					'label'       => esc_html__( 'Real Estate Object', 'real_estate_object' ),
					/* esc_html__ це і ми робимо транслейшн і прибераємо хтмл */
					'supports'    => array(
						'title',
						'editor',
						'thumbnail',
						'excerpt'
					),
				) );
			register_post_type( 'agent',
				array(
					'public'       => true,
					'has_archive'  => true,
					'rewrite'      => [ 'slug' => 'agents' ],
					'label'        => esc_html__( 'Agents', 'real_estate_object' ),
					'supports'     => array(
						'title',
						'editor',
						'thumbnail',
					),
					'show_in_rest' => true,
				) );

			$labels = [
				'name'              => esc_html_x( 'Location', 'taxonomy general name', 'real_estate_object' ),
				'singular_name'     => esc_html_x( 'Location', 'taxonomy singular name', 'real_estate_object' ),
				'search_items'      => esc_html__( 'Search Locations', 'real_estate_object' ),
				'all_items'         => esc_html__( 'All Locations', 'real_estate_object' ),
				'parent_item'       => esc_html__( 'Parent Location', 'real_estate_object' ),
				'parent_item_colon' => esc_html__( 'Parent Location:', 'real_estate_object' ),
				'edit_item'         => esc_html__( 'Edit Location', 'real_estate_object' ),
				'update_item'       => esc_html__( 'Update Location', 'real_estate_object' ),
				'add_new_item'      => esc_html__( 'Add New Location', 'real_estate_object' ),
				'new_item_name'     => esc_html__( 'New Location Name', 'real_estate_object' ),
				'menu_name'         => esc_html__( 'Location', 'real_estate_object' ),
			];
			$args   = array(
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => [ 'slug' => 'real_estate_object/location' ],
				'labels'            => $labels,
			);

			register_taxonomy( 'location', 'real_estate_object', $args );

			unset( $args );
			unset( $labels );

			$labels = array(
				'name'              => esc_html_x( 'Types', 'taxonomy general name', 'real_estate_object' ),
				'singular_name'     => esc_html_x( 'Type', 'taxonomy singular name', 'real_estate_object' ),
				'search_items'      => esc_html__( 'Search Types', 'real_estate_object' ),
				'all_items'         => esc_html__( 'All Types', 'real_estate_object' ),
				'parent_item'       => esc_html__( 'Parent Type', 'real_estate_object' ),
				'parent_item_colon' => esc_html__( 'Parent Type:', 'real_estate_object' ),
				'edit_item'         => esc_html__( 'Edit Type', 'real_estate_object' ),
				'update_item'       => esc_html__( 'Update Type', 'real_estate_object' ),
				'add_new_item'      => esc_html__( 'Add New Type', 'real_estate_object' ),
				'new_item_name'     => esc_html__( 'New Type Name', 'real_estate_object' ),
				'menu_name'         => esc_html__( 'Type', 'real_estate_object' ),
			);

			$args = array(
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'real_estate_object/type' ),
				'labels'            => $labels,
			);

			register_taxonomy( 'real_estate_object-type', 'real_estate_object', $args );
		}

		public function custom_columns_for_real_estate_object( $columns ) {
			/*тут ми отримуємо значення які у нас були до*/
			$title    = $columns['title'];
			$date     = $columns['date'];
			$location = $columns['taxonomy-location']; /*беремо ці id з колонок в безпосередньо Real Estate Object */
			$type     = $columns['taxonomy-real_estate_object-type'];

			/*тут робимо значення після і додаємо колонки*/
			$columns['title']                            = $title;
			$columns['date']                             = $date;
			$columns['taxonomy-location']                = $location;
			$columns['taxonomy-real_estate_object-type'] = $type;
			$columns['price']                            = esc_html__( 'Price', 'real_estate_object' );
			$columns['type']                             = esc_html__( 'Type Offer', 'real_estate_object' );


			return $columns;
		} /* після цього можно подивитись результат. ми побачимо додаткові 3 колонки (поки що пусті) у нашему пост тайпі real_estate_object*/

		public function custom_real_estate_object_columns_data( $column, $post_id ) {

			$price = get_post_meta( $post_id, 'real_estate_price', true );
			$type  = get_post_meta( $post_id, 'real_estate_type', true );

			switch ( $column ) {
				case 'price' :
					echo esc_html( $price );
					break;
				case 'type' :
					echo $type;
					break;
			}
		}

		public function custom_real_estate_object_columns_sort( $columns ) {

			$columns['price'] = 'price';

			//$columns['type'] = 'type';

			return $columns;
		}

		public function custom_real_estate_object_order( $query ) {

			if ( ! is_admin() ) {
				return;
			}
			$orderby = $query->get( 'orderby' );

			if ( 'price' === $orderby ) {
				$query->set( 'meta_key', 'real_estate_price' );
				$query->set( 'orderby', 'meta_value_num' );
			}
		}


	}


}/* перевірка */
if ( class_exists( 'Real_Estate_Object_Cpt' ) ) {
	$real_estate_object_cpt = new Real_Estate_Object_Cpt();
	$real_estate_object_cpt->register();
}