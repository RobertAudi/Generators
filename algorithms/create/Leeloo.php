<?php if ( ! defined( 'APPPATH' ) ) exit( 'Direct access to the individual scripts is not allowed!' );

/**
 * @package ThePasswordTools
 * @subpackage Leeloo
 * @author Aziz Light
 */
abstract class Leeloo implements Plavalagoona
{
	/**
	 * The content of the configuration file will be stored in this variable.
	 *
	 * @access protected
	 * @static
	 * @var array
	 */
	protected static $config;
	
	/**
	 * The generated password.
	 * Only the first generated password will be accessible via this property.
	 * Any additional generated password will be stored in the password property.
	 *
	 * @var string
	 */
	public $password;
	
	/**
	 * Array of paswords.
	 * All the generated passwords will be stored in this array.
	 *
	 * @var array
	 */
	public $passwords;
	
	/**
	 * El Constructor!
	 * Loads the configuration file.
	 *
	 * @access public
	 * @author Aziz Light
	 */
	public function __construct()
	{
		self::$config = array();
		self::getConfig( get_class( $this ) );
		
		$this->password = '';
		$this->passwords = array();
	} // End of __construct
	
	public function generate() {}
	
	/**
	 * Parse the configuration file to retrieve the appropriate
	 * configuration variables depending on the algorithm used.
	 *
	 * @access protected
	 * @param string $algorithm : name of the algorithm used. Must match the name of the algorithm specific section in the config file.
	 * @return void
	 * @author Aziz Light
	 */
	protected static function getConfig( $algorithm )
	{
		$config = parse_ini_file( APPPATH . 'config.ini', true );
		self::$config = array_merge( $config['General'], $config[$algorithm] );
		self::$config['chars'] = $config['Chars'];
		return;
	} // End of getConfig
	
} // End of Leeloo class

/**
 * @package ThePasswordTools
 * @author Aziz Light
 */
interface Plavalagoona
{
	public function generate();
} // End of Plavalagoona interface

/* End of file Leeloo.php */
/* Location: ./algorithms/create/Leeloo.php */