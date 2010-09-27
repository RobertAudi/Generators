<?php if (! defined('APPPATH')) exit('Direct access to the individual scripts is not allowed!');

/**
 * @package ThePasswordTools
 * @subpackage Marvin
 * @author Aziz Light
 */
class Marvin extends Leeloo
{
	/**
	 * List of all valid arguments that can be passed
	 * when creating a new instance of the class.
	 * NOTE: It is assumed that all the arguments are integers!
	 *
	 * @todo Change the code so that I can remove this useless variable!
	 * @access protected
	 * @static
	 * @var array
	 */
	protected static $valid_args = array(
		'length',
		'numbers_count',
		'max_numbers_count',
		'symbols_count',
		'max_symbols_count',
		'chars',
		'deny_char_repetition',
	);
	
	/**
	 * This is a copy of the array of chars that will be used to generate the passwords.
	 * If character repetition is disabled, this array will change during the password generation
	 * and will be reset every time a new password will be generated.
	 *
	 * @access protected
	 * @var array
	 */
	protected $chars;
	
	/**
	 * Number of passwords created by the generate method.
	 *
	 * @access private
	 * @var int
	 */
	private $_password_count;
	
	/**
	 * These four constants are verbose versions of the deny_char_repetition allowed values.
	 */
	const ALLOW_DUPLICATES = 0;
	const NO_DUPLICATES    = 1;
	const TYPE_DUPLICATES  = 2;
	const SOS_DUPLICATES   = 3;
	
// ------------------------------------------------------------------------
	
