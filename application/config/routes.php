<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/


// $route['contohpath'] = 'registered/Basecontroller/index';
// $route['visitor'] = 'visitor/Basecontroller/index';
// $route['404_override'] = '';
// $route['translate_uri_dashes'] = FALSE;

$route['default_controller'] = 'welcome';

// account
$route['regissubmit'] = 'RegisterController/register';
$route['login'] = 'RegisterController/login';
$route['regisupdate'] = 'RegisterController/update'; 
$route['regisupdate_text'] = 'RegisterController/update_text'; 

$route['forgpass'] = 'RegisterController/index'; // lupa password

// datatable
$route['datatable'] = 'DatatableController/get_data'; 

// course
$route['createcourse'] = 'CourseController/create'; 
$route['deletecourse'] = 'CourseController/delete'; 
$route['regiscourse'] = 'CourseController/registration'; 
$route['confirm_register'] = 'CourseController/registration_confirm'; 
$route['decline_register'] = 'CourseController/registration_decline'; 
$route['createchapter'] = 'CourseController/create_chapter'; 
$route['createchapter_vid'] = 'CourseController/video_chapter'; 
$route['cert_upload'] = 'CourseController/cert_upload'; 
$route['cert_delete'] = 'CourseController/cert_delete'; 

// payment
$route['payconfirm'] = 'PaymentController/confirm'; 
$route['payconfirm_file'] = 'PaymentController/confirm_file'; 

// Event
$route['createevent'] = 'EventController/create_event'; 
$route['add_course_event'] = 'EventController/add_course_event'; 
$route['add_cover_event'] = 'EventController/add_cover_event'; 
$route['delete_cover_event'] = 'EventController/cover_delete'; 
$route['delete_bridge_event_course'] = 'EventController/bridge_delete'; 
$route['regisevent'] = 'EventController/registration'; 
$route['regisevent_confirm'] = 'EventController/registration_confirm'; 
$route['regisevent_decline'] = 'EventController/registration_decline'; 
$route['create_gallery_event'] = 'EventController/create_gallery_event'; 
$route['upload_gallery_event'] = 'EventController/upload_gallery_event'; 
$route['delete_gallery_event'] = 'EventController/delete_gallery_event'; 


