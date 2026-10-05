<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Auth::home');
$routes->get('setup', 'Auth::setup');
$routes->post('setup', 'Auth::createInitialUsers');
$routes->get('login/(:segment)', 'Auth::login/$1');
$routes->post('login/(:segment)', 'Auth::attempt/$1');
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->group('', ['filter' => 'role:admin,super_admin'], static function (RouteCollection $routes): void {
        $routes->get('dashboard', 'Dashboard::index');
        $routes->get('bookings', 'Bookings::index');
        $routes->get('bookings/new', 'Bookings::new');
        $routes->post('bookings', 'Bookings::create');
        $routes->get('bookings/(:num)/edit', 'Bookings::edit/$1');
        $routes->post('bookings/(:num)', 'Bookings::update/$1');
        $routes->post('bookings/(:num)/delete', 'Bookings::delete/$1');

        $routes->get('finance', 'Finance::index');
        $routes->get('finance/new', 'Finance::new');
        $routes->post('finance', 'Finance::create');
        $routes->post('finance/(:num)/delete', 'Finance::delete/$1');

        $routes->get('employees', 'Employees::index');
        $routes->get('employees/new', 'Employees::new');
        $routes->post('employees', 'Employees::create');
        $routes->get('employees/(:num)/edit', 'Employees::edit/$1');
        $routes->post('employees/(:num)', 'Employees::update/$1');
        $routes->post('employees/(:num)/delete', 'Employees::delete/$1');

        $routes->get('attendance', 'Attendance::index');
        $routes->get('attendance/new', 'Attendance::new');
        $routes->post('attendance', 'Attendance::save');
        $routes->post('attendance/(:num)/delete', 'Attendance::delete/$1');
    });

    $routes->group('my-attendance', ['filter' => 'role:staff'], static function (RouteCollection $routes): void {
        $routes->get('/', 'StaffAttendance::index');
        $routes->post('check-in', 'StaffAttendance::checkIn');
        $routes->post('check-out', 'StaffAttendance::checkOut');
    });

    $routes->group('users', ['filter' => 'role:super_admin'], static function (RouteCollection $routes): void {
        $routes->get('/', 'Users::index');
        $routes->get('new', 'Users::new');
        $routes->post('/', 'Users::create');
        $routes->post('(:num)/delete', 'Users::delete/$1');
    });
});
