<?php namespace Config;
      use App\Controllers\RestResume;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('home');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'AuthController::login');

$routes->get('/chat', 'PruebaController::chat');
$routes->get('/datatable/(:segment)', 'PruebaController::datatable/$1');

$routes->post('/table', 'PruebaController::table');
$routes->get('/table', 'PruebaController::table');

$routes->post('prueba', 'PruebaController::index');

$routes->get('password', 'PasswordController::index');
$routes->post('password/updated', 'PasswordController::updated');

$routes->group('dashboard', function ($routes){
	$routes->get('', 'DashboardController::index');

	// Rutas para las cotizaciones
	$routes->group('cotizaciones', function($routes){
		$routes->get('', 'QuotesController::index');
		$routes->get('data', 'QuotesController::data');
		$routes->get('new', 'QuotesController::new');
		$routes->get('editar/(:num)', 'QuotesController::editar/$1');
		$routes->get('invoice/(:num)', 'QuotesController::invoice/$1');
	});

	$routes->group('clientes', function($routes){
		$routes->get('', 'CustomersController::index');
		$routes->get('data', 'CustomersController::data');
		$routes->post('created', 'CustomersController::created');
		$routes->post('edit', 'CustomersController::edit');
		$routes->post('delete', 'CustomersController::delete');

		$routes->group('sucursales', function($routes){
			$routes->get('(:num)', 'BranchController::index/$1');
			$routes->get('data', 'BranchController::data');
			$routes->post('created', 'BranchController::created');
			$routes->post('edit', 'BranchController::edit');
		});
	});

	$routes->get('report', 'ReportsController::index');
	
	$routes->group('reports', function($routes){
		$routes->post('data_index', 'ReportsController::dataIndex');

		$routes->get('customers', 'ReportsController::customers');
		$routes->get('sellers', 'ReportsController::sellers');
		$routes->get('data/(:segment)', 'ReportsController::data/$1');
	});

});

$routes->group('invoices', function($routes){
	$routes->post('created', 'InvoiceController::created');
	$routes->post('edit', 'InvoiceController::edit');
	$routes->post('decline', 'InvoiceController::decline');
	$routes->get('download/(:num)', 'InvoiceController::download/$1');

	$routes->post('load/order', "InvoiceController::load_order");
});

$routes->group('data', function($routes){
	$routes->post('products', 'QuotesController::products');
});

$routes->get('/login', 'AuthController::login');
// $routes->get('/register', 'AuthController::register');
// $routes->post('/create', 'AuthController::create');
$routes->get('/reset_password', 'AuthController::resetPassword');
$routes->post('/forgot_password', 'AuthController::forgotPassword');
$routes->post('/validation', 'AuthController::validation');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/about', 'DashboardController::about');
$routes->get('/perfile', 'UserController::perfile');
$routes->post('/update_photo', 'UserController::updatePhoto');
$routes->post('/update_user', 'UserController::updateUser');
$routes->post('/config/(:segment)', 'ConfigController::index/$1');
$routes->get('/config/(:segment)', 'ConfigController::index/$1');
$routes->post('/table/(:segment)', 'TableController::index/$1');
$routes->get('/table/(:segment)', 'TableController::index/$1');
$routes->post('/table/(:segment)/(:segment)', 'TableController::detail/$1/$2');
$routes->get('/table/(:segment)/(:segment)', 'TableController::detail/$1/$2');

$routes->group('load', function($routes){
	$routes->get('products', 'LoadsController::products');
	$routes->get('customers', 'LoadsController::customers');
	$routes->get('env', 'LoadsController::env');
	$routes->get('updated/invoices', 'LoadsController::updatedInvoices');
});

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
