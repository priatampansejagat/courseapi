<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


define('AS_MENTOR', 'd730bb9677663feb30d4c4e9d273c7c9c713e4d5b8eebf9218a2f587dd7c5d9b');
define('AS_ADMIN', 'fdd38312da2d5ddc4b90a49aaa2bcf52d586572db5ce37cb2630799476aa13e4');
define('AS_STUDENT', '83bbe0cd25d8cc4b8c076497a57d4b6452e84946b9042dc7983a7806a1f636cf');

/* STATUS */
define('INACTIVE', 0);
define('ACTIVE', 1); //true or active 
define('CONFIRMED', 2); //Confirmed and active
define('DELETED', 3); // deleted or declined
/* END STATUS */

define('DIR_COURSE', './uploads/courses/');
define('DIR_COURSE_PUBLIC', 'uploads/courses/');

define('DIR_MEMBER', './uploads/members/');
define('DIR_MEMBER_PUBLIC', 'uploads/members/');

define('DIR_EVENT', './uploads/events/');
define('DIR_EVENT_PUBLIC', 'uploads/events/');


/* ZOOM */
define('ZOOM_API_KEY', 'I5xntiszQau07ujlkGpaGQ');
define('ZOOM_API_SECRET', '0EnBSIBPyTHM38JAj8mrTWmMXpZ6GUUBjv8Z');
define('ZOOM_IM_CHAT_HISTORY', 'eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJCcl9SNndqNVEwdVhULUl6ekFmRHFBIn0.1fUnm3me5Z5uR2lzlmBOP249LF_pOF2dPhvSEszUQFI');

define('ZOOM_OAUTH_CLIENT_ID', '9i_7ZM7yQxacQvHVNu2nAw');
define('ZOOM_OAUTH_CLIENT_SECRET', 'EzlFGvffkSZrOpIYT4ULVug7qluC7Uwl');
define('ZOOM_OAUTH_REDIRECT_URI', 'http://temporaryapi.rumahpeneleh.or.id/zoomoauthtoken');
// define('ZOOM_OAUTH_REDIRECT_URI', 'http://api.research-academy.org/zoomoauthtoken');

