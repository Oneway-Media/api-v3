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





// TEST area
$router->get('/test', function () {
    // Test your idea here ...    
});





/*-----------------------------------------------------------------------------------------------
*	Routes end here
-------------------------------------------------------------------------------------------------*/
$router->notFound(function () { echo '<h1>404 - Not Found!</h1>'; }); $router->run();
?>