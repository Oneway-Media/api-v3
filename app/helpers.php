<?php

// Response JSON
function json($data = []) {		
    header('Content-Type:application/json;charset=utf-8');
    echo json_encode($data);
    exit;
}

// Sanitize returned data fields
function sanitize($data = null, $fields = []) {
    if($data === null || $fields === null) {
        return [];
    }        

    // Convert $data into array
    if( !is_array($data) ) {
        $data = [$data];
    }

    // Output data
    foreach( $data as $dat ) {
        foreach($fields as $new => $current) {
            $tmp[$new] = get_object_vars($dat)[$current];
        }            
        $output[] = $tmp;            
    }        
    return $output;
}

/*  Take an item in a collection
*   Return:
*   Array of found objects
*/
function find($collection = [], $field = null, $equal = null) {
    
    if($collection === [] || $field === null || $equal === null) {
        return [];
    }
    
    foreach($collection as $col) {
        if( get_object_vars($col)[$field] === $equal ) {
            $output[] = $col;
        }
    }
    
    return $output;
}


//Change Vietnamese no mark
function no_mark( $str ) {        
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($unicode as $nonUnicode => $uni){
            $str = preg_replace('/('.$uni.')/i', $nonUnicode, $str);
        }        
        $str = strtolower( $str );        
        $str = preg_replace('/[^a-z0-9. -]+/', '', $str);
        $str = str_replace(' ', '+', $str);
        return trim($str, '+');
}
    
?>