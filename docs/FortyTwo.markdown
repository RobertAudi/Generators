## FortyTwo

FortyTwo is the default algorithm for generating passwords. It only takes one optional argument: an int to represent the length of the password(s) that will be generated.

    $password = new FortyTwo( 42 );

To generate passwords, you need to use the `generate` method:

    $p = $password->generate();

The first password will always be stored in the `$password` instance variable. Also, if you only generate one password, the `generate` method will return that password:

    echo $password->password;
    echo $p;

Both of the statements above will print the newly generated password to the screen.

### Generating multiple passwords

To generate multiple passwords, you have two choices:

1. **Pass an argument to the `generate` method.**

	The `generate` method takes 1 argument: the number of passwords you want to generate:

	    $q = $password->generate( 42 );

	The statement above will generate 42 passwords. When multiple passwords are generated, the `generate` method returns an array containing all the passwords. Additionally, the passwords are stored in the `$passwords` instance variable.

	    print_r( $password->passwords );
	    print_r( $q );

	Both of the statements above will print the content of the array of passwords to the screen. Note that the first password generated is also available from the `$password` instance variable. Another thing to note is that all the passwords generated using this method will have the same length.

2. **Create a new instance of the FortyTwo class.**
	
	This method is less “lightweight” but the user can generate multiple passwords of the different lengths.