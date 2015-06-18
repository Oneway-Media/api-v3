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



?>