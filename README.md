# A Laravel Package for easily retrieving a list of active users and guests that are currently online.

This is a small, simple little package for seeing who's currently online.

## Installation

Install via composer with:

```
composer require joshrainwater/active-users
```

## Setup

### Sessions and Database

For now, this package only supports the 'database' type for session storage. Open your .env file and change the session driver to database:

```
SESSION_DRIVER=database
```

and also make sure to publish the default session table with:

```
php artisan session:table

php artisan migrate
```

### Providers and Aliases

In order to use the Active class, reference the full vendor namespace, or just import the class at the top of the file that you want to use it in. Eg:

```
\Rainwater\Active\Active::users();
```

or

```
use Rainwater\Active\Active;
Active::users();
```

This package also includes a provider and alias to make things easier. To set those up properly, open `config/app.php` and add this to your providers array:

```
Rainwater\Active\ActiveServiceProvider::class
```

and this to your aliases array:

```
'Active' => Rainwater\Active\ActiveFacade::class
```

## Usage

### Grabbing Most Recent Activities

Import the Active facade at the top and then do a simple query.

```
// Import at the top
use Active;


// Find latest users
$users = Active::users()->get();

// Loop through and echo user's name
foreach ($users as $activity) {
    echo $activity->user->name . '<br>';
}
```

By default, the 'users' method will return users that have been active in the past 5 minutes. If you want to display a different timespan, use the following functions:

```
$users = Active::users(3)->get();   				// Last 3 minutes
$users = Active::usersWithinSeconds(30)->get();  	// Get active users within the last 30 seconds
$users = Active::usersWithinMinutes(10)->get();  	// Get active users within the last 10 minutes
$users = Active::usersWithinHours(1)->get();     	// Get active users within the last 1 hour
```

The functions listed above all return Eloquent Queries, so you can do anything with the results that would do with any other model:

```
$numberOfUsers = Active::users()->count();        // Count the number of active users
```

### Sorting Methods

There are a couple convenience methods for sorting results, as well:

```
$users = Active::users()->mostRecent()->get();   // Get active users and sort them by most recent
$users = Active::users()->orderByUsers('email')->get(); // Sort by the email column on the users table.
```

### Guests

You can also view online guests with all the same methods as users above:

```
$guests = Active::guests(1)->get();   				// Last 1 minute
$guests = Active::guests(3)->get();   				// Last 3 minutes
$guests = Active::guestsWithinSeconds(30)->get();  	// Get active guests within the last 30 seconds
$guests = Active::guestsWithinMinutes(10)->get();  	// Get active guests within the last 10 minutes
$guests = Active::guestsWithinHours(1)->get();     	// Get active guests within the last 1 hour

$numberOfGuests = Active::guests()->count();      // Count the number of active guests
```

Many thanks to [thomastkim/laravel-online-users](https://github.com/thomastkim/laravel-online-users) on which this is very heavily based.

## License

This package is free software distributed under the terms of the MIT license.
