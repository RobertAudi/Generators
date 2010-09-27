<?php if (! defined('APPPATH')) exit('Direct access to the individual scripts is not allowed!');

/* List of possible values:
 *  0  : Turn off all error reporting
 * -1 : Turn on all error reporting
 *
 * There are also predefined constants that you can use.
 * Here is the list: http://www.php.net/manual/en/errorfunc.constants.php
 */
error_reporting(-1);

// TODO: Divide the project into several classes: PasswordTools will contain the password enhancers and password strength checkers. StringGenerator will contain all the algorithms to generate passwords or strings.
// TODO: Workflow: Create a new StringGenerator instance -> select the type of string you want to generate

/* Change that to your own timezone to avoid getting error messages.
 * You can find a list of all the supported timezones here: http://php.net/manual/en/timezones.php
 */
date_default_timezone_set('Europe/Paris');

// ------------------------------------------------------------------------

/**
 * ThePasswordTools are a set of tools used to
 * create new passwords, test the strength of
 * existing ones and/or enchance them.
 *
 * @package ThePasswordTools
 * @subpackage PasswordTool
 * @author Aziz Light
 */
class PasswordTool
{
	/**
	 * El Constructor!
	 *
	 * @access public
	 * @author Aziz Light
	 */
	public function __construct()
	{
		
	} // End of __construct
	
} // End of PasswordTool class

// ------------------------------------------------------------------------

/**
 * Try to autoload one of the classes in one of the algorithms dirs
 *
 * @return void
 * @author Aziz Light
 */
function __autoload($algorithm)
{
	// List of algorithms dirs to check when trying to load an algorithms
	$algo_dirs = array(
		'create',
		'enhance',
		'test',
	);
	
	if ($algorithm == 'Helpers')
	{
		include_once APPPATH . 'Helpers.php';
		return;
	}
	
	$valid_dir = false;
	foreach ($algo_dirs as $dir)
	{
		if (is_readable(ALGODIR . $dir . '/' . $algorithm . '.php'))
		{
			require_once ALGODIR . $dir . '/' . $algorithm . '.php';
			return;
		}
	}
	
	/* NOTE: eval sucks, eval is evil and eval will probably make your life miserable.
	 * However, it's impossible to throw an exception from within an __autoload() function in PHP 5.2
	 * The code below is an easy fix.
	 * I can probably make the code below less sucky if instead I use an exception error handler to convert
	 * errors to exceptions, but I think that might be a little bit overkill in the scope of this application.
	 * 
	 * More info for the stubborn people: http://php.net/manual/en/class.errorexception.php
	 */
	eval("class $algorithm {}");
	throw new Exception('Unable to load the ' . $algorithm . ' class. ' . $algorithm . '.php doesn\'t exist!');
} // End of __autoload

/* End of file passwordTool.php */
/* Location: ./passwordTool.php */