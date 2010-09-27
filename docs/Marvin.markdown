<!-- Clean this up... -->

### Character types
* `symbols`
* `lowerCaseLetters`
* `upperCaseLetters`
* `numbers`

If the user adds the value `_append_` in the characters array, then the characters in the array will get appended to the default characters array.

### deny_char_repetition

- **0 - ALLOW_DUPLICATES :** Allow duplicates    
	*There will probably be a lot of dupplicates.*
- **1 - NO_DUPLICATES    :** No duplicates (strict)    
	*The resulting password might be shorter than expected.*
- **2 - TYPE_DUPLICATES  :** No duplicates    
	*The last chars of the password will probably be of the same type (ie: uppercase, numbers, etc). Also, the length of passwords will be limited to the total number of available passwords.*
- **3 - SOS_DUPLICATES   :** Allow duplicates    
	*ONLY if necessary*


### Valid attributes
- `length`
- `numbers_count`
- `max_numbers_count`
- `symbols_count`
- `max_symbols_count`
- `chars`
- `deny_char_repetition`