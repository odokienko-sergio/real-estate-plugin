<?php 

$real_estate_objects = get_post(array('post_type' =>['real_estate_object', 'agent'],'numberposts'=>-1));
foreach($real_estate_objects as $real_estate_object) {
    wp_delete_posts($real_estate_object->ID,true);
}