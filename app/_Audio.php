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

        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];

        if ($from > 0) {$from = $from - 1;} else if ($from <= 0) {$from = 0;};
        if ($limit == null) { $limit = 30; };
        if ($sort == null) { $sort = 'new';};

        $offset = intval($from)*intval($limit);

        $arg = [
            'post_status' => 'publish',
            'post_type' => 'audio',
            'offset' => $offset,
            'posts_per_page' => $limit,                
        ];

        if ($sort == 'view') {
            $arg['orderby'] = 'meta_value_num';
            $arg['meta_key'] = '_count-views_all';
            $arg['order'] = 'DESC';
            
        } else {
            $arg['orderby'] = ['date' => 'DESC'];
            
        }
        
        $raw = new WP_Query($arg);

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

	//Get list of audio belong to specific category
    public static function listAudioCate($id,$from,$limit = null, $sort=null) {
        
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];

        if ($from > 0) {$from = $from - 1;} else if ($from <= 0) {$from = 0;};
        if ($limit == null) { $limit = 30; };
        if ($sort == null) { $sort = 'new';};

        $offset = intval($from)*intval($limit);

        $arg = [
            'post_status' => 'publish',
            'post_type' => 'audio',
            'offset' => $offset,
            'posts_per_page' => $limit,             
        ];

        

        if ($sort == 'view') {
            $arg['orderby'] = 'meta_value_num';
            $arg['meta_key'] = '_count-views_all';
            $arg['order'] = 'DESC';
            
        } else {
            $arg['orderby'] = ['date' => 'DESC'];
            
        }

        if( is_numeric($id) ) {
            // By ID
            $arg['tax_query'] = [
                [
                    'taxonomy' => 'audio_category',
                    'field'    => 'term_id',
                    'terms'    => $id,
                ]
            ];
        } else {
            // By Slug
            $arg['tax_query'] = [
                [
                    'taxonomy' => 'audio_category',
                    'field'    => 'slug',
                    'terms'    => $id,
                ]
            ];
        }

        $raw = new WP_Query($arg);

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


    //Get related audio by anchor audio.
    public static function listAudioRel($id,$from,$limit = null,$sort = null) {
        
        // Switch $id to ID from slug
        if( !is_numeric($id) ) {
            // get slug from id
            $post = get_page_by_path($id, OBJECT, 'audio');
            $id = $post->ID;
        };


        if ($from > 0) {$from = $from - 1;} else if ($from <= 0) {$from = 0;};

        //Divide amount to get from three sources
        if ($limit == null || $limit > 30) { 
            $limit = 30;
            $search_num = 10;
        } else {
            $search_num =  (int) ($limit/3) + ($limit%3);
        }

        $other_num = ($limit - $search_num)/2;

        $offset = $limit*$from;

        //Get audio from keyword
        $keyword_value = get_post_meta( $id, '_yoast_wpseo_focuskw', true );
        // check if the custom field has a value
        if( ! empty( $keyword_value ) ) {
            $search_res = self::search(no_mark($keyword_value)); 
            $res = array_splice($search_res, ($offset+1), $search_num);
        } else {
            $other_num = (int)($limit /2);
        }

        //Get audio random from category
        $cat = wp_get_post_terms($id,'audio_category');
        $idcat = $cat[0]->term_id;
        $random_CateAudio = self::listAudioCate($idcat,$offset,$other_num);
        $res = array_merge($res,$random_CateAudio);

        //Get audio random from audio
        $randomAudio = self::listAudio($offset,$other_num,'view');
        $res = array_merge($res,$randomAudio);

        //Search for the same post
        $same_post_key = array_search($id, array_column($res, 'id'));

        //Remove same post 
        unset($res[$same_post_key]);
        //Reset array
        array_values($res);

        // Replace another post to fill out array 
        if (count($res) < $limit) {
            $missing = $limit - count($res);
            //Get replace post(s).
            $re_post = self::listAudio(0,$missing,'new');
            $res = array_merge($res,$re_post);
        }


         if(count($res) > 0) {
            foreach($res as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }

    }

     
}

?>