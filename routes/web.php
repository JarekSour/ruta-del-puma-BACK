<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//WEB

$router->group(['prefix' => 'admin'], function () use ($router) {
    $router->post('/login', 'AdminController@login');
});

$router->group(['prefix' => 'planes'], function () use ($router) {
    $router->post('get-planes', 'PlanController@getPlanes');
    $router->post('update-plan', 'PlanController@updateStatusPlan');
    $router->post('add-plan', 'PlanController@addPlan');
});

$router->group(['prefix' => 'responsable'], function () use ($router) {
    $router->post('get-responsables', 'ResponsableController@getResponsable');
    $router->post('update-responsable', 'ResponsableController@updateResponsable');
    $router->post('add-responsable', 'ResponsableController@addResponsable');
});

$router->group(['prefix' => 'imagenes'], function () use ($router) {
    $router->post('/upload-image', 'ImageController@uploadImage');
    $router->post('/get-album', 'ImageController@getAlbum');
    $router->post('/create-album', 'ImageController@createAlbumHttp');
    $router->post('/delete-image', 'ImageController@deleteImage');
});

$router->group(['prefix' => 'empresa'], function () use ($router) {
    $router->post('/new-empresa', 'EmpresaController@newEmpresa');
    $router->post('/get-empresas', 'EmpresaController@getEmpresas');
    $router->post('/get-empresa', 'EmpresaController@getEmpresa');
    $router->post('/update-empresa', 'EmpresaController@updateEmpresa');
});

$router->group(['prefix' => 'suscripcion'], function () use ($router) {
    $router->post('/update-suscripcion', 'SuscripcionController@updateSuscripcion');
    $router->post('/add-suscripcion', 'SuscripcionController@addSuscripcion');
});


//APP
$router->group(['prefix' => 'app'], function () use ($router) {
    $router->post('/get-empresas', 'EmpresaController@APPgetEmpresas');
    $router->post('/get-empresa', 'EmpresaController@APPgetEmpresa');
    $router->post('/get-comentarios', 'EmpresaController@APPgetComentarios');
    $router->post('/send-comentario', 'EmpresaController@APPsendComentario');
    $router->post('/login', 'UsuarioController@APPlogin');

});


