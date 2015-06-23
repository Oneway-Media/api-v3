<?php
define('LIMIT', 30);
//define('FIELDS', 'basic');

class Audio {
	
	// Get Categories
	public static function category($id = null) {
        $fields = ['id'=>'cat_ID','slug'=>'slug','title'=>'name','description'=>'description','count'=>'count'];
        // All categories
        $categories = get_categories(['type'=>'audio','taxonomy'=>'audio_category','orderby'=>'id','order'=>'ASC']);        
		// Get all categories
        if($id === null) {
            return sanitize( $categories, $fields);        
        // Get category by its ID or Slug
        } else {                                         
            if( is_numeric($id) ) {
                return sanitize( find($categories, 'cat_ID', $id), $fields); // By ID
            } else {
                return sanitize( find($categories, 'slug', $id), $fields);// By Slug
            }
        }        
	}
    
	
    // Online Radio
    public static function radio() {
        $id = get_posts(['post_type'=>'radio','posts_per_page'=>1])[0]->ID;
        $content = '<ul id="radio_block_jumpover">';
        $content .= '<li><a href="#radio_block_00_12">0h-12h</a></li>';
        $content .= '<li><a href="#radio_block_12_24">12h-24h</a></li>';
        $content .= '</ul>';
        $content .= '<div id="radio_block_00_12" class="radio_block"><div class="radio_block_milestone">0h-12h</div>';
        $content .= get_post_meta( $id, 'oneway_radioblock1', true );
        $content .= '</div>';
        $content .= '<div id="radio_block_12_24" class="radio_block"><div class="radio_block_milestone">12h-24h</div>';
        $content .= get_post_meta( $id, 'oneway_radioblock2', true );
        $content .= '</div>';
        
        $radio = [
            'id' => 0,
            'slug' => 'oneway-radio-truc-tuyen',
            'date' => 'Hôm nay',
            'title' => 'Radio Trực tuyến ngày '.date('d-m-Y'),
            'permalink' => 'http://oneway.vn/',
            'excerpt' => 'Oneway Radio - Kênh Radio Cơ Đốc trực tuyến 24/7 dành cho người Việt.',
            'content' => $content,
            'src' => 'http://radio.oneway.vn:8000/ow',
            'keyword' => 'oneway, radio, co doc',
            'icon' => 'http://oneway.vn/api/api-v3/public/icon-xxx.png',
            'thumbnail' => 'http://oneway.vn/api/api-v3/public/thumbnail-xxx.jpg',
            'cover' => 'http://oneway.vn/api/api-v3/public/cover-xxx.jpg',
            'view' => 0,
            'like' => 0,
            'share' => 0
        ];
        
        return [$radio];
    }
    
    
    // Searching
    public static function search($keyword, $category = null) {
        
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];
        
        if( $category === null ) { // Search all            
            $raw = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'audio',
                'posts_per_page' => LIMIT,
                's' => $keyword                
            ]);
        } else { //Search by category
            $raw = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'audio',
                'tax_query' => [
                    [
                        'taxonomy' => 'audio_category',
                        'field'    => 'slug',
                        'terms'    => $category,
                    ]
                ],
                'posts_per_page' => LIMIT,
                's' => $keyword                
            ]);
        }
            
        $pre = sanitize($raw->posts, $fields);
        
        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }
        
    }
    
    // Get list of audio.
    public static function listAudio($from,$limit = null, $sort = null) {
        $ord = array();

        if ($limit=='') {$limit = '30'; };
        if ($sort=='') {
            $sort = 'new';
            $ord = array( 'date' => 'DESC' );
        } else if ($sort == 'az') {
            // Tua de hien tai chua the sap xep theo theo thu tu
            //$ord = array( 'title' => 'ASC' );
            $ord = array( 'date' => 'DESC' );
        } else {
            $ord = array( 'date' => 'DESC' );
        };

        $offset = intval($from)*intval($limit);

        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];
        if ($sort != 'view') {
            $raw = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'audio',
                'orderby' => $ord,
                'offset' => $offset,
                'posts_per_page' => $limit                
            ]);    
        } else if ($sort == 'view') {
            $raw = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'audio',
                'orderby'   => 'meta_value_num',
                'meta_key'  => '_count-views_all',
                'order'   => 'DESC',
                'offset' => $offset,
                'posts_per_page' => $limit                
            ]);
        }
        

        $pre = sanitize($raw->posts, $fields);

        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }

        // $res = 'From :'.$from.', Limit :'.$limit.', Sort :'.$sort;
        // return $res;

    }

	
}

?>