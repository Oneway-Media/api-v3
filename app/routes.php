<?php $router = new \Slim\Slim();
// Models
$Audio = new Audio; $News = new News;

/*-----------------------------------------------------------------------------------------------
*	Routes start here
-------------------------------------------------------------------------------------------------*/

// $router->get('/:id', function ($id) { ...	});
// $router->post('/', function () use ($router) { $router->request()->params('paramName'); });

/*------------------------------Audio-----------------------------*/

/*
*   http://test.oneway.vn/api/api-v3/index.php
*/
$router->get('/', function () { echo "<h1>Oneway.vn API v3.0.0!</h1>"; });

/*
*   http://test.oneway.vn/api/api-v3/index.php/category
*/

$router->get('/category', function () use ($Audio) {
    json( $Audio->category() );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/category/:id|slug
*/
$router->get('/category/:id', function ($id) use ($Audio) {
    json( $Audio->category($id) );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/tag
*/
$router->get('/tag', function () use ($Audio) {
    json( $Audio->tag() );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/tag/:id|slug
*/
$router->get('/tag/:id', function ($id) use ($Audio) {
    json( $Audio->tag($id) );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/audio-tag/:slug|id
*/
$router->get('/audio-tag/:id', function ($id) use ($Audio) {
    json( $Audio->listAudioTag($id) );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/radio
*/
$router->get('/radio', function () use ($Audio) {
    json( $Audio->radio() );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/search/:keyword
*/
$router->get('/search/:keyword', function ($keyword) use ($Audio) {
    json( $Audio->search($keyword) );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/search/:keyword/:category
*/
$router->get('/search/:keyword/:category', function ($keyword, $category) use ($Audio) {
    json( $Audio->search($keyword, $category) );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/audio/:from[/:limit/:sort]
*/
$router->get('/audio/:from', function ($from) use ($Audio) {
    json( $Audio->listAudio($from) );
});
$router->get('/audio/:from/:sort', function ($from,$limit) use ($Audio) {
    json($Audio->listAudio($from,$limit));
});
$router->get('/audio/:from/:limit/:sort', function ($from, $limit, $sort) use ($Audio) {
    json($Audio->listAudio($from,$limit,$sort));
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/audio-category/:slug|id/:from[/:limit/:sort]
*/
$router->get('/audio-category/:id/:from', function ($id,$from) use ($Audio) {
    json($Audio->listAudioCate($id,$from));
});
$router->get('/audio-category/:id/:from/:limit', function ($id,$from,$limit) use ($Audio) {
    json($Audio->listAudioCate($id,$from,$limit));
});
$router->get('/audio-category/:id/:from/:limit/:sort', function ($id,$from, $limit, $sort) use ($Audio) {
    json($Audio->listAudioCate($id,$from,$limit,$sort));
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/audio-related/:slug|id/:from[/:limit/:sort]
*/
$router->get('/audio-related/:id/:from', function ($id,$from) use ($Audio) {
    json($Audio->listAudioRel($id,$from));
});
$router->get('/audio-related/:id/:from/:limit', function ($id,$from,$limit) use ($Audio) {
    json($Audio->listAudioRel($id,$from,$limit));
});
$router->get('/audio-related/:id/:from/:limit/:sort', function ($id,$from, $limit, $sort) use ($Audio) {
    json($Audio->listAudioRel($id,$from,$limit,$sort));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/audio-item/:slug|:id/:fields
*/
$router->get('/audio-item/:id', function ($id) use ($Audio) {
    json($Audio->audioItem($id));
});
$router->get('/audio-item/:id/:fields', function ($id,$fields) use ($Audio) {
    json($Audio->audioItem($id,$fields));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/count
*/
$router->get('/count', function () use ($Audio) {
    json($Audio->countAll());
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/count/:id
*/
$router->get('/count/:id', function ($id) use ($Audio) {
    json($Audio->countCate($id));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/random
*/
$router->get('/random', function () use ($Audio) {
    json($Audio->randomAll());
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/random/:slug|:id/:from[/:limit/:sort]
*/
$router->get('/random/:id/:from', function ($id,$from) use ($Audio) {
    json($Audio->randomCate($id,$from));
});
$router->get('/random/:id/:from/:limit', function ($id,$from,$limit) use ($Audio) {
    json($Audio->randomCate($id,$from,$limit));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/comment/:id[/:from/:limit]
*/
$router->get('/comment/:id', function ($id) use ($Audio) {
    json( $Audio->listComment($id) );
});
$router->get('/comment/:id/:from', function ($id,$from) use ($Audio) {
    json($Audio->listComment($id,$from));
});
$router->get('/comment/:id/:from/:limit', function ($id,$from,$limit) use ($Audio) {
    json($Audio->listComment($id,$from,$limit));
});



/*
*   http://test.oneway.vn/api/api-v3/index.php/audio/update-meta/
	:id 
	:key = duration || like || share
	:value
*/
$router->post('/audio/update-meta', function () use ($router, $Audio) {
	json(
		$Audio->updateMeta(
			$router->request()->params('id'),
			$router->request()->params('key'),
			$router->request()->params('value')
		)
	);	
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/post-comment/
    :id 
    :comment = [email,name,content]
*/
$router->post('/post-comment', function () use ($router, $Audio) {
    json(
        $Audio->addComment(
            $router->request()->params('id'),
            [
                'email'=> $router->request()->params('email'),
                'name'=> $router->request()->params('name'),
                'content'=> $router->request()->params('content'),
            ]
        )
    );  
});








/*------------------------------Tin Tuc-------------------------------*/

/*
*   http://test.oneway.vn/api/api-v3/index.php/category-news
*/
$router->get('/category-news', function () use ($News) {
    json( $News->category() );
});
/*
*   http://test.oneway.vn/api/api-v3/index.php/category-news/:id|slug
*/
$router->get('/category-news/:id', function ($id) use ($News) {
    json( $News->category($id) );
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/news/:from[/:limit/:sort]
*/
$router->get('/news/:from', function ($from) use ($News) {
    json( $News->listNews($from) );
});
$router->get('/news/:from/:sort', function ($from,$limit) use ($News) {
    json ($News->listNews($from,$limit));
});
$router->get('/news/:from/:limit/:sort', function ($from, $limit, $sort) use ($News) {
    json ($News->listNews($from,$limit,$sort));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/news-category/:slug|id/:from[/:limit/:sort]
*/
$router->get('/news-category/:id/:from', function ($id,$from) use ($News) {
    json ($News->listNewsCate($id,$from));
});
$router->get('/news-category/:id/:from/:limit', function ($id,$from,$limit) use ($News) {
    json ($News->listNewsCate($id,$from,$limit));
});
$router->get('/news-category/:id/:from/:limit/:sort', function ($id,$from, $limit, $sort) use ($News) {
    json ($News->listNewsCate($id,$from,$limit,$sort));
});



/*
*   http://test.oneway.vn/api/api-v3/index.php/news-related/:slug|id/:from[/:limit/:sort]
*/
$router->get('/news-related/:id/:from', function ($id,$from) use ($News) {
    json ($News->listNewsRel($id,$from));
});
$router->get('/news-related/:id/:from/:limit', function ($id,$from,$limit) use ($News) {
    json ($News->listNewsRel($id,$from,$limit));
});
$router->get('/news-related/:id/:from/:limit/:sort', function ($id,$from, $limit, $sort) use ($News) {
    json ($News->listNewsRel($id,$from,$limit,$sort));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/news-item/:slug|:id/:fields
*/
$router->get('/news-item/:id', function ($id) use ($News) {
    json ($News->newsItem($id));
});
$router->get('/news-item/:id/:fields', function ($id,$fields) use ($News) {
    json ($News->newsItem($id,$fields));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/count-news
*/
$router->get('/count-news', function () use ($News) {
    json ($News->countAll());
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/count-news/:id
*/
$router->get('/count-news/:id', function ($id) use ($News) {
    json ($News->countCate($id));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/random-news
*/
$router->get('/random-news', function () use ($News) {
    json ($News->randomAll());
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/random/:slug|:id/:from[/:limit/:sort]
*/
$router->get('/random-news/:id/:from', function ($id,$from) use ($News) {
    json ($News->randomCate($id,$from));
});
$router->get('/random-news/:id/:from/:limit', function ($id,$from,$limit) use ($News) {
    json ($News->randomCate($id,$from,$limit));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/search-news/:keyword
*/
$router->get('/search-news/:keyword', function ($keyword) use ($News) {
    json( $News->search($keyword) );
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/search-news/:keyword/:category
*/
$router->get('/search-news/:keyword/:category', function ($keyword, $category) use ($News) {
    json( $News->search($keyword, $category) );
});




// TEST area
$router->get('/test', function () {
    $data = array(
        'name' => 'linh',
        'email' => 'linh@gmail.com',
        'content' => 'tesst lan 2'
    );
    echo $query = http_build_query(array('aParam' => $data));
});




/*-----------------------------------------------------------------------------------------------
*	Routes end here
-------------------------------------------------------------------------------------------------*/
$router->notFound(function () { echo '<h1>404 - Not Found!</h1>'; }); $router->run();
?>