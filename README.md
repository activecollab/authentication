# Authentication Library

[![Build Status](https://travis-ci.org/activecollab/authentication.svg?branch=master)](https://travis-ci.org/activecollab/authentication)

Table of Contents:

* [Who are Authenticated Users](#who-are-authenticated-users)
* [Working with Passwords](#working-with-passwords)
  * [Hashing and Validating Passwords](#hashing-and-validating-passwords)
  * [Password Policy](#password-policy)
  * [Generating Random Passwords](#generating-random-passwords)
* [Login Policy](#login-policy)
* [To Do](#to-do)

## Transports

During authentication and authorization steps, this library returns transport objects that encapsulate all auth elements that are relevant for the given step in the process:

1. `AuthenticationTransportInterface` is returned on initial authentication. It can be empty, when request does not bear any user ID embedded (token, or session), or it can contain information about authenticated user, way of authentication, used adapter etc, when system finds valid ID in the request. 
1. `AuthroizationTransportInterface` is returned when user provides their credentials to the authorizer.
1. `DeauthenticationTransportInterface` - is returned when user requests to be logged out of the system.

Authentication and authorization transports can be applied to responses (and requests) to sign them with proper identification data (set or extend user session cookie for example):

```php
if (!$transport->isApplied()) {
    list ($request, $response) = $transport->applyTo($request, $response);
}
```
    
## Who are Authenticated Users?

Authentication library builds on top of `activecollab/user` package. There are three types of visitors that we recognize:

1. Unidentified visitors - Visitors that we know nothing bout,
1. Identified visitors - People that we identified when they provided their email address,
1. Users with accounts - People with actual accounts in our application.

Only users with accounts in our application can be authenticated.

### Accessing Users

Write a class that implements `ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface` when integrating this package. Implementation of this interface will let the library find users by their ID and username:

```php
<?php

namespace MyApp;

class MyUsersRepository implements \ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findById($user_id)
    {
        // Find and return user by ID
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username)
    {
        // Find and return user by username (can be an email address as well)
    }
}
```

## Working with Passwords

### Hashing and Validating Passwords

Passwords can be hashed using one of the three mechanisms:

1. PHP's built in `password_*` functions. This is default and recommended method
1. Using PBKDF2
1. Using SHA1

Later two are there for compatibility reasons only, so you can transition your hashed passwords to PHP's password management system if you have not done that already. Password manager's `needsRehash()` method will always recommend rehashing for PBKDF2 and SHA1 hashed passwords.

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

Library offers a way to check if password needs to be rehashed, usually after you successfully checked if password that user provided is correct one:

```php
$manager = new PasswordManager('global salt, if needed');

if ($manager->verify($user_provided_password, $hash_from_storage, PasswordManagerInterface::HASHED_WITH_PHP)) {
    if ($manager->needsRehash($hash_from_storage, PasswordManagerInterface::HASHED_WITH_PHP)) {
        // Update hash in our data storage
    }
    
    // Proceed with user authentication
} else {
    print "Invalid password\n";
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

Password policy implements `\JsonSerializable` interface, and can be safely encoded to JSON using `json_encode()`.

### Generating Random Passwords

Password strength validator can also be used to prepare new passwords that meed the requirements of provided policies:

```php
$validator = new PasswordStrengthValidator();
$policy = new PasswordPolicy(32, true, true, true);

// Prepare 32 characters long password that mixes case, numbers and symbols
$password = $validator->generateValidPassword(32, $policy); 
```

Password generator uses letters and numbers by default, unless symbols are required by the provided password policy.

Note that generator may throw an exeception if it fails to prepare a password in 10000 tries.

## Login Policy

Login Policy is used by adapters to publish their log in page settings. These settings include:

1. Format of username fields. Supported values are `email` and `username`,
1. Whether "Remember Me" option for extended sessions is supported by the adapter,
1. Whether passwords can be changed by the user,
1. Log in, log out, password reset and update profile URL-s. These URL-s are used by adapters which implement off-site authentication, so application that uses these adapters can redirect users to correct pages.

This example shows how different settings can be configured using setter calls. All these settings can also be set when you construct new `LoginPolicy` instance:

```php
$login_policy = (new LoginPolicy())
    ->setUsernameFormat(LoginPolicyInterface::USERNAME_FORMAT_EMAIL)
    ->setRememberExtendsSession(true)
    ->setIsPasswordChangeEnabled(true)
    ->setIsPasswordRecoveryEnabled(true)
    ->setExternalLoginUrl('http://idp.example.com/login')
    ->setExternalLogoutUrl('http://idp.example.com/logout')
    ->setExternalChangePasswordUrl('http://idp.example.com/change-password')
    ->setExternalUpdateProfileUrl('http://idp.example.com/update-profile');
```

Login policy implements `\JsonSerializable` interface, and can be safely encoded to JSON using `json_encode()`.

## To Do

1. Consider adding previously used passwords repository, so library can enforce no-repeat policy for passwords
1. Remove compat `ActiveCollab\Authentication\Adapter\BrowserSession` and `ActiveCollab\Authentication\Adapter\TokenBearer` classes once all apps that use this package are updated to use new classes
