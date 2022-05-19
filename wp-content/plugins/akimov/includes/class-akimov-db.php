a<?php

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
class Akimov_DB {
    /*
    [0] => Отметка времени 
    [1] => id 
    [2] => Тип обьекта 
    [3] => Назначение 
    [4] => Этаж 
    [5] => Первичка вторичка 
    [6] => Отделка 
    [7] => статус 
    [8] => Балкон/лоджия 
    [9] => Тип дома 
    [10] => Тип строения 
    [11] => Материал стен 
    [12] => Площадь участка 
    [13] => Площадь участка 
    [14] => Назначение земель 
    [15] => Количество комнат 
    [16] => Этажей в доме 
    [17] => Общая площадь 
    [18] => Площадь кухни 
    [19] => Жилая площадь 
    [20] => Адрес 
    [21] => Геолокации 
    [22] => Описание 
    [23] => Цена 
    [24] => Загрузка фото 
    [25] => Главное фото 
    [26] => Ссылка youtube 
    [27] => Анент 
    */
}

$real_estate_obj_time = $arr[0];
$real_estate_obj_id = $arr[1];
$real_estate_obj_type = $arr[2];
$real_estate_obj_dest = $arr[3];
$real_estate_obj_floor = $arr[4];
$real_estate_obj_resale = $arr[5];
$real_estate_obj_finishing = $arr[6];
$real_estate_obj_status = $arr[7];
$real_estate_obj_balkon = $arr[8];
$real_estate_obj_house_type = $arr[9];
$real_estate_obj_building_type = $arr[10];
$real_estate_obj_material = $arr[11];
$real_estate_obj_area = $arr[12];
$real_estate_obj_land_assignment = $arr[13];
$real_estate_obj_room_count = $arr[14];
$real_estate_obj_floor_count = $arr[15];
$real_estate_obj_all_area = $arr[16];
$real_estate_obj_kitchen_area = $arr[17];
$real_estate_obj_live_area = $arr[18];
$real_estate_obj_address = $arr[19];
$real_estate_obj_lat = explode(', ', $arr[20])[0];
$real_estate_obj_long = explode(', ', $arr[20])[1];
$real_estate_obj_description = $arr[21];
$real_estate_obj_price = $arr[22];
$real_estate_obj_download_foto = $arr[23];
$real_estate_obj_general_foto = $arr[24];
$real_estate_obj_link_youtube = $arr[25];
$real_estate_obj_agent = $arr[26];
$real_estate_obj_kontakts = $arr[27];


'real_estate_obj_time' = $arr[0],
'real_estate_obj_id' = $arr[1],
'real_estate_obj_type' = $arr[2],
'real_estate_obj_dest' = $arr[3],
'real_estate_obj_floor' = $arr[4],
'real_estate_obj_resale' = $arr[5],
'real_estate_obj_finishing' = $arr[6],
'real_estate_obj_status' = $arr[7],
'real_estate_obj_balkon' = $arr[8],
'real_estate_obj_house_type' = $arr[9],
'real_estate_obj_building_type' = $arr[10],
'real_estate_obj_material' = $arr[11],
'real_estate_obj_area' = $arr[12],
'real_estate_obj_land_assignment' = $arr[13],
'real_estate_obj_room_count' = $arr[14],
'real_estate_obj_floor_count' = $arr[15],
'real_estate_obj_all_area' = $arr[16],
'real_estate_obj_kitchen_area' = $arr[17],
'real_estate_obj_live_area' = $arr[18],
'real_estate_obj_address' = $arr[19],
'real_estate_obj_lat' = explode(', ', $arr[20])[0],
'real_estate_obj_long' = explode(', ', $arr[20])[1],
'real_estate_obj_description' = $arr[21],
'real_estate_obj_price' = $arr[22],
'real_estate_obj_download_foto' = $arr[23],
'real_estate_obj_general_foto' = $arr[24],
'real_estate_obj_link_youtube' = $arr[25],
'real_estate_obj_agent' = $arr[26],
'real_estate_obj_kontakts' = $arr[27],