	/**
	 * El Constructor!
	 *
	 * @access public
	 * @param int|array $args : Either the length of the password that will be generated or an array of arguments to setup the passwords generation.
	 * @author Aziz Light
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_password_count = 0;
		
		// validate the passed arguments
		$args = func_get_args();
		if (!empty($args))
			$this->validateArgs($args);
		unset($args);
	} // End of __construct
	
// ------------------------------------------------------------------------
	
	/**
	 * Generate passwords
	 *
	 * @param int $num : number of passwords to generate.
	 * @return void
	 * @author Aziz Light
	 */
	public function generate($num = 1)
	{
		// $num must be apositive int, if the user passes anything else $num's value will be reset to 1
		if ((int) $num <= 0)
			$num = 1;
		
		for ($j = 0; $j < $num; $j++)
		{
			// if at least one password was already created...
			if ($this->_password_count > 0)
			{
				// ...reset the chars array.
				unset($this->chars);
			}
			$this->chars = self::$config['chars'];
			
			$this->passwords[$this->_password_count] = '';
			for ($i = 1, $chars = $this->chars, $char = ''; $i <= self::$config['length']; $i++)
			{
				$chars = $this->setChars($char);
				unset($char);
				
				if ($chars === true)
					break;
				
				$char = $this->getChar($chars);
				unset($chars);
				
				if ($this->_password_count == 0)
					$this->password .= $char;
				$this->passwords[$this->_password_count] .= $char;
			}
			$this->_password_count++;
		}
		
		// When the first password is generated, return it,
		// When additional passwords are generated, return the array of passwords
		if ($this->_password_count == 1)
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
	 * Validate the arguments that were passed to the generate() method.
	 *
	 * @access protected
	 * @param string|int|array|void $args 
	 * @return void
	 * @author Aziz Light
	 */
	protected function validateArgs($args)
	{
		$a = $args;
		unset($args);
		$args = $a[0];
		
		if (is_string($args))
		{ // The current argument is a chars string.
			
			if (isset($a[1]) && $a[1] === true)
			{ // The chars array will be replaced
				self::$config['chars'] = $args;
			}
			else
			{ // The string will be appended to the chars array.
				self::$config['chars']['user'] = $args;
			}
		}
		elseif (($num_args = count($a)) == 1)
		{
			if (is_array($args))
			{
				// verify the validity of the passed arguments
				foreach (self::$valid_args as $arg)
				{
					if (array_key_exists($arg, $args))
					{
						// foc($arg);
						if (is_int($args[$arg]))
						{
							if ($arg == 'length' && $args[$arg] > 0)
							{ // Update the config array only if the user passed a valid value for the length.
								self::$config[$arg] = $args[$arg];
							}
							elseif ($arg == 'deny_char_repetition' && ((int) $args[$arg] >= 0 || (int) $args[$arg] <= 3))
							{
								self::$config[$arg] = $args[$arg];
							}
							elseif ($arg != 'length' && $arg != 'deny_char_repetition')
							{
								if ($args[$arg] <= -1)
								{
									// Any negative value that is less than or equal to -1 will be deleted because it won't be used.
									// -1 means "random". Any negative value that is less than -1 is irrelevent.
									unset(self::$config[$arg]);
								}
								elseif ($args[$arg] == 0 )
								{ // This is used to disable numbers or symbols
									if (substr($arg, 0, 4) == 'max_')
									{
										unset($arg);
										$arg = substr_replace($arg, '', 0, 4);
									}
									
									self::$config[$arg] = $args[$arg];
									$pieces = explode('_', $arg);
									unset(self::$config[$arg], self::$config['chars'][$pieces[0]], $pieces);
								}
								elseif ($args[$arg] > 0)
								{
									self::$config[$arg] = $args[$arg];
									if (substr($arg, 0, 4) == 'max_')
									{
										// max_* args take precedence over regular count args
										$b = substr_replace($arg, '', 0, 4);
										if (array_key_exists($b, self::$config))
											unset(self::$config[$b]);
										unset($b);
									}
								}
							}
						}
						elseif (is_array($args[$arg]))
						{ // The current argument is the chars array
							// The user can either append characters to the characters array or replace the characters array
							// By default, the new characters will be appended to the characters array.
							// To replace the characters array completely, the string "_override_" must be in the array.
							if (in_array('_override_', $args[$arg]))
							{
								// remove the _override_ keyword from the array
								unset($args[$arg][array_search('_override_', $args[$arg])]);
								
								// We will also make sure that the new characters array is organized by type
								self::$config['chars'] = Helpers::generateCharsArray(join('', $args[$arg]));
							}
							else
							{
								self::$config['chars'] = array_unshift(self::$config['chars'], $args[$arg]);
							}
						}
						elseif (is_string($args[$arg]))
						{ // The current argument is a string of chars that will be appended to the chars array
							self::$config['chars']['user'] = $args[$arg];
						}
					}
					// NOTE: Should I do something if the user passed a non-valid arg? ie: warning, log, etc.
				}
			}
			elseif ((int) $args > 0)
			{ // If an int is passed as argument, it is assumed that this int is the length of the passwords that will be generated.
				self::$config['length'] = (int) $args;
			}
		}
		elseif ($num_args > 1)
		{
			// FIXME: Throwing an exception might not be appropriate here...
			throw new Exception('The ' . __CLASS__ . ' constructor accepts only one argument! You passed ' . $num_args . '!!!');
		}
		
		// Finally we need to remove regular count args (ie: numbers_count) if its max_* counterpart exists.
		// Also any count arg with a value of -1 will be removed too.
		foreach (self::$config as $key => $value)
		{
			if (self::$config[$key] == -1)
			{
				unset(self::$config[$key]);
				continue;
			}
			
			if (substr($key, 0, 4) != 'max_')
				continue;
			
			$b = substr_replace($key, '', 0, 4);
			if (array_key_exists($b, self::$config))
				unset(self::$config[$b]);
			unset($b);
		}
		
		return;
	}
	
// ------------------------------------------------------------------------
	
	/**
	 * Generate the eligible characters list for each character that will be
	 * generated. This is necessary to avoid character repetition.
	 * Also, removes previously generated characters from the eligible characters array (the instance variable)
	 *
	 * @access protected
	 * @param array $chars : The array of chars in it latest state
	 * @param string $char : The last character generated. Used to exclude similar character from getting generated for the next "round"
	 * @return string : The new string of eligible characters
	 * @author Aziz Light
	 */
	protected function setChars($char = '')
	{
		// FIXME: This whole method will break if the user overrides the chars array with a string of chars or an array with only one key containing all the chars!
		
		$chars = '';
		if ($char == '')
		{
			$c = array_slice($this->chars, 1);
			foreach ($c as $key => $value)
			{
				$chars .= $value;
			}
			unset($c);
		}
		else
		{
			// Count the number of symbols and numbers so that it respects the count values of the config
			// FIXME: Not sure that this takes into account the characters added by the user (unless the whole chars array was overridden)
			$b = $this->getCounts();
			if (!empty($b))
			{
				foreach ($b as $t)
				{
					// FIXME: This only takes care of the max counts. The fixed counts option feature is not yet implemented because I haven't found a way to implement it without sacrificing the quality of the generated passwords.
					$d = explode('_', $t);
					$max = (substr($d[0], 0, 3) == 'max');
					$type = $max ? $d[1] : $d[0];
					
					$ca = Helpers::generateCharsArray($this->passwords[$this->_password_count]);
					$len = strlen($ca[$type]);
					
					if ($len >= self::$config[$t])
					{
						unset(self::$config['chars'][$type], $this->chars[$type], self::$config[$t]);
					}
				}
			}
			unset($b);
			
			foreach ($this->chars as $key => $value)
			{
				if (strrpos($value, $char) === false)
				{
					$chars .= $value;
				}
				elseif ((bool)self::$config['deny_char_repetition'] === true)
				{
					// remove a character if it's already in the password
					$this->chars[$key] = str_ireplace($char, '', $this->chars[$key]);
				}
			}
			
			if (empty($chars))
			{
				switch (self::$config['deny_char_repetition'])
				{
					case self::NO_DUPLICATES:
						return true;
						break;
					case self::TYPE_DUPLICATES:
						// Append the remaining characters to the password.
						$c = '';
						foreach ($this->chars as $key => $value)
						{
							if ($this->_password_count == 0)
								$this->password .= $value;
							$this->passwords[$this->_password_count] .= $value;
						}
						return true;
						break;
					case self::SOS_DUPLICATES:
						// Reset the characters array.
						$this->chars = self::$config['chars'];
						return $this->setChars();
						break;
				}
			}
		}
		
		// Avoid having the same letter twice in a row with a different case. ie: Oo or Ww
		return str_ireplace($char, '', $chars);
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
	protected function getChar($chars = '')
	{
		if ($chars == '')
		{
			throw new Exception('You need to pass a string of characters so that one of them will be returned!');
		}
		
		return $chars[mt_rand(0, strlen($chars) - 1)];
	} // End of getChar
	
// ------------------------------------------------------------------------
	
	/**
	 * This little method will return an array containing any *_count argument that is present in the config array.
	 *
	 * @access protected
	 * @return array
	 * @author Aziz Light
	 */
	protected function getCounts()
	{
		preg_match_all('/,?([a-z_]*_count),?/', join(',',array_keys(self::$config)), $m);
		return $m[1];
	} // End of getCounts
	
} // End of Marvin class

/* End of file Marvin.php */
/* Location: ./algorithms/create/Marvin.php */