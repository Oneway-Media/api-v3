<?php

// Wordpress
require_once('../../../wp-load.php');

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


$action = isset($_GET['action']) ? $_GET['action']: 'xxx';
$from = isset($_GET['page']) ? intval($_GET['page']): 0;

$limit = 100;



switch($action) {
    
    // ALL
    case 'all':
    
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'src' => '',
            'duration' => 0
        ];
    
        $offset = $from * $limit;
        
        $raw = new WP_Query([
            'post_status' => ['publish', 'future'],
            'post_type' => 'audio',
            'offset' => $offset,
            'posts_per_page' => $limit,
            'orderby' => 'ID'
        ]);
    
            
        $pre = sanitize($raw->posts, $fields);
        
        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['src'] = get_post_meta( $p['id'], 'oneway_audiolink', true );
                
                // Audio Duration
                $duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
                $p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
                
                $output[] = $p;
            }

            json($output);
        } else {
            json([]);
        }
    
    break;
    
    
    
    
    // CHECK
    case 'check':
    
        $fields = [
            'id' => 'ID',
            'title' => 'post_title'
        ];
    
    
        $raw = new WP_Query([
            'post_status' => ['publish', 'future'],
            'post_type' => 'audio',
            'posts_per_page' => $limit,
            'meta_query' => [
                'relation' => 'OR',
                [
                'key' => 'oneway_audioduration',
                'value' => '0',
                'compare' => 'NOT EXISTS'
                ],
                [                    
                    'key' => 'oneway_audioduration',
                    'value' => '0',
                    'compare' => '=',
                    'type'    => 'numeric'
                ]
            ],
            'orderby' => 'ID'
        ]);
    
    
            
        $pre = sanitize($raw->posts, $fields);
        
        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['src'] = get_post_meta( $p['id'], 'oneway_audiolink', true );
                
                $p['duration'] = 0;
                
                $output[] = $p;
            }

            json($output);
        } else {
            json([]);
        }
    
    break;
    
    
    
    
    
    // COUNT
    case 'count':
        json([
            'count' => intval(wp_count_posts('audio')->publish),
            'limit' => $limit
        ]);
    break;
    
    
    
    
    
    
    default:
        echo 'Shhh ...!';
    break;
}










?>
