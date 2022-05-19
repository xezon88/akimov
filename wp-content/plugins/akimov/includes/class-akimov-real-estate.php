<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       @xezon
 * @since      1.0.0
 *
 * @package    Akimov
 * @subpackage Akimov/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Akimov
 * @subpackage Akimov/includes
 * @author     xezon <@xezon>
 */


class Akimov_RealEstate
{



    // private $real_estate = 'real_estate';







    public function __construct()
    {

        //add_action('init', array($this,'set_real_estate'));
        add_action('init', array($this, 'register_real_estate_content_type'));

        add_action('add_meta_boxes', array($this, 'create_post'));
        add_action('add_meta_boxes', array($this, 'add_real_estate_meta_boxes'));
        add_action('save_post_real_estate', array($this, 'save_real_estate'));
        add_action('save_post', array($this, 'gallery_meta_save'));
    }

    //triggered on activation of the plugin (called only once)
    public function plugin_activate()
    {
        //call our custom content type function
        $this->register_real_estate_content_type();
        //flush permalinks
        flush_rewrite_rules();
    }

    //trigered on deactivation of the plugin (called only once)
    public function plugin_deactivate()
    {
        //flush permalinks
        flush_rewrite_rules();
    }
    public function register_real_estate_content_type()
    {
        $taxonomies = array(

            'label'                 => 'Город', // определяется параметром $labels->name
            'labels'                => array(
                'name'              => 'Город',
                'singular_name'     => 'Город',
                'search_items'      => 'Искать город',
                'all_items'         => 'Все города',
                'parent_item'       => null,
                'parent_item_colon' => null,
                'edit_item'         => 'Ред. город',
                'update_item'       => 'Обновить город',
                'add_new_item'      => 'Добавить город',
                'new_item_name'     => 'Новый город',
                'menu_name'         => 'Города',
            ),

            'public'                => true,
            'show_in_nav_menus'     => true, // равен аргументу public
            'show_ui'               => true, // равен аргументу public
            'show_tagcloud'         => false, // равен аргументу show_ui
            'meta_box_cb'           => 'post_categories_meta_box',
            'hierarchical'          => false,
            'rewrite'               => array('slug' => 'real_estate_city', 'hierarchical' => false, 'with_front' => true, 'feed' => false),
            'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)


        );


        register_taxonomy('real_estate_city', array('real_estate'), $taxonomies);
        register_taxonomy_for_object_type('real_estate_city', 'real_estate');

        register_taxonomy(
            'real_estate_type', //taxonomy 
            'real_estate', //post-type
            array(
                'hierarchical'  => false,
                'label'         => 'Тип недвижимости', 'taxonomy general name',
                'singular_name' => 'Тип недвижимости', 'taxonomy general name',
                'rewrite'       => true,
                'query_var'     => true,
                'show_admin_column'     => true,
                'show_in_quick_edit'  => true,
                'meta_box_cb'  => 'post_categories_meta_box',
                'public'                => true,
                'show_in_nav_menus'     => true, // равен аргументу public
                'show_ui'               => true, // равен аргументу public
                'show_tagcloud'         => true, // равен аргументу show_ui
                'supports'          => array('title', 'thumbnail', 'editor', 'post-formats'),

            )
        );
        //Labels for post type
        $labels = array(
            'name'               => 'Объект недвижимости',
            'singular_name'      => 'Объект недвижимости',
            'menu_name'          => 'Объекты недвижимости',
            'name_admin_bar'     => 'Объект недвижимости',
            'add_new'            => 'Добавить новый',
            'add_new_item'       => 'Добавить новый объект',
            'new_item'           => 'Новый объект',
            'edit_item'          => 'Редактировать',
            'view_item'          => 'Просмотреть объект',
            'all_items'          => 'Все объекты',
            'search_items'       => 'Найти объект',
            'parent_item_colon'  => 'Parent Location:',
            'not_found'          => 'Объектов не найдено',
            'not_found_in_trash' => 'Нет объектов в корзине',
        );
        //arguments for post type
        $args = array(
            'labels'            => $labels,
            'public'            => true,
            'publicly_queryable' => true,
            'show_ui'           => true,
            'show_in_nav'       => true,
            'query_var'         => true,
            'hierarchical'      => false,
            'taxonomies'        => array('real_estate_city'),
            'supports'          => array('title', 'thumbnail', 'post-formats'),
            'has_archive'       => true,
            'menu_position'     => 20,
            'show_in_admin_bar' => true,
            'menu_icon'         => 'dashicons-location-alt',
            'rewrite'            => array('slug' => '%real_estate_city%/real_estate', 'with_front' => true)
        );
        //register post type
        register_post_type('real_estate', $args);

        // add_post_type_support( 'real_estate', array('page-attributes') );

        add_filter('post_type_link', 'real_estate_permalink', 1, 2);
        function real_estate_permalink($permalink, $post)
        {

            // выходим если это не наш тип записи: без холдера %faqcat%
            if (strpos($permalink, '%real_estate_city%') === false) {
                return $permalink;
            }

            // Получаем элементы таксы
            $terms = get_the_terms($post, 'city');
            // если есть элемент заменим холдер
            if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) {
                $term_slug = array_pop($terms)->slug;
            }
            // элемента нет, а должен быть...
            else {
                $term_slug = 'no-city';
            }

            return str_replace('%real_estate_city%', $term_slug, $permalink);
        }
    }

    public function add_real_estate_meta_boxes()
    {

        add_meta_box(
            'real_estate_meta_box', //id
            'Информация об объекте', //name
            array($this, 'location_meta_box_display'), //display function
            'real_estate', //post type
            'normal', //location
            'default' //priority
        );

        add_meta_box(
            'gallery-metabox',
            'Галерея',
            array($this, 'gallery_meta_callback'),
            'real_estate',
            'normal',
            'high'
        );

        add_meta_box(
            'map-metabox',
            'Карта',
            array($this, 'map_meta_callback'),
            'real_estate',
            'normal',
            'high'
        );
    }

    public function location_meta_box_display($post)
    {

        //set nonce field
        wp_nonce_field('real_estate_nonce', 'real_estate_nonce_field');

        //collect variables
        



        $real_estate_obj_time = get_post_meta($post->ID, 'real_estate_obj_time', true);
        $real_estate_obj_id = get_post_meta($post->ID, 'real_estate_obj_id', true);
        $real_estate_obj_type = get_post_meta($post->ID, 'real_estate_obj_type', true);
        $real_estate_obj_dest = get_post_meta($post->ID, 'real_estate_obj_dest', true);
        $real_estate_obj_floor = get_post_meta($post->ID, 'real_estate_obj_floor', true);
        $real_estate_obj_resale = get_post_meta($post->ID, 'real_estate_obj_resale', true);
        $real_estate_obj_finishing = get_post_meta($post->ID, 'real_estate_obj_finishing', true);
        $real_estate_obj_status = get_post_meta($post->ID, 'real_estate_obj_status', true);
        $real_estate_obj_balkon = get_post_meta($post->ID, 'real_estate_obj_balkon', true);
        $real_estate_obj_house_type = get_post_meta($post->ID, 'real_estate_obj_house_type', true);
        $real_estate_obj_building_type = get_post_meta($post->ID, 'real_estate_obj_building_type', true);
        $real_estate_obj_material = get_post_meta($post->ID, 'real_estate_obj_material', true);
        $real_estate_obj_area = get_post_meta($post->ID, 'real_estate_obj_area', true);
        $real_estate_obj_land_assignment = get_post_meta($post->ID, 'real_estate_obj_land_assignment', true);
        $real_estate_obj_room_count = get_post_meta($post->ID, 'real_estate_obj_room_count', true);
        $real_estate_obj_floor_count = get_post_meta($post->ID, 'real_estate_obj_floor_count', true);
        $real_estate_obj_all_area = get_post_meta($post->ID, 'real_estate_obj_all_area', true);
        $real_estate_obj_kitchen_area = get_post_meta($post->ID, 'real_estate_obj_kitchen_area', true);
        $real_estate_obj_live_area = get_post_meta($post->ID, 'real_estate_obj_live_area', true);
        $real_estate_obj_address = get_post_meta($post->ID, 'real_estate_obj_address', true);
        $real_estate_obj_lat = get_post_meta($post->ID, 'real_estate_obj_lat', true);
        $real_estate_obj_long = get_post_meta($post->ID, 'real_estate_obj_long', true);
        $real_estate_obj_description = get_post_meta($post->ID, 'real_estate_obj_description', true);
        $real_estate_obj_price = get_post_meta($post->ID, 'real_estate_obj_price', true);
        $real_estate_obj_download_foto = get_post_meta($post->ID, 'real_estate_obj_download_foto', true);
        $real_estate_obj_general_foto = get_post_meta($post->ID, 'real_estate_obj_general_foto', true);
        $real_estate_obj_link_youtube = get_post_meta($post->ID, 'real_estate_obj_link_youtube', true);
        $real_estate_obj_agent = get_post_meta($post->ID, 'real_estate_obj_agent', true);
        $real_estate_obj_kontakts = get_post_meta($post->ID, 'real_estate_obj_kontakts', true);

?>

        <div class="field-container">
            <?php
            //before main form elementst hook
            do_action('real_estate_admin_form_start');
            ?>
            <div class="field">
                <label for="real_estate_obj_time">Время создания</label>

                <input type="text" name="real_estate_obj_time" id="real_estate_obj_time" value="<?php echo $real_estate_obj_time; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_id">ID</label>

                <input type="text" name="real_estate_obj_id" id="real_estate_obj_id" value="<?php echo $real_estate_obj_id; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_type">Тип объекта</label>

                <input type="text" name="real_estate_obj_type" id="real_estate_obj_type" value="<?php echo $real_estate_obj_type; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_dest">Назначение объекта</label>

                <input type="text" name="real_estate_obj_dest" id="real_estate_obj_dest" value="<?php echo $real_estate_obj_dest; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_floor">Этаж</label>

                <input type="text" name="real_estate_obj_floor" id="real_estate_obj_floor" value="<?php echo $real_estate_obj_floor; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_resale">Тип продажи</label>

                <input type="text" name="real_estate_obj_resale" id="real_estate_obj_resale" value="<?php echo $real_estate_obj_resale; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_finishing">Отделка</label>

                <input type="text" name="real_estate_obj_finishing" id="real_estate_obj_finishing" value="<?php echo $real_estate_obj_finishing; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_status">Статус</label>

                <input type="text" name="real_estate_obj_status" id="real_estate_obj_status" value="<?php echo $real_estate_obj_status; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_balkon">Балкон / Лоджия</label>

                <input type="text" name="real_estate_obj_balkon" id="real_estate_obj_balkon" value="<?php echo $real_estate_obj_balkon; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_house_type">Тип дома</label>

                <input type="text" name="real_estate_obj_house_type" id="real_estate_obj_house_type" value="<?php echo $real_estate_obj_house_type; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_building_type">Тип строения</label>

                <input type="text" name="real_estate_obj_building_type" id="real_estate_obj_building_type" value="<?php echo $real_estate_obj_building_type; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_material">Материал стен</label>

                <input type="text" name="real_estate_obj_material" id="real_estate_obj_material" value="<?php echo $real_estate_obj_material; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_area">Площадь участка</label>

                <input type="text" name="real_estate_obj_area" id="real_estate_obj_area" value="<?php echo $real_estate_obj_area; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_land_assignment">Назначение земель</label>

                <input type="text" name="real_estate_obj_land_assignment" id="real_estate_obj_land_assignment" value="<?php echo $real_estate_obj_land_assignment; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_room_count">Количество комнат</label>

                <input type="text" name="real_estate_obj_room_count" id="real_estate_obj_room_count" value="<?php echo $real_estate_obj_room_count; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_floor_count">Количество этажей</label>

                <input type="text" name="real_estate_obj_floor_count" id="real_estate_obj_floor_count" value="<?php echo $real_estate_obj_floor_count; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_all_area">Общая площадь</label>

                <input type="text" name="real_estate_obj_all_area" id="" value="<?php echo $real_estate_obj_all_area; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_kitchen_area">Площадь кухни</label>

                <input type="text" name="real_estate_obj_kitchen_area" id="real_estate_obj_kitchen_area" value="<?php echo $real_estate_obj_kitchen_area; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_live_area">Жилая площадь</label>

                <input type="text" name="real_estate_obj_live_area" id="real_estate_obj_live_area" value="<?php echo $real_estate_obj_live_area; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_address">Адрес</label>

                <input type="text" name="real_estate_obj_address" id="real_estate_obj_address" value="<?php echo $real_estate_obj_address; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_lat">Широта</label>

                <input type="text" name="real_estate_obj_lat" id="real_estate_obj_lat" value="<?php echo $real_estate_obj_lat; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_long">Долгота</label>

                <input type="text" name="real_estate_obj_long" id="real_estate_obj_long" value="<?php echo $real_estate_obj_long; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_description">Описание</label>

                <input type="text" name="real_estate_obj_description" id="real_estate_obj_description" value="<?php echo $real_estate_obj_description; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_price">Цена</label>

                <input type="text" name="real_estate_obj_price" id="real_estate_obj_price" value="<?php echo $real_estate_obj_price; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_download_foto">Фото галереи</label>

                <input type="text" name="real_estate_obj_download_foto" id="real_estate_obj_download_foto" value="<?php echo $real_estate_obj_download_foto; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_general_foto">Главное фото</label>

                <input type="text" name="real_estate_obj_general_foto" id="real_estate_obj_general_foto" value="<?php echo $real_estate_obj_general_foto; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_link_youtube">Ссылка ютуб</label>

                <input type="text" name="real_estate_obj_link_youtube" id="real_estate_obj_link_youtube" value="<?php echo $real_estate_obj_link_youtube; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_agent">Агент</label>

                <input type="text" name="real_estate_obj_agent" id="real_estate_obj_agent" value="<?php echo $real_estate_obj_agent; ?>" />
            </div>
            <div class="field">
                <label for="real_estate_obj_kontakts">Контакты</label>

                <input type="text" name="real_estate_obj_kontakts" id="real_estate_obj_kontakts" value="<?php echo $real_estate_obj_kontakts; ?>" />
            </div>


            <?php
            //after main form elementst hook
            do_action('real_estate_admin_form_end');
            ?>
        </div>
    <?php

    }

    public function save_real_estate($post_id)
    {

        //check for nonce
        if (!isset($_POST['real_estate_nonce_field'])) {
            return $post_id;
        }
        //verify nonce
        if (!wp_verify_nonce($_POST['real_estate_nonce_field'], 'real_estate_nonce')) {
            return $post_id;
        }
        //check for autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        //get our phone, email and address fields


        $real_estate_obj_time = isset($_POST['real_estate_obj_time']) ? sanitize_text_field($_POST['real_estate_obj_time']) : '';
        $real_estate_obj_id = isset($_POST['real_estate_obj_id']) ? sanitize_text_field($_POST['real_estate_obj_id']) : '';
        $real_estate_obj_type = isset($_POST['real_estate_obj_type']) ? sanitize_text_field($_POST['real_estate_obj_type']) : '';
        $real_estate_obj_dest = isset($_POST['real_estate_obj_dest']) ? sanitize_text_field($_POST['real_estate_obj_dest']) : '';
        $real_estate_obj_floor = isset($_POST['real_estate_obj_floor']) ? sanitize_text_field($_POST['real_estate_obj_floor']) : '';
        $real_estate_obj_finishing = isset($_POST['real_estate_obj_finishing']) ? sanitize_text_field($_POST['real_estate_obj_finishing']) : '';
        $real_estate_obj_resale = isset($_POST['real_estate_obj_resale']) ? sanitize_text_field($_POST['real_estate_obj_resale']) : '';
        $real_estate_obj_status = isset($_POST['real_estate_obj_status']) ? sanitize_text_field($_POST['real_estate_obj_status']) : '';
        $real_estate_obj_balkon = isset($_POST['real_estate_obj_balkon']) ? sanitize_text_field($_POST['real_estate_obj_balkon']) : '';
        $real_estate_obj_house_type = isset($_POST['real_estate_obj_house_type']) ? sanitize_text_field($_POST['real_estate_obj_house_type']) : '';
        $real_estate_obj_building_type = isset($_POST['real_estate_obj_building_type']) ? sanitize_text_field($_POST['real_estate_obj_building_type']) : '';
        $real_estate_obj_material = isset($_POST['real_estate_obj_material']) ? sanitize_text_field($_POST['real_estate_obj_material']) : '';
        $real_estate_obj_area = isset($_POST['real_estate_obj_area']) ? sanitize_text_field($_POST['real_estate_obj_area']) : '';
        $real_estate_obj_land_assignment = isset($_POST['real_estate_obj_land_assignment']) ? sanitize_text_field($_POST['real_estate_obj_land_assignment']) : '';
        $real_estate_obj_room_count = isset($_POST['real_estate_obj_room_count']) ? sanitize_text_field($_POST['real_estate_obj_room_count']) : '';
        $real_estate_obj_floor_count = isset($_POST['real_estate_obj_floor_count']) ? sanitize_text_field($_POST['real_estate_obj_floor_count']) : '';
        $real_estate_obj_all_area = isset($_POST['real_estate_obj_all_area']) ? sanitize_text_field($_POST['real_estate_obj_all_area']) : '';
        $real_estate_obj_kitchen_area = isset($_POST['real_estate_obj_kitchen_area']) ? sanitize_text_field($_POST['real_estate_obj_kitchen_area']) : '';
        $real_estate_obj_live_area = isset($_POST['real_estate_obj_live_area']) ? sanitize_text_field($_POST['real_estate_obj_live_area']) : '';
        $real_estate_obj_address = isset($_POST['real_estate_obj_address']) ? sanitize_text_field($_POST['real_estate_obj_address']) : '';
        $real_estate_obj_lat = isset($_POST['real_estate_obj_lat']) ? sanitize_text_field($_POST['real_estate_obj_lat']) : '';
        $real_estate_obj_long = isset($_POST['real_estate_obj_long']) ? sanitize_text_field($_POST['real_estate_obj_long']) : '';
        $real_estate_obj_description = isset($_POST['real_estate_obj_description']) ? sanitize_text_field($_POST['real_estate_obj_description']) : '';
        $real_estate_obj_price = isset($_POST['real_estate_obj_price']) ? sanitize_text_field($_POST['real_estate_obj_price']) : '';
        $real_estate_obj_download_foto = isset($_POST['real_estate_obj_download_foto']) ? sanitize_text_field($_POST['real_estate_obj_download_foto']) : '';
        $real_estate_obj_general_foto = isset($_POST['real_estate_obj_general_foto']) ? sanitize_text_field($_POST['real_estate_obj_general_foto']) : '';
        $real_estate_obj_link_youtube = isset($_POST['real_estate_obj_link_youtube']) ? sanitize_text_field($_POST['real_estate_obj_link_youtube']) : '';
        $real_estate_obj_agent = isset($_POST['real_estate_obj_agent']) ? sanitize_text_field($_POST['real_estate_obj_agent']) : '';
        $real_estate_obj_kontakts = isset($_POST['real_estate_obj_kontakts']) ? sanitize_text_field($_POST['real_estate_obj_kontakts']) : '';



        //update phone, memil and address field

        update_post_meta($post_id, 'real_estate_obj_time', $real_estate_obj_time);
        update_post_meta($post_id, 'real_estate_obj_id', $real_estate_obj_id);
        update_post_meta($post_id, 'real_estate_obj_type', $real_estate_obj_type);
        update_post_meta($post_id, 'real_estate_obj_dest', $real_estate_obj_dest);
        update_post_meta($post_id, 'real_estate_obj_floor', $real_estate_obj_floor);
        update_post_meta($post_id, 'real_estate_obj_resale', $real_estate_obj_resale);
        update_post_meta($post_id, 'real_estate_obj_finishing', $real_estate_obj_finishing);
        update_post_meta($post_id, 'real_estate_obj_status', $real_estate_obj_status);
        update_post_meta($post_id, 'real_estate_obj_balkon', $real_estate_obj_balkon);
        update_post_meta($post_id, 'real_estate_obj_house_type', $real_estate_obj_house_type);
        update_post_meta($post_id, 'real_estate_obj_building_type', $real_estate_obj_building_type);
        update_post_meta($post_id, 'real_estate_obj_material', $real_estate_obj_material);
        update_post_meta($post_id, 'real_estate_obj_area', $real_estate_obj_area);
        update_post_meta($post_id, 'real_estate_obj_land_assignment', $real_estate_obj_land_assignment);
        update_post_meta($post_id, 'real_estate_obj_room_count', $real_estate_obj_room_count);
        update_post_meta($post_id, 'real_estate_obj_floor_count', $real_estate_obj_floor_count);
        update_post_meta($post_id, 'real_estate_obj_all_area', $real_estate_obj_all_area);
        update_post_meta($post_id, 'real_estate_obj_kitchen_area', $real_estate_obj_kitchen_area);
        update_post_meta($post_id, 'real_estate_obj_live_area', $real_estate_obj_live_area);
        update_post_meta($post_id, 'real_estate_obj_address', $real_estate_obj_address);
        update_post_meta($post_id, 'real_estate_obj_lat', $real_estate_obj_lat);
        update_post_meta($post_id, 'real_estate_obj_long', $real_estate_obj_long);
        update_post_meta($post_id, 'real_estate_obj_description', $real_estate_obj_description);
        update_post_meta($post_id, 'real_estate_obj_price', $real_estate_obj_price);
        update_post_meta($post_id, 'real_estate_obj_download_foto', $real_estate_obj_download_foto);
        update_post_meta($post_id, 'real_estate_obj_general_foto', $real_estate_obj_general_foto);
        update_post_meta($post_id, 'real_estate_obj_link_youtube', $real_estate_obj_link_youtube);
        update_post_meta($post_id, 'real_estate_obj_agent', $real_estate_obj_agent);
        update_post_meta($post_id, 'real_estate_obj_kontakts', $real_estate_obj_kontakts);


        //location save hook 
        //used so you can hook here and save additional post fields added via 'wp_location_meta_data_output_end' or 'wp_location_meta_data_output_end'


    }

    public function gallery_meta_callback($post)
    {
        wp_nonce_field(basename(__FILE__), 'gallery_meta_nonce');
        $ids = get_post_meta($post->ID, 'vdw_gallery_id', true);

    ?>
        <table class="form-table">
            <tr>
                <td>
                    <a class="gallery-add button" href="#" data-uploader-title="Add image(s) to gallery" data-uploader-button-text="Add image(s)">Add image(s)</a>

                    <ul id="gallery-metabox-list">
                        <?php if ($ids) : foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value); ?>

                                <li>
                                    <input type="hidden" name="vdw_gallery_id[<?php echo $key; ?>]" value="<?php echo $value; ?>">
                                    <img class="image-preview" src="<?php echo $image[0]; ?>">
                                    <a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br>
                                    <small><a class="remove-image" href="#">Remove image</a></small>
                                </li>

                        <?php endforeach;
                        endif; ?>
                    </ul>

                </td>
            </tr>
        </table>
    <?php }

    public function gallery_meta_save($post_id)
    {
        if (!isset($_POST['gallery_meta_nonce']) || !wp_verify_nonce($_POST['gallery_meta_nonce'], basename(__FILE__))) return;

        if (!current_user_can('edit_post', $post_id)) return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (isset($_POST['vdw_gallery_id'])) {
            update_post_meta($post_id, 'vdw_gallery_id', $_POST['vdw_gallery_id']);
        } else {
            delete_post_meta($post_id, 'vdw_gallery_id');
        }
    }


    public function create_post()
    {
        $id = '1Q08p_WncgTX4Ep8ik1Lga59PsD3uOt4-XYf7eGwxvhM';
        $gid = '539937704';


        $csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);
        // var_dump($csv);
        $csv = explode("\r\n", $csv);
        //var_dump($csv);
        $array = array_map('str_getcsv', $csv);
        $array2 = array_shift($array);

        foreach ($array as $arr) {

            $real_estate_obj_time = $arr[0];
            // echo $real_estate_obj_time . '</br>';
            $real_estate_obj_id = $arr[1];
            // echo $real_estate_obj_id . '</br>';
            $real_estate_obj_type = $arr[2];
            // echo $real_estate_obj_type . '</br>';
            $real_estate_obj_dest = $arr[3];
            // echo $real_estate_obj_dest . '</br>';
            $real_estate_obj_floor = $arr[4];
            // echo $real_estate_obj_floor . '</br>';
            $real_estate_obj_resale = $arr[5];
            // echo $real_estate_obj_resale . '</br>';
            $real_estate_obj_finishing = $arr[6];
            // echo $real_estate_obj_finishing . '</br>';
            $real_estate_obj_status = $arr[7];
            // echo $real_estate_obj_status . '</br>';
            $real_estate_obj_balkon = $arr[8];
            // echo $real_estate_obj_balkon . '</br>';
            $real_estate_obj_house_type = $arr[9];
            // echo $real_estate_obj_house_type . '</br>';
            $real_estate_obj_building_type = $arr[10];
            // echo $real_estate_obj_building_type . '</br>';
            $real_estate_obj_material = $arr[11];
            // echo $real_estate_obj_material . '</br>';
            $real_estate_obj_area = $arr[12];
            // echo $real_estate_obj_area . '</br>';
            $real_estate_obj_land_assignment = $arr[13];
            // echo $real_estate_obj_land_assignment . '</br>';
            $real_estate_obj_room_count = $arr[14];
            // echo $real_estate_obj_room_count . '</br>';
            $real_estate_obj_floor_count = $arr[15];
            // echo $real_estate_obj_floor_count . '</br>';
            $real_estate_obj_all_area = $arr[16];
            // echo $real_estate_obj_all_area . '</br>';
            $real_estate_obj_kitchen_area = $arr[17];
            // echo $real_estate_obj_kitchen_area . '</br>';
            $real_estate_obj_live_area = $arr[18];
            // echo $real_estate_obj_live_area . '</br>';
            $real_estate_obj_address = $arr[19];
            // echo $real_estate_obj_address . '</br>';
            $real_estate_obj_lat = explode(', ', $arr[20])[0];
            // echo $real_estate_obj_lat . '</br>';
            $real_estate_obj_long = explode(', ', $arr[20])[1];
            // echo $real_estate_obj_long . '</br>';
            $real_estate_obj_description = $arr[21];
            // echo $real_estate_obj_description . '</br>';
            $real_estate_obj_price = $arr[22];
            // echo $real_estate_obj_price . '</br>';
            $real_estate_obj_download_foto = $arr[23];
            // echo $real_estate_obj_download_foto . '</br>';
            $real_estate_obj_general_foto = $arr[24];
            // echo $real_estate_obj_general_foto . '</br>';
            $real_estate_obj_link_youtube = $arr[25];
            // echo $real_estate_obj_link_youtube . '</br>';
            $real_estate_obj_agent = $arr[26];
            // echo $real_estate_obj_agent . '</br>';
            $real_estate_obj_kontakts = $arr[27];
            // echo $real_estate_obj_kontakts . '</br>';

            $post_data = array(

                
                'post_status'    =>  'publish',         // Статус создаваемой записи.
                'post_title'     => $real_estate_obj_address,                                                   // Заголовок (название) записи.
                'post_type'      =>  'real_estate', // Тип записи.
                
                'meta_input'     => [
                    'real_estate_obj_time' => $arr[0],
                    'real_estate_obj_id' => $arr[1],
                    'real_estate_obj_type' => $arr[2],
                    'real_estate_obj_dest' => $arr[3],
                    'real_estate_obj_floor' => $arr[4],
                    'real_estate_obj_resale' => $arr[5],
                    'real_estate_obj_finishing' => $arr[6],
                    'real_estate_obj_status' => $arr[7],
                    'real_estate_obj_balkon' => $arr[8],
                    'real_estate_obj_house_type' => $arr[9],
                    'real_estate_obj_building_type' => $arr[10],
                    'real_estate_obj_material' => $arr[11],
                    'real_estate_obj_area' => $arr[12],
                    'real_estate_obj_land_assignment' => $arr[13],
                    'real_estate_obj_room_count' => $arr[14],
                    'real_estate_obj_floor_count' => $arr[15],
                    'real_estate_obj_all_area' => $arr[16],
                    'real_estate_obj_kitchen_area' => $arr[17],
                    'real_estate_obj_live_area' => $arr[18],
                    'real_estate_obj_address' => $arr[19],
                    'real_estate_obj_lat' => explode(', ', $arr[20])[0],
                    'real_estate_obj_long' => explode(', ', $arr[20])[1],
                    'real_estate_obj_description' => $arr[21],
                    'real_estate_obj_price' => $arr[22],
                    'real_estate_obj_download_foto' => $arr[23],
                    'real_estate_obj_general_foto' => $arr[24],
                    'real_estate_obj_link_youtube' => $arr[25],
                    'real_estate_obj_agent' => $arr[26],
                    'real_estate_obj_kontakts' => $arr[27]
                ],
            );
            if (!post_exists($arr[19])) {
                wp_insert_post($post_data, true);
            }
        }
    }

    public function get_real_estates_output($arguments = "")
    {

        //default args
        $default_args = array(
            'real_estate_id'   => '',
            'number_of_real_estates'   => -1
        );

        //update default args if we passed in new args
        if (!empty($arguments) && is_array($arguments)) {
            //go through each supplied argument
            foreach ($arguments as $arg_key => $arg_val) {
                //if this argument exists in our default argument, update its value
                if (array_key_exists($arg_key, $default_args)) {
                    $default_args[$arg_key] = $arg_val;
                }
            }
        }

        //find locations
        $real_estate_args = array(
            'post_type'     => 'real_estate',
            'posts_per_page' => $default_args['number_of_real_estates'],
            'post_status'   => 'publish'
        );
        //if we passed in a single location to display
        if (!empty($default_args['location_id'])) {
            $real_estate_args['include'] = $default_args['real_estate_id'];
        }

        //output
        $html = '';
        $locations = get_posts($real_estate_args);
        //if we have locations 
        if ($locations) {
            $html .= '<article class="location_list cf">';
            //foreach location
            foreach ($locations as $location) {
                $html .= '<section class="location">';
                //collect location data
                $real_estate_id = $location->ID;
                $real_estate_title = get_the_title($real_estate_id);
                $real_estate_thumbnail = get_the_post_thumbnail($real_estate_id, 'thumbnail');
                $real_estate_content = apply_filters('the_content', $location->post_content);
                if (!empty($real_estate_content)) {
                    $real_estate_content = strip_shortcodes(wp_trim_words($real_estate_content, 40, '...'));
                }
                $real_estate_permalink = get_permalink($real_estate_id);
                $real_estate_phone = get_post_meta($real_estate_id, 'real_estate_obj_address', true);
                $real_estate_email = get_post_meta($real_estate_id, 'real_estate_obj_lat', true);

                //apply the filter before our main content starts 
                //(lets third parties hook into the HTML output to output data)
                $html = apply_filters('real_estate_before_main_content', $html);

                //title
                $html .= '<h2 class="title">';
                $html .= '<a href="' . $real_estate_permalink . '" title="view location">';
                $html .= $real_estate_title;
                $html .= '</a>';
                $html .= '</h2>';



                //image & content
                if (!empty($real_estate_thumbnail) || !empty($real_estate_content)) {

                    $html .= '<p class="image_content">';
                    if (!empty($real_estate_thumbnail)) {
                        $html .= $real_estate_thumbnail;
                    }
                    if (!empty($real_estate_content)) {
                        $html .=  $real_estate_content;
                    }

                    $html .= '</p>';
                }

                //phone & email output
                if (!empty($real_estate_phone) || !empty($real_estate_email)) {
                    $html .= '<p class="phone_email">';
                    if (!empty($real_estate_phone)) {
                        $html .= '<b>Phone: </b>' . $real_estate_phone . '</br>';
                    }
                    if (!empty($real_estate_email)) {
                        $html .= '<b>Email: </b>' . $real_estate_email;
                    }
                    $html .= '</p>';
                }

                //apply the filter after the main content, before it ends 
                //(lets third parties hook into the HTML output to output data)
                $html = apply_filters('real_estate_after_main_content', $html);

                //readmore
                $html .= '<a class="link" href="' . $real_estate_permalink . '" title="view location">View Location</a>';
                $html .= '</section>';
            }
            $html .= '</article>';
            $html .= '<div class="cf"></div>';
        }
        $html .= '<div id="map"></div>';
        $html .= '<div id="floating-panel">';

        $html .= '<input id="place-id" type="text" value="ChIJd8BlQ2BZwokRAFUEcm_qrcA" />';
        $html .= '<input id="submit" type="button" value="Get Address for Place ID" />';
        $html .= '</div>';
        $test = get_option('akimov_api_key');

    ?>
        <script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo $test; ?>&callback=initMap">
        </script>

        <script>
            // Initialize the map.
            var data = [{
                    name: 'Санкт-Петербург',
                    description: 'Наб. реки Смоленки, 5-7',
                    lat: 59.948940,
                    lon: 30.261825,
                },
                {
                    name: 'Москва',
                    description: 'Студенческая ул., 15',
                    lat: 55.742730,
                    lon: 37.551128,
                },
                {
                    name: 'Самара',
                    description: 'ул. Скляренко, 26',
                    lat: 53.216539,
                    lon: 50.160825,
                },
                {
                    name: 'Омск',
                    description: 'ул. Лермонтова, 63',
                    lat: 54.982917,
                    lon: 73.396128,
                },
            ];

            function initMap() {
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 10,
                    center: {
                        lat: 55.74,
                        lng: 37.55
                    },

                });
                for (var i = 0; i < data.length; i++) {
                    var item = data[i];
                    var marker = new google.maps.Marker({
                        position: {
                            lat: item.lat,
                            lng: item.lon
                        },
                        map: map,
                       // icon: getMarkerIcon(),
                        id: i
                    });
                    marker.addListener('click', function() {
                        var marker = this;
                        // center map to marker
                        var objPoint = new google.maps.LatLng(
                            marker.position.lat(),
                            marker.position.lng()
                        );
                        map.setZoom(11);
                        map.setCenter(objPoint);
                        var item = data[marker.id];
                        var contentString = '<div class="map__window" style="color: #000"><div class="map__window-title">' + item.name + '</div><p class="map__window-description">' + item.description + '</p></div>';
                        infoWindow = new google.maps.InfoWindow({
                            content: contentString
                        });
                        infoWindow.open(map, marker);
                    });
                }

                const styles = {
                    default: [],
                    hide: [{
                            "featureType": "administrative",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative",
                            "elementType": "geometry",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "administrative.country",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "elementType": "geometry",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.locality",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.locality",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.locality",
                            "elementType": "geometry.stroke",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.locality",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.locality",
                            "elementType": "labels.icon",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.locality",
                            "elementType": "labels.text",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.neighborhood",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.province",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.province",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "administrative.province",
                            "elementType": "labels.icon",
                            "stylers": [{
                                    "color": "#ffeb3b"
                                },
                                {
                                    "visibility": "on"
                                },
                                {
                                    "weight": 1
                                }
                            ]
                        },
                        {
                            "featureType": "administrative.province",
                            "elementType": "labels.text",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "landscape",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "poi",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "labels.text",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "road",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "geometry",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "geometry.stroke",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.icon",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.text",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.text.stroke",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.arterial",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.highway",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "labels.text.stroke",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.highway.controlled_access",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.highway.controlled_access",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.local",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road.local",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "transit",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        }
                    ],
                };
                map.setOptions({
                    styles: styles["hide"]
                });

            }

            // This function is called when the user clicks the UI button requesting
            // a geocode of a place ID.


            window.initMap = initMap;
        </script>
<?php


        return $html;
    }
}
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-akimov-real-estate-shortcode.php';
