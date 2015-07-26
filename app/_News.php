<?php
define('LIMIT', 30);


class News {
	
	
	// Get Categories
	public static function category($id = null) {
        $fields = ['id'=>'cat_ID','slug'=>'slug','title'=>'name','description'=>'description','count'=>'count'];
        // All categories
        $categories = get_categories(['type'=>'news','taxonomy'=>'news_category','orderby'=>'id','order'=>'ASC','exclude'=>'82']);        
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
                'post_type' => 'news',
                'posts_per_page' => LIMIT,
                's' => $keyword                
            ]);
        } else { //Search by category
            if( is_numeric($category) ) {
                // Using Term ID
                $raw = new WP_Query([
                    'post_status' => 'publish',
                    'post_type' => 'news',
                    'tax_query' => [
                        [
                            'taxonomy' => 'news_category',
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
                    'post_type' => 'news',
                    'tax_query' => [
                        [
                            'taxonomy' => 'news_category',
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
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }
        
    }


	// Get list of News.
    public static function listNews($from,$limit = null, $sort = null) {

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
            'post_type' => 'news',
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

    }


    //Get list of news belong to specific category
    public static function listNewsCate($id,$from,$limit = null, $sort=null) {
        
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
            'post_type' => 'news',
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
                    'taxonomy' => 'news_category',
                    'field'    => 'term_id',
                    'terms'    => $id,
                ]
            ];
        } else {
            // By Slug
            $arg['tax_query'] = [
                [
                    'taxonomy' => 'news_category',
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

    //Get related news by anchor news.
    public static function listNewsRel($id,$from,$limit = null,$sort = null) {
        
        // Switch $id to ID from slug
        if( !is_numeric($id) ) {
            // get slug from id
            $post = get_page_by_path($id, OBJECT, 'news');
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

        //Get news from keyword
        $keyword_value = get_post_meta( $id, '_yoast_wpseo_focuskw', true );
        // check if the custom field has a value
        if( ! empty( $keyword_value ) ) {
            $search_res = self::search(no_mark($keyword_value)); 
            $res = array_splice($search_res, ($offset+1), $search_num);
        } else {
            $other_num = (int)($limit /2);
        }

        //Get news random from category
        $cat = wp_get_post_terms($id,'news_category');
        $idcat = $cat[0]->term_id;
        $random_CateNews = self::listNewsCate($idcat,$offset,$other_num);
        $res = array_merge($res,$random_CateNews);

        //Get news random from news
        $randomNews = self::listNews($offset,$other_num,'view');
        $res = array_merge($res,$randomNews);

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
            $re_post = self::listNews(0,$missing,'new');
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


     //Get related news by anchor news.
    public static function newsItem($id,$fields = null) {
        
        // Switch $id to ID from slug
        if( !is_numeric($id) ) {
            // get posst from slug
            $post = get_page_by_path($id, OBJECT, 'news');
            
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
                    $p['keyword'] = get_post_meta( $p['id'], '_yoast_wpseo_focuskw', true );
                    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $p['content'], $img);
                    $p['cover'] = $img['1'];
                    $p['view'] = get_post_meta( $p['id'], '_count-views_all', true );
                    $p['like'] = get_post_meta( $p['id'], 'oneway_like', true );
                    $p['share'] = get_post_meta( $p['id'], 'oneway_share', true );
                    $output[] = $p;
                }

                return $output;
            } else {
                return [];
            }
        } else if ($fields == 'extra')  {
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

            $cmts = get_comments( 'post_id='.$id );
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
                    $p['keyword'] = get_post_meta( $p['id'], '_yoast_wpseo_focuskw', true );
                    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $p['content'], $img);
                    $p['cover'] = $img['1'];
                    $p['view'] = get_post_meta( $p['id'], '_count-views_all', true );
                    $p['like'] = get_post_meta( $p['id'], 'oneway_like', true );
                    $p['share'] = get_post_meta( $p['id'], 'oneway_share', true );
                    $p['comments'] = $cmts_pre;
                    $output[] = $p;
                }

                return $output;

            } else {
                return [];
            }


        }


    }
	
    // Count all news.
    public static function countAll() {
        $allPosts = wp_count_posts('news');
        return $allPosts->publish;
    }

    // Count news by category.
    public static function countCate($id) {

        $taxonomy = "news_category"; // can be category, post_tag, or custom taxonomy name
         
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
            'post_type' => 'news',
            'posts_per_page' => '30',  
            'orderby'   => 'rand'
        ];

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
            'post_type' => 'news',
            'offset' => $offset,
            'posts_per_page' => $limit,  
            'orderby'   => 'rand'
        ];


        if( is_numeric($id) ) {
            // By ID
            $arg['tax_query'] = [
                [
                    'taxonomy' => 'news_category',
                    'field'    => 'term_id',
                    'terms'    => $id,
                ]
            ];
        } else {
            // By Slug
            $arg['tax_query'] = [
                [
                    'taxonomy' => 'news_category',
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
	
}

?>  