<?php $router = new \Slim\Slim();
// Models
$Audio = new Audio; $News = new News;

/*-----------------------------------------------------------------------------------------------
*	Routes start here
-------------------------------------------------------------------------------------------------*/

// $router->get('/:id', function ($id) { ...	});
// $router->post('/', function () use ($router) { $router->request()->params('paramName'); });



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
	// $res = $Audio->listAudio($from);
 //    echo $res;
    json( $Audio->listAudio($from) );
});
$router->get('/audio/:from/:sort', function ($from,$limit) use ($Audio) {
    json ($Audio->listAudio($from,$limit));
});
$router->get('/audio/:from/:limit/:sort', function ($from, $limit, $sort) use ($Audio) {
    json ($Audio->listAudio($from,$limit,$sort));
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/audio-category/:slug|id/:from[/:limit/:sort]
*/
$router->get('/audio-category/:id/:from', function ($id,$from) use ($Audio) {
    json ($Audio->listAudioCate($id,$from));
});
$router->get('/audio-category/:id/:from/:limit', function ($id,$from,$limit) use ($Audio) {
    json ($Audio->listAudioCate($id,$from,$limit));
});
$router->get('/audio-category/:id/:from/:limit/:sort', function ($id,$from, $limit, $sort) use ($Audio) {
    json ($Audio->listAudioCate($id,$from,$limit,$sort));
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/audio-related/:slug|id/:from[/:limit/:sort]
*/
$router->get('/audio-related/:id/:from', function ($id,$from) use ($Audio) {
    json ($Audio->listAudioRel($id,$from));
});
$router->get('/audio-related/:id/:from/:limit', function ($id,$from,$limit) use ($Audio) {
    json ($Audio->listAudioRel($id,$from,$limit));
});
$router->get('/audio-related/:id/:from/:limit/:sort', function ($id,$from, $limit, $sort) use ($Audio) {
    json ($Audio->listAudioRel($id,$from,$limit,$sort));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/audio-item/:slug|:id/:fields
*/
$router->get('/audio-item/:id', function ($id) use ($Audio) {
    json ($Audio->audioItem($id));
});
$router->get('/audio-item/:id/:fields', function ($id,$fields) use ($Audio) {
    json ($Audio->audioItem($id,$fields));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/count
*/
$router->get('/count', function () use ($Audio) {
    json ($Audio->countAll());
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/count/:id
*/
$router->get('/count/:id', function ($id) use ($Audio) {
    json ($Audio->countCate($id));
});


/*
*   http://test.oneway.vn/api/api-v3/index.php/random
*/
$router->get('/random', function () use ($Audio) {
    json ($Audio->randomAll());
});

/*
*   http://test.oneway.vn/api/api-v3/index.php/random/:slug|:id/:from[/:limit/:sort]
*/
$router->get('/random/:id/:from', function ($id,$from) use ($Audio) {
    json ($Audio->randomCate($id,$from));
});
$router->get('/random/:id/:from/:limit', function ($id,$from,$limit) use ($Audio) {
    json ($Audio->randomCate($id,$from,$limit));
});


// TEST area
$router->get('/test', function () {
    // Test your idea here ... 
});






/*-----------------------------------------------------------------------------------------------
*	Routes end here
-------------------------------------------------------------------------------------------------*/
$router->notFound(function () { echo '<h1>404 - Not Found!</h1>'; }); $router->run();
?>