<?php

class ShMapperDrive_ajax
{
    public static $instance;
    public static function get_instance()
    {
        if (!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }
    public function __construct()
    {
        add_action('shm_ajax_submit', [__CLASS__, 'shm_ajax_submit']);
    }
    
    public static function shm_ajax_submit($params)
    {
        $action = sanitize_text_field($params[0]);
        switch ($action) {
            case "test2":
                require_once(SHM_REAL_PATH . "assets/google-sheets/google-sheets.php");
                //$id		= "1dQupQpiGjPqIbVHCTRvpybr-cmk5zs8U";
                
                $data = get_sheet($id);
                $html = "<table>";
                foreach ($data as $d) {
                    $html .= "<tr>";
                    foreach ($d as $dd) {
                        $html .= "<td>$dd</td>";
                    }
                    $html .= "</tr>";
                }
                $html .= "</table>";
                $d = [
                    $action,
                    [
                        "text"	=> $html,
                    ]
                ];
                break;
            case "shmd_google_preview":
                require_once(SHM_REAL_PATH . "class/ShMapperDriverPreview.class.php");
                $matrix = ShMapperDriverPreview::get_preview();
                $d = [
                    $action,
                    [
                        "matrix" => $matrix
                    ]
                ];
                break;
            case "google_matrix_data":
                $name 	= $params[1];
                $value 	= $params[2];
                $stroke	= $params[3];
                $google_matrix_data = ShMapperDrive::$options['google_matrix_data'];
                $google_matrix_data[$stroke]->$name = $value;
                ShMapperDrive::update_options();
                $d = [
                    $action,
                    [
                        "name"		=> $name,
                        "stroke"	=> $stroke,
                        "value" 	=> $value
                    ]
                ];
                break;
            case "shmd_google_update":
                require_once(SHM_REAL_PATH . "class/ShMapperDriverPreview.class.php");
                $matrix = ShMapperDriverPreview::update();
                $d = [
                    $action,
                    [
                        "msg"		=> __("Update successful!", SHMAPPER),
                        "matrix" => $matrix
                    ]
                ];
                break;
            case "shm_options":
                $name  = $params[1];
                $value = $params[2];
                ShMapperDrive::$options[$name] = $value;
                ShMapperDrive::update_options();
                $d = [
                    $action,
                    [
                        "name"	=> $name,
                        "value" => $value,
                        "data"	=> ShMapperDrive::$options
                    ]
                ];
                break;
            case "load_google_table":
                $id  = $params[1];
                ShMapperDrive::$options["google_table_id"] = $id;
                ShMapperDrive::update_options();
                require_once(SHM_REAL_PATH . "assets/google-sheets/google-sheets.php");
                $matrix = get_sheet($id);
                $data	=  static::get_matrix($matrix);
                if (!is_array($data)) {
                    $data = [];
                }
                if ($matrix && $matrix[0] && $matrix[0][0]) {
                    $d = [
                        $action,
                        [
                            "msg"		=> __("Success load Google Table.", SHMAPPER),
                            "matrix"	=> $matrix,
                            "data"		=> $data
                        ]
                    ];
                } else {
                    $d = [
                        $action,
                        [
                            "msg"		=> _("Error load Google Table.", SHMAPPER)
                        ]
                    ];
                }
        }
        $d_obj		= json_encode(apply_filters('shm_ajax_data', $d, $params));
        print $d_obj;
        wp_die();
    }
    public static function get_matrix($matrix)
    {
        if (
            is_array(ShMapperDrive::$options['google_matrix_data'])
            && count($matrix[0]) == count(ShMapperDrive::$options['google_matrix_data'])
        ) {
            $data = ShMapperDrive::$options['google_matrix_data'];
            $i = 0;
            foreach ($matrix[0] as $column) {
                if ($column != $data[$i]->title) {
                    $data[$i]->title = $column;
                }
                $i++;
            }
            return $data;
        } else {
            $data = [];
            $order = 0;
            foreach ($matrix[0] as $column) {
                $d = new StdClass;
                $d->include = 1;
                $d->order 	= $order;
                $d->title 	= $column;
                $d->meta 	= str2url($column);
                $data[] 	= $d;
                $order++;
            }
            ShMapperDrive::$options['google_matrix_data'] = $data;
            ShMapperDrive::update_options();
            return $data;
        }
    }
}

function rus2translit($string)
{
    $converter = array(
        '??' => 'a',   '??' => 'b',   '??' => 'v',
        '??' => 'g',   '??' => 'd',   '??' => 'e',
        '??' => 'e',   '??' => 'zh',  '??' => 'z',
        '??' => 'i',   '??' => 'y',   '??' => 'k',
        '??' => 'l',   '??' => 'm',   '??' => 'n',
        '??' => 'o',   '??' => 'p',   '??' => 'r',
        '??' => 's',   '??' => 't',   '??' => 'u',
        '??' => 'f',   '??' => 'h',   '??' => 'c',
        '??' => 'ch',  '??' => 'sh',  '??' => 'sch',
        '??' => '\'',  '??' => 'y',   '??' => '\'',
        '??' => 'e',   '??' => 'yu',  '??' => 'ya',
        
        '??' => 'A',   '??' => 'B',   '??' => 'V',
        '??' => 'G',   '??' => 'D',   '??' => 'E',
        '??' => 'E',   '??' => 'Zh',  '??' => 'Z',
        '??' => 'I',   '??' => 'Y',   '??' => 'K',
        '??' => 'L',   '??' => 'M',   '??' => 'N',
        '??' => 'O',   '??' => 'P',   '??' => 'R',
        '??' => 'S',   '??' => 'T',   '??' => 'U',
        '??' => 'F',   '??' => 'H',   '??' => 'C',
        '??' => 'Ch',  '??' => 'Sh',  '??' => 'Sch',
        '??' => '\'',  '??' => 'Y',   '??' => '\'',
        '??' => 'E',   '??' => 'Yu',  '??' => 'Ya',
    );
    return strtr($string, $converter);
}
function str2url($str)
{
    // ?????????????????? ?? ????????????????
    $str = rus2translit($str);
    // ?? ???????????? ??????????????
    $str = strtolower($str);
    // ?????????????? ?????? ???????????????? ?????? ???? ""
    $str = preg_replace('~[^-a-z0-9_]+~u', '_', $str);
    $str = preg_replace('/\s+/', '_', $str);
    // ?????????????? ?????????????????? ?? ???????????????? '_'
    $str = trim($str, "_");
    return $str;
}
