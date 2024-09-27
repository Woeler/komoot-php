# Komoot PHP
NOTICE: Komoot has decided to add an invisible captcha to their login process. This renders this library unusable. I have sent them an email asking if we can get an alternative way to log in, but I have yet to receive a response.

This is a PHP library that can be used to interact with Komoot. It uses the same API the Komoot website uses. It is not officially supported by Komoot to integrate it in third party projects. I just needed a fancy way of interacting with Komoot for a personal project. That is why I created this package.

## Usage

### Authentication

You will need to use your Komoot account email address and password to log in.
```php
use Woeler\KomootPhp\Api\Komoot;

// Create the api object
$api = new Komoot('my-komoot-account@example.com', 'my-komoot-password');

// Execute the login
$api->login();

// ... You can now start making api calls
```

### Api calls

The endpoints I deemed important have dedicated methods. After logging in, you can simply use them.
```php
// Get a single tour
$api->getTour(123);

// Get a single tour as GPX data
$api->getTourGpx(123);

// Get all tours of your account
$api->getAllTours();

// Get a single user
$api->getUser(123);

// Get a single collection
$api->getCollection(123);
```

You may have a look in the `Komoot` class, various other endpoints are also available.

If you have an endpoint that you wish to call, but it does not have a dedicated method, you can use the `customRequest` method.
```php
$api->customRequest('https://api.komoot.de/v007/some-other-endpoint', [], 'GET');
```
