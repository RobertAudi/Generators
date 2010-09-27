<?php if (! defined('APPPATH')) exit('Direct access to the individual scripts is not allowed!');

/**
 * Class containing static helper methods
 *
 * @package PasswordTools
 * @author Aziz Light
 */
class Helpers
{
	/**
	 * Count the letters occurrences within a string.
	 *
	 * @access public
	 * @static
	 * @param string $string : The string that will be analyzed.
	 * @param string $onlyDuplicates : If set to true, only letters that occur more than once will be counted. If all the letters only occur once, then a message saying so will be returned.
	 * @return array|string.
	 * @author Aziz Light
	 */
	public static function count_letters_occurrences($string, $onlyDuplicates = false)
	{
		$a = array();
		$c = strlen($string);
		for ($i=0; $i < $c; $i++)
		{
			$a[$string[$i]] = isset($a[$string[$i]]) ? $a[$string[$i]] + 1 : 1;
		}
		
		if ($onlyDuplicates === true)
		{
			foreach ($a as $key => $value)
			{
				if ((int) $a[$key] == 1)
					unset($a[$key]);
			}
			if (empty($a))
				$a= "No Duplicates";
		}
		
		return $a;
	} // End of count_letters_occurrences
	
// ------------------------------------------------------------------------
	
	/**
	 * Count the number of duplicate letters in a string
	 *
	 * @access public
	 * @static
	 * @param string $string : The string that will be analyzed.
	 * @return int
	 * @author Aziz Light
	 */
	public static function count_duplicates($string)
	{
		return count(self::count_letters_occurrences($string, true));
	} // End of count_duplicates
	
// ------------------------------------------------------------------------
	
	/**
	 * Transforms a char string in a char array containing arrays for each character type.
	 *
	 * @access public
	 * @static
	 * @param string $charString : A string of characters.
	 * @return array
	 * @author Aziz Light
	 */
	public static function generateCharsArray($charString)
	{
		$b = preg_match_all('/(?P<symbols>[~_!\?@#\$%&\*\^\(\)\[\]{}\-=\+\/])|(?P<upperLetters>[a-z])|(?P<loweLetters>[A-Z])|(?P<numbers>[0-9])/', $charString, $matches);
		unset($matches[0]);
		
		foreach ($matches as $key => $value)
		{
			if (preg_match('/\d+/', $key))
			{
				unset($matches[$key]);
				continue;
			}
			
			foreach ($matches[$key] as $k => $v)
			{
				if (empty($v))
				{
					unset($matches[$key][$k]);
				}
			}
			
			$matches[$key] = implode('', $matches[$key]);
		}
		
		return $matches;
	} // End of generateCharsArray
	
} // End of Helpers

/* End of file helpers.php */
/* Location: ./helpers.php */