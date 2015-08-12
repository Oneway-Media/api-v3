<?php
define('LIMIT', 30);
//define('FIELDS', 'basic');

class Audio {
	
	// Get Categories
	public static function category($id = null) {
        $fields = ['id'=>'cat_ID','slug'=>'slug','title'=>'name','description'=>'description','count'=>'count'];
        // All categories
        $categories = get_categories(['type'=>'audio','taxonomy'=>'audio_category','orderby'=>'id','order'=>'ASC','exclude'=>'83']);         
        
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
	
	// Get Tags
	public static function tag($id = null) {
        $fields = ['id'=>'term_id','slug'=>'slug','title'=>'name','description'=>'description','count'=>'count'];
        // All categories
        $tags = get_terms(['audio_tag']);
        
		// Get all categories
        if($id === null) {
            return sanitize( $tags, $fields);        
        // Get category by its ID or Slug
        } else {                                         
            if( is_numeric($id) ) {
                return sanitize( find($tags, 'term_id', $id), $fields); // By ID
            } else {
                return sanitize( find($tags, 'slug', $id), $fields);// By Slug
            }
        }        
	}
    
    
    
	//Get list of audio belong to specific category
    public static function listAudioTag($id = null) {
        
        if( $id === null ) {
            return [];
        }
        
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];
        
        
        if( is_numeric($id) ) {
            // Using Term ID
            $raw = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'audio',
                'tax_query' => [
                    [
                        'taxonomy' => 'audio_tag',
                        'field'    => 'term_id',
                        'terms'    => $id,
                    ]
                ],
                'posts_per_page' => LIMIT    
            ]);
        } else {
            // Using Term Slug
            $raw = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'audio',
                'tax_query' => [
                    [
                        'taxonomy' => 'audio_tag',
                        'field'    => 'slug',
                        'terms'    => $id,
                    ]
                ],
                'posts_per_page' => LIMIT    
            ]);
        }
        
        $pre = sanitize($raw->posts, $fields);
        
        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
				
				// Audio Duration
				$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
				$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
				
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }

    }
    
    
	
    // Online Radio
    public static function radio() {
        $id = get_posts(['post_type'=>'radio','posts_per_page'=>1])[0]->ID;
        //$content = '<ul id="radio_block_jumpover">';
        //$content .= '<li><a href="#radio_block_00_12">0h-12h</a></li>';
        //$content .= '<li><a href="#radio_block_12_24">12h-24h</a></li>';
        //$content .= '</ul>';
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
            'icon' => 'http://oneway.vn/api/api-v3/public/icon-radio.png',
            'thumbnail' => 'http://oneway.vn/api/api-v3/public/thumbnail-radio.png',
            'cover' => 'http://oneway.vn/api/api-v3/public/cover-radio.png',
            'view' => 0,
            'like' => 0,
            'share' => 0,
            'duration' => '∞'
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
        } else {
            if( is_numeric($category) ) {
                // Using Term ID
                $raw = new WP_Query([
                    'post_status' => 'publish',
                    'post_type' => 'audio',
                    'tax_query' => [
                        [
                            'taxonomy' => 'audio_category',
                            'field'    => 'term_id',
                            'terms'    => $category,
                        ]
                    ],
                    'posts_per_page' => LIMIT,
                    's' => $keyword                
                ]);
            } else {
                // Using Term Slug
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
        }
        
            
        $pre = sanitize($raw->posts, $fields);
        
        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
				
				// Audio Duration
				$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
				$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
				
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
            'thumbnail' => '',
			'permalink' => ''
			
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
				$p['permalink'] = get_permalink($p['id']);
				$p['view'] = get_post_meta( $p['id'], '_count-views_all', true );
                $p['like'] = get_post_meta( $p['id'], 'oneway_like', true );
				// Audio Duration
				$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
				$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
				
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }


    }

	//Get list of audio belong to specific category
    public static function listAudioCate($id,$from,$limit = null, $sort=null) {
        
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => '',
			'permalink' => ''
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
				$p['permalink'] = get_permalink($p['id']);
				
				// Audio Duration
				$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
				$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
				
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
				$p['view'] = get_post_meta( $p['id'], '_count-views_all', true );
                $p['like'] = get_post_meta( $p['id'], 'oneway_like', true );
				// Audio Duration
				$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
				$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
				
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }

    }

     //Get related audio by anchor audio.
    public static function audioItem($id,$fields = null) {
        
        // Switch $id to ID from slug
        if( !is_numeric($id) ) {
            // get posst from slug
            $post = get_page_by_path($id, OBJECT, 'audio');
            
        } else {
            //get post from ID
            $post = get_post($id);
        }



        if ($fields == 'basic' || $fields === null) {
            $basic_fields = [
                'id' => 'ID',
                'slug' => 'post_name',
                'title' => 'post_title',
                'date' => 'post_date',
                'permalink' => '',
                'excerpt' => 'post_excerpt',
                'content' => 'post_content',
                'src' => '',
                'keyword' => '',
                'cover' => '',
                'view' => '',
                'like' => '',
                'share' => '',
                'thumbnail' => ''
            ];


            $pre = sanitize($post, $basic_fields);

            if(count($pre) > 0) {
                foreach($pre as $p) {
                    $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                    $p['permalink'] = get_permalink($p['id']);
                    $p['src'] = get_post_meta( $p['id'], 'oneway_audiolink', true );
                    $p['keyword'] = get_post_meta( $p['id'], '_yoast_wpseo_focuskw', true );
                    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $p['content'], $img);
                    $p['cover'] = $img['1'];
					$p['view'] = intval( get_post_meta( $p['id'], '_count-views_all', true ) );
                    $p['like'] = intval( get_post_meta( $p['id'], 'oneway_like', true ) );
                    $p['share'] = intval( get_post_meta( $p['id'], 'oneway_share', true ) );
					
					// Audio Duration
					$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
					$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
					
					// Filter the content
                    $p['content'] = apply_filters( 'the_content', $p['content'] );
					
                    $output[] = $p;
                }

                return $output;
            } else {
                return [];
            }
        }

         else if ($fields == 'extra')  {
            $cmts = get_comments( 'post_id='.$id);
            $extra_fields = [
                'id' => 'comment_ID',
                'author_name' => 'comment_author',
                'author_email' => 'comment_author_email',
                'date' => 'comment_date',
                'content' => 'comment_content',
                'approved' => 'comment_approved',
                'parent_id' => 'comment_parent',
                'user_id' => 'user_id'
            ];

            $pre = sanitize($cmts, $extra_fields);

            if(count($pre) > 0) {
                return $pre;
            } else {
                return [];
            }

        } else if ($fields == 'full')  {

             $cmts = get_comments( 'post_id='.$id);
            $cmt_fields = [
                'id' => 'comment_ID',
                'author_name' => 'comment_author',
                'author_email' => 'comment_author_email',
                'date' => 'comment_date',
                'content' => 'comment_content',
                'approved' => 'comment_approved',
                'parent_id' => 'comment_parent',
                'user_id' => 'user_id'
            ];

            $cmts_pre = sanitize($cmts, $cmt_fields);

            $full_fields = [
                'id' => 'ID',
                'slug' => 'post_name',
                'title' => 'post_title',
                'date' => 'post_date',
                'permalink' => '',
                'excerpt' => 'post_excerpt',
                'content' => 'post_content',
                'src' => '',
                'keyword' => '',
                'cover' => '',
                'view' => '',
                'like' => '',
                'share' => '',
                'thumbnail' => '',
                'comments' => ''
            ];

            $full_pre = sanitize($post, $full_fields);

            if (count($full_pre) > 0) {
                foreach($full_pre as $p) {
                    $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                    $p['permalink'] = get_permalink($p['id']);
                    $p['src'] = get_post_meta( $p['id'], 'oneway_audiolink', true );
                    $p['keyword'] = get_post_meta( $p['id'], '_yoast_wpseo_focuskw', true );
                    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $p['content'], $img);
                    $p['cover'] = $img['1'];
                    $p['view'] = intval( get_post_meta( $p['id'], '_count-views_all', true ) );
                    $p['like'] = intval( get_post_meta( $p['id'], 'oneway_like', true ) );
                    $p['share'] = intval( get_post_meta( $p['id'], 'oneway_share', true ) );
                    $p['comments'] = $cmts_pre;
					
					// Audio Duration
					$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
					$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
					
                    $output[] = $p;
                }

                return $output;

            } else {
                return [];
            }


        }


    } 
          

    // Count all audio.
    public static function countAll() {
        $allPosts = wp_count_posts('audio');
        return $allPosts->publish;
    }

    // Count audio by category.
    public static function countCate($id) {

        $taxonomy = "audio_category"; // can be category, post_tag, or custom taxonomy name
         
        if( is_numeric($id) ) {
            // Using Term ID
            $term = get_term_by('id', $id, $taxonomy);
        } else {
            // Using Term Slug
            $term = get_term_by('slug', $id, $taxonomy);
        }
        // Fetch the count
        return $term->count;

    }

    // Get random items.
    public static function randomAll() {
        

        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];

        $arg = [
            'post_status' => 'publish',
            'post_type' => 'audio',
            'posts_per_page' => '30',  
            'orderby'   => 'rand'
        ];

        $raw = new WP_Query($arg);

        $pre = sanitize($raw->posts, $fields);

        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
				
				// Audio Duration
				$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
				$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
				
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }


    }

    // Get random items by category.
    public static function randomCate($id,$from, $limit = null) {
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];

        if ($from > 0) {$from = $from - 1;} else if ($from <= 0) {$from = 0;};
        if ($limit == null) { $limit = 30; };

        $offset = intval($from)*intval($limit);

        $arg = [
            'post_status' => 'publish',
            'post_type' => 'audio',
            'offset' => $offset,
            'posts_per_page' => $limit,  
            'orderby'   => 'rand'
        ];


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
				
				// Audio Duration
				$duration = get_post_meta( $p['id'], 'oneway_audioduration', true );				
				$p['duration'] = ( is_numeric( $duration) && $duration != '' ) ? intval($duration): 0;
				
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }
    }

	// Update audio meta
	public static function updateMeta($id = null, $key = null, $value = null) {
		
		if($id === null || $key === null) {
			return false;
		}
		
		if($value == null) {
			$value = 1;
		}
		
		if( ! get_post_status( $id ) ) {
			return false;
		}
		
		switch($key) {
			
			// Duration
			case 'duration':
				
				if( ! is_numeric($value) || $value < 100 ) {
					return false;
				}
				
				$duration = get_post_meta( $id, 'oneway_audioduration', true );
				
//				if( intval($duration) > 0 ) {
//					return true;
//				}
				
				update_field('oneway_audioduration', $value, $id);
				return true;
			break;
			
			
			
			// Like
			case 'like':
				$value = intval($value);
				
				if($value < -1 || $value > 1 || $value == 0) {
					return false;
				}
			
				$key = 'oneway_like';				
				$like = intval( get_post_meta( $id, $key, true ) );
				
				if($value == -1 && $like <= 0) {
					return true;
				}
				
				update_field($key, $like + $value, $id);
				
				return true;
			break;
            
            
            
			// Share
			case 'share':
				$key = 'oneway_share';				
				$share = intval( get_post_meta( $id, $key, true ) );	
				update_field($key, $share + 1, $id);
				return true;
			break;
			
			
            // Status
			case 'status':
            
                if( $value == 'publish' || $value == 'private' || $value == 'future' || $value == 'draft' || $value == 'trash' ) {
                    wp_update_post([
                        'ID' => $id,
                        'post_status' => $value
                    ]);
                    
                    return true;
                }
            
                return false;
            break;
            
            
			
			default:
				return false;
			break;
		}
		
	}

    // Add a comment to an item.
    public static function addComment($id = null, $comment = null) {
        if($id == null || $comment == null) {
            return false;
        } else if( $comment['content'] == null ) {
            return false;
        }

        $commentdata = array(
            'comment_post_ID' => $id, // to which post the comment will show up
            'comment_author' => $comment['name'], //fixed value - can be dynamic 
            'comment_author_email' => $comment['email'], //fixed value - can be dynamic 
            'comment_content' => $comment['content'], //fixed value - can be dynamic 
            'comment_approved' => 0, //set comment's status is pending
            // 'comment_parent' => 0, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
            // 'user_id' => $current_user->ID, //passing current user ID or any predefined as per the demand
        );

        //Insert new comment and get the comment ID
        $cmt_id = wp_new_comment( $commentdata );

        return true;

    }

    // Get list of comments.
    public static function listComment($id, $from = null, $limit = null) {

        $fields = [
            'id' => 'comment_ID',
            'name' => 'comment_author',
            'email' => 'comment_author_email',
            'content' => 'comment_content',
            'date' => 'comment_date',
        ];

        $count = get_comments(['post_id' => $id,'count'=> true, 'status' => 'approve',]);

        if ($limit == null) { 
            $limit = 10; 
        }

        if ( intval($limit) > intval($count)) {
            $limit = $count;
            $from = 0;
        }

        if ($from > 0) {$from = $from - 1;} else if ($from <= 0 || $from == null) {$from = 0;};
        

        $offset = intval($from)*intval($limit);

        if ($offset > intval($count)) {
            $offset = 0;
            $limit = intval($count);
        }

        if (($offset+intval($limit)) > intval($count)) {
            $limit = intval($count) - $offset;
        }

        // WP_Comment_Query arguments
        $args = array (
            'id'             => $id,
            'status'         => 'approve',
            'number' => $limit,
            'offset' => $offset,
        );

        // The Comment Query
        $comment_query = new WP_Comment_Query;
        $comments = $comment_query->query( $args );
        

        $pre = sanitize($comments, $fields);

        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['timer'] =  timer($p['date']);
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }

    }
    
    
}

?>