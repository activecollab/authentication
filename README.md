# Authentication Library

[![Build Status](https://travis-ci.org/activecollab/authentication.svg?branch=master)](https://travis-ci.org/activecollab/authentication)

Authentication library builds on top of `activecollab/user` package. There are three types of visitors that we recognize:

1. Unidentified visitors - Visitors that we know nothing bout,
1. Identified visitors - People that we identified when they provided their email address,
1. Users with accounts - People with actual accounts in our application.

Only users with accounts in our application can be authenticated.

## Working with Passwords

### Hashing Passwords

Passwords can be hashed using one of the three mechanisms:

1. PHP's built in `password_*` functions. This is default and recommended method
1. Using PBKDF2
1. Using SHA1

Later two are there for compatibility resons only, so you can transition your hashed passwords to PHP's password management system if you have not done that already. Password manager's `needsRehash()` method will always recommend rehashing for PBKDF2 and SHA1 hashed passwords.

Example:

```php
$manager = new PasswordManager('global salt, if needed');

$hash = $manager->hash('easy to remember, hard to guess');

if ($manager->verify('easy to remember, hard to guess', $hash, PasswordManagerInterface::HASHED_WITH_PHP)) {
    print "All good\n";
} else {
    print "Not good\n";
}
```

### Password Policy

All passwords are validated against password policies. By default, policy will accept any non-empty string:

```php
(new PasswordStrengthValidator())->isPasswordValid('weak', new PasswordPolicy()); // Will return TRUE
```

Policy can enforce following rules:

1. Password is longer than N characters
1. Password contains at least one number
1. Password contains mixed case (uppercase and lowercase) letters
1. Password contains at least one of the following symbols: `,.;:!$\%^&~@#*`

Here's an example where all rules are enforced:

```php
// Weak password, not accepted
(new PasswordStrengthValidator())->isPasswordValid('weak', new PasswordPolicy(32, true, true, true));
 
// Strong password, accepted
(new PasswordStrengthValidator())->isPasswordValid('BhkXuemYY#WMdU;QQd4QpXpcEjbw2XHP', new PasswordPolicy(32, true, true, true));
```

## Generating Passwords

Password strength validator can also be used to prepare new passwords that meed the requirements of provided policies:

```php
$validator = new PasswordStrengthValidator();
$policy = new PasswordPolicy(32, true, true, true);

// Prepare 32 characters long password that mixes case, numbers and symbols
$password = $validator->generateValidPassword(32, $policy); 
```

Password generator uses letters and numbers by default, unless symbols are required by the provided password policy.

Note that generator may throw an exeception if it fails to prepare a password in 10000 tries.

## To Do

1. Consider adding previously used passwords repository, so library can enforce no-repeat policy for passwords
