<?php if ( ! defined( 'APPPATH' ) ) exit( 'Direct access to the individual scripts is not allowed!' );

require_once ALGODIR . 'create/Leeloo.php';

 /**
 * @package ThePasswordTools
 * @subpackage FortyTwo
 * @author Aziz Light
 */
class FortyTwo extends Leeloo
{
	
	// FIXME: I changed the structure of the config.ini file. This might affect the characters retrieval system.
	
	/**
	 * Array of characters that are eligible.
	 * Each element of the array can be a sequence of
	 * characters or a single character.
	 *
	 * @access protected
	 * @var array
	 */
	protected $chars;
	
	/**
	 * A copy of the chars array the way it was when a new
	 * instance of the class was created.
	 * This is necessary to be able to create several passwords.
	 *
	 * @access protected
	 * @var array
	 */
	protected $chars_backup;
	
	/**
	 * The length of the generated password
	 *
	 * @access protected
	 * @var int
	 */
	protected $length;
	
	/**
	 * This is the maximum number of times a character can be used in one password.
	 * This is only used if the length of the password that will
	 * be generated is greater than the number of eligible characters.
	 *
	 * @access private
	 * @var int
	 */
	private $_usage_count;
	
	/**
	 * Number of passwords created by the generate method.
	 *
	 * @access private
	 * @var int
	 */
	private $_password_count;
	
// ------------------------------------------------------------------------
	
	/**
	 * El Constructor!
	 *
	 * @access public
	 * @param int $length : The length of the password that will be generated.
	 * @author Aziz Light
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_password_count = 0;
		
		if ( func_num_args() == 1 )
		{
			$length = func_get_args();
			$length = ( int ) $length[0];
			if ( $length == 0 )
			{
				$this->length = self::$config['length'];
			}
			else
			{
				$this->length = $length;
			}
		}
		elseif ( ( $num_args = func_num_args() ) > 1 )
		{
			// FIXME: Throwing an exception might not be appropriate here...
			throw new Exception( 'The ' . __CLASS__ . ' constructor accepts only one argument! You passed ' . $num_args . '!!!');
		}
		else
		{
				$this->length = self::$config['length'];
		}
		
		// Calculate the maximum number of times a character can be repeated
		$this->chars = self::$config['chars'];
		$this->_usage_count = 1;
		while ( strlen( implode( '', $this->chars ) ) < $this->length )
		{
			for ( $i = 0, $max = ( count( $this->chars ) ); $i < $max ; $i++ )
			{ 
				$this->chars[$i] .= self::$config['chars'][$i];
			}
			$this->_usage_count++;
		}
		$this->chars_backup = $this->chars;
	} // End of __construct
	
// ------------------------------------------------------------------------
	
	/**
	 * Generate the password.
	 *
	 * @access public
	 * @param int $num : Optional. Lets the user create multiple passwords at the same time.
	 * @return void
	 * @author Aziz Light
	 * 
	 * @todo - add a parameter to generate several passwords at the same time.
	 */
	public function generate( $num = 1 )
	{
		// $num must be apositive int, if the user passes anything else $num's value will be reset to 1
		if ( ( int ) $num <= 0 )
			$num = 1;
		
		for ( $j = 0; $j < $num; $j++ )
		{
			// if at least one password was already created...
			if ( $this->_password_count > 0 )
			{
				// ...reset the chars array.
				unset( $this->chars );
				$this->chars = $this->chars_backup;
			}
			
			$this->passwords[$this->_password_count] = '';
			for ( $i = 1, $chars = '', $char = ''; $i <= $this->length; $i++ )
			{
				$chars = $this->setChars( $char );
				unset( $char );
			
				$char = $this->getChar( $chars );
				unset( $chars );
			
			
				if ( $this->_password_count == 0 )
					$this->password .= $char;
				$this->passwords[$this->_password_count] .= $char;
			}
			$this->_password_count++;
		}
		
		// When the first password is generated, return it,
		// When additional passwords are generated, return the array of passwords
		if ( $this->_password_count == 1 )
		{
			
			return $this->password;
		}
		else
		{
			return $this->passwords;
		}
	} // End of generate
	
// ------------------------------------------------------------------------
	
	/**
	 * Generate the eligible characters list for each character that will be
	 * generated. This is necessary to avoid character repetition.
	 * Also, removes previously generated characters from the eligible characters array (the instance variable)
	 *
	 * @access protected
	 * @param string $char : The last character generated. Used to exclude similar character from getting generated for the next "round"
	 * @return string : The new string of eligible characters
	 * @author Aziz Light
	 */
	protected function setChars( $char = '' )
	{
		$chars = '';
		if ( $char == '' )
		{
			for ( $i = 1, $max = ( ( int ) count( $this->chars ) - 1 ); $i <= $max; $i++ )
			{
				$chars .= $this->chars[$i];
			}
		}
		else
		{
			for ( $i = 0, $max = ( ( int ) count( $this->chars ) ); $i < $max; $i++ )
			{
				if ( strrpos( $this->chars[$i], $char ) === false )
				{
					$chars .= $this->chars[$i];
				}
			}
		}
		
		return $chars;
	} // End of setChars
	
// ------------------------------------------------------------------------
	
	/**
	 * Pick a random character from the eligible characters list.
	 *
	 * @access protected
	 * @param string $chars : The eligible characters list.
	 * @return string : The newly generated character.
	 * @author Aziz Light
	 */
	protected function getChar( $chars = '' )
	{
		if ( $chars == '' )
		{
			throw new Exception( 'You need to pass a string of characters so that one of them will be returned!' );
		}
		
		do
		{
			$char = $chars[mt_rand( 0, strlen( $chars ) - 1 )];
			$repeted_count = ( $this->_password_count == 0 ) ? substr_count( $this->password, $char ) : substr_count( $this->passwords[$this->_password_count], $char );
			if ( $repeted_count >= $this->_usage_count )
			{
				for ( $i = 0, $max = ( ( int ) count( $this->chars ) ); $i < $max; $i++ )
				{
					$this->chars[$i] = str_replace( $char, '', $this->chars[$i] );
				}
				$char = $chars[mt_rand( 0, strlen( $chars ) - 1 )];
			}
			else
			{
				break;
			}
			unset($repeted_count);
			$repeted_count = 0;
		} while ( true );
		
		return $char;
	} // End of getChar
	
} // End of FortyTwo class

/* End of file FortyTwo.php */
/* Location: ./algorithms/create/FortyTwo.php */