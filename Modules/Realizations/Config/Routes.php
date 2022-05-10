<?php

if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}


$routes->group(lang('Realizations.realization_public_url'), ['namespace' => 'App\Modules\Realizations\Controllers'], function ($routes) {
	$routes->get('(:any)/(:any)', 'Realizations::view_item/$1/$2', ['as' => 'realizations_item']);
});

$routes->get(lang('Realizations.realizations_public_url'), 'Realizations::index', ['namespace' => 'App\Modules\Realizations\Controllers', 'as' => 'realizations']);
$routes->group(lang('Realizations.realizations_public_url'), ['namespace' => 'App\Modules\Realizations\Controllers'], function ($routes) {
	$routes->get('(:any)', 'Realizations::category/$1', ['as' => 'realizations_category']);
});


//API
$routes->group('api', ['namespace' => 'App\Modules\Realizations\Controllers'], function ($routes) {
	$routes->post('getRealizations', 'Realizations::getRealizations', ['as' => 'api_realizations']);
	$routes->post('getRealizationsCategory', 'Realizations::getRealizationsCategory', ['as' => 'api_realizations_category']);
	$routes->post('getRealization', 'Realizations::getRealization', ['as' => 'api_realization']);
});

// ADMIN
$routes->group('admin', ['namespace' => 'App\Modules\Realizations\Controllers\Admin', 'filter' => 'isadmin'], function ($routes) {
	$routes->get('realizations', 'Realizations::index', ['as' => 'admin_realizations']);
	$routes->group('realizations', ['namespace' => 'App\Modules\Realizations\Controllers\Admin'], function ($routes) {
		$routes->get('create', 'Realizations::create_item', ['as' => 'admin_realizations_item_create']);
		$routes->get('(:num)', 'Realizations::view_item/$1', ['as' => 'admin_realizations_item']);
		$routes->get('(:num)/edit', 'Realizations::edit_item/$1', ['as' => 'admin_realizations_item_edit']);
		$routes->get('(:num)/remove', 'Realizations::remove_item/$1', ['as' => 'admin_realizations_item_remove']);

		$routes->post('createItem', 'Realizations::createItem', ['as' => 'admin_realizations_createItem']);
		$routes->post('editItem', 'Realizations::editItem', ['as' => 'admin_realizations_editItem']);
		$routes->post('removeItem', 'Realizations::removeItem', ['as' => 'admin_realizations_removeItem']);

		$routes->get('(:num)/gallery', 'Realizations::gallery/$1', ['as' => 'admin_realizations_item_gallery']);
		$routes->get('(:num)/image/create', 'Realizations::create_image/$1', ['as' => 'admin_realizations_image_create']);
		$routes->get('(:num)/image/(:num)', 'Realizations::view_image/$1/$2', ['as' => 'admin_realizations_image_view']);
		$routes->get('(:num)/image/(:num)/edit', 'Realizations::edit_image/$1/$2', ['as' => 'admin_realizations_image_edit']);
		$routes->get('(:num)/image/(:num)/remove', 'Realizations::remove_image/$1/$2', ['as' => 'admin_realizations_image_remove']);

		$routes->post('createImage', 'Realizations::createImage', ['as' => 'admin_realizations_createImage']);
		$routes->post('editImage', 'Realizations::editImage', ['as' => 'admin_realizations_editImage']);
		$routes->post('removeImage', 'Realizations::removeImage', ['as' => 'admin_realizations_removeImage']);

		$routes->get('categories', 'Realizations::categories', ['as' => 'admin_realizations_categories']);
		$routes->get('category/create', 'Realizations::create_category', ['as' => 'admin_realizations_category_create']);
		$routes->get('category/(:num)', 'Realizations::view_category/$1', ['as' => 'admin_realizations_category_view']);
		$routes->get('category/(:num)/edit', 'Realizations::edit_category/$1', ['as' => 'admin_realizations_category_edit']);
		$routes->get('category/(:num)/remove', 'Realizations::remove_category/$1', ['as' => 'admin_realizations_category_remove']);

		$routes->post('createCategory', 'Realizations::createCategory', ['as' => 'admin_realizations_createCategory']);
		$routes->post('editCategory', 'Realizations::editCategory', ['as' => 'admin_realizations_editCategory']);
		$routes->post('removeCategory', 'Realizations::removeCategory', ['as' => 'admin_realizations_removeCategory']);
	});
});
