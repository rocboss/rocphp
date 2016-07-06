# The document is being perfected.(to be continued)

----------


# What is ROCPHP?

ROCPHP is a fast, simple, extensible framework for PHP which is the concrete realization of flight.

# Requirements

 - `PHP 5.4` or greater.
 - `pdo_mysql` extension
 - `Redis` extension
 - Url rewrite


# License

ROCPHP is released under the [MIT](https://github.com/rocboss/rocphp/blob/master/LICENSE) license.

# Installation

1\. Download the files.

You can [download](https://github.com/rocboss/rocphp/archive/master.zip) rocphp directly and extract them to your web directory.

2\. Specify the site root directory.

By default, the WEB root directory should be directed to the `app/web/`.

When you need to create multiple applications, create a new directory (such as `admin`) under the `app/` directory, and then direct the WEB root directory to the folder.

3\. Configure your webserver.

For *Apache*, edit your `.htaccess` file with the following:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Note**: This file has been provided.

For *Nginx*, add the following to your server declaration:

```
server {
    location / {
        try_files $uri $uri/ /index.php;
    }
}
```
# MVCS

The design pattern of rocphp is MVCS, that is, model, view, controller, and service layer.

The first demo, it only takes you three minutes

 1\. Add a rule into `app/web/_router.php`, the content is

```php
['/demo', 'web\Index:demo'],
```


 2\. Add a function into `app/controller/web/IndexController.php`, the content is

```php
public static function demo()
{
    echo 'This a demo.';
}
```

3\. Enter `http://youdomain/demo` into your browser address bar.Then, you will see "This a demo." on the screen.

# DBEngine

DBEngine is a simple but powerful database toolkit in Rocphp. It is a fluent SQL builder, database abstraction layer, cache manager, query statistics generator, and micro-ORM all rolled into a single class file.

## Building SQL

```php
// Include the library
include '/path/to/DBEngine.php';

// Declare the class instance
$db = new DBEngine();

// Select a table
$db->from('user')

// Build a select query
$db->select();

// Display the SQL
echo $db->sql();
```

Output:

```sql
SELECT * FROM user
```

### Method Chaining

DBEngine allows you to chain methods together, so you can instead do:

```php
echo $db->from('user')->select()->sql();
```

### Where Conditions

To add where conditions to your query, use the `where` function.

```php
echo $db->from('user')
    ->where('id', 123)
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE id = 123
```

You can call where multiple times to add multiple conditions.

```php
echo $db->from('user')
    ->where('id', 123)
    ->where('name', 'bob')
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE id = 123 AND name = 'bob'
```

You can also pass an array to the where function. The following would produce the same output.

```php
$where = ['id' => 123, 'name' => 'bob'];
```

```php
echo $db->from('user')
    ->where($where)
    ->select()
    ->sql();
```

You can even pass in a string literal.

```php
echo $db->from('user')
    ->where('id = 99')
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE id = 99
```

### Custom Operators

The default operator for where queries is `=`. You can use different operators by placing them after the field declaration.

```php
echo $db->from('user')
    ->where('id >', 123)
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE id > 123;
```

### OR Queries

By default where conditions are joined together by `AND` keywords. To use OR instead, simply place a `|` delimiter before the field name.

```php
echo $db->from('user')
    ->where('id <', 10)
    ->where('|id >', 20)
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE id < 10 OR id > 20
```

### LIKE Queries

To build a LIKE query you can use the special `%` operator.

```php
echo $db->from('user')
    ->where('name %', '%bob%')
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE name LIKE '%bob%'
```

To build a NOT LIKE query, add a `!` before the `%` operator.

```php
echo $db->from('user')
    ->where('name !%', '%bob%')
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE name NOT LIKE '%bob%'
```

### IN Queries

To use an IN statement in your where condition, use the special `@` operator
and pass in an array of values.

```php
echo $db->from('user')
    ->where('id @', array(10, 20, 30))
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE id IN (10, 20, 30)
```

To build a NOT IN query, add a `!` before the `@` operator.

```php
echo $db->from('user')
    ->where('id !@', array(10, 20, 30))
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user WHERE id NOT IN (10, 20, 30)
```

### Selecting Fields

To select specific fields, pass an array in to the `select` function.

```php
echo $db->from('user')
    ->select(['id','name'])
    ->sql();
```

Output:

```sql
SELECT id, name FROM user
```

### Limit and Offset

To add a limit or offset to a query, you can use the `limit` and `offset` functions.

```php
echo $db->from('user')
    ->limit(10)
    ->offset(20)
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user LIMIT 10 OFFSET 20
```

You can also pass in additional parameters to the `select` function.

```php
echo $db->from('user')
    ->select('*', 50, 10)
    ->sql();
```

Output:

```sql
SELECT * FROM user LIMIT 50 OFFSET 10
```

### Distinct

To add a DISTINCT keyword to your query, call the `distinct` function.

```php
echo $db->from('user')
    ->distinct()
    ->select('name')
    ->sql();
```

Output:

```sql
SELECT DISTINCT name FROM user
```

### Table Joins

To add a table join, use the `join` function and pass in an array of fields to join on.

```php
echo $db->from('user')
    ->join('role', ['role.id' => 'user.id'])
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user INNER JOIN role ON role.id = user.id
```

The default join type is an `INNER` join. To build other types of joins you can use the alternate join functions `leftJoin`, `rightJoin`, and `fullJoin`.

The join array works just like where conditions, so you can use custom operators and add multiple conditions.

```php
echo $db->from('user')
    ->join('role', ['role.id' => 'user.id', 'role.id >' => 10])
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user INNER JOIN role ON role.id = user.id AND role.id > 10
```

### Sorting

To add sorting to a query, use the `sortAsc` and `sortDesc` functions.

```php
echo $db->from('user')
    ->sortDesc('id')
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user ORDER BY id DESC
```

You can also pass an array to the sort functions.

```php
echo $db->from('user')
    ->sortAsc(['rank','name'])
    ->select()
    ->sql();
```

Output:

```sql
SELECT * FROM user ORDER BY rank ASC, name ASC
```

### Grouping

To add a field to group by, use the `groupBy` function.

```php
echo $db->from('user')
    ->groupBy('points')
    ->select(['id','count(*)'])
    ->sql();
```

Output:

```sql
SELECT id, count(*) FROM user GROUP BY points;
```

### Insert Queries

To build an insert query, pass in an array of data to the `insert` function.

```php
$data = ['id' => 123, 'name' => 'bob'];

echo $db->from('user')
    ->insert($data)
    ->sql();
```

Output:

```sql
INSERT INTO user (id, name) VALUES (123, 'bob')
```

### Update Queries

To build an update query, pass in an array of data to the `update` function.

```php
$data = ['name' => 'bob', 'email' => 'bob@aol.com'];
$where = ['id' => 123];

echo $db->from('user')
    ->where($where)
    ->update($data)
    ->sql();
```

Output:

```sql
UPDATE user SET name = 'bob', email = 'bob@aol.com' WHERE id = 123
```

### Delete Queries

To build a delete query, use the `delete` function.

```php
echo $db->from('user')
    ->where('id', 123)
    ->delete()
    ->sql();
```

Output:

```sql
DELETE FROM user WHERE id = 123
```

## Executing Queries

DBEngine can also execute the queries it builds. You will need to call the `setDb()` method with either a connection string, an array of connection information, or a connection object.

The supported database types are `mysql`, `mysqli`, `pgsql`, `sqlite` and `sqlite3`.

Using a connection string:

```php
$db->setDb('mysql://admin:hunter2@localhost/mydb');
```

The connection string uses the following format:

type://username:password@hostname[:port]/database

For sqlite, you need to use:

type://database

Using a connection array:

```php
$db->setDb([
    'type' => 'mysql',
    'hostname' => 'localhost',
    'database' => 'mydb',
    'username' => 'admin',
    'password' => 'hunter2'
]);
```

The possible array options are `type`, `hostname`, `database`, `username`, `password`, and `port`.

Using a connection object:

```php
$mysql = mysql_connect('localhost', 'admin', 'hunter2');

mysql_select_db('mydb');

$db->setDb($mysql);
```

You can also use PDO for the database connection. To use the connection string or array method, prefix the database type with `pdo`:

```php
$db->setDb('pdomysql://admin:hunter2@localhost/mydb');
```

The possible PDO types are `pdomysql`, `pdopgsql`, and `pdosqlite`.

You can also pass in any PDO object directly:

```php
$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'admin', 'hunter2');

$db->setDb($pdo);
```

### Fetching records

To fetch multiple records, use the `many` function.

```php
$rows = $db->from('user')
    ->where('id >', 100)
    ->many();
```

The result returned is an array of associative arrays:

```php
[
    ['id' => 101, 'name' => 'joe'],
    ['id' => 102, 'name' => 'ted'];
[
```

To fetch a single record, use the `one` function.

```php
$row = $db->from('user')
    ->where('id', 123)
    ->one();
```

The result returned is a single associative array:

```php
['id' => 123, 'name' => 'bob']
```

To fetch the value of a column, use the `value` function and pass in the name of the column.

```php
$username = $db->from('user')
    ->where('id', 123)
    ->value('username');
```

All the fetch functions automatically perform a select, so you don't need to include the `select` function
unless you want to specify the fields to return.

```php
$row = $db->from('user')
    ->where('id', 123)
    ->select(['id', 'name'])
    ->one();
```

### Non-queries

For non-queries like update, insert and delete, use the `execute` function after building your query.

```php
$db->from('user')
    ->where('id', 123)
    ->delete()
    ->execute();
```

Executes:

```sql
DELETE FROM user WHERE id = 123
```

### Custom Queries

You can also run raw SQL by passing it to the `sql` function.

```php
$posts = $db->sql('SELECT * FROM posts')->many();

$user = $db->sql('SELECT * FROM user WHERE id = 123')->one();

$db->sql('UPDATE user SET name = 'bob' WHERE id = 1')->execute();
```

### Escaping Values

DBEngine's SQL building functions automatically quote and escape values to prevent SQL injection.
To quote and escape values manually, like when you're writing own queries, you can use the `quote` function.

```php
$name = "O'Dell";

printf("SELECT * FROM user WHERE name = %s", $db->quote($name));
```

Output:

```sql
SELECT * FROM user WHERE name = 'O\'Dell'
```

### Query Properties

After executing a query, several property values will be populated which you can access directly.

```php
// Last query executed
$db->last_query;

// Number of rows returned
$db->num_rows;

// Last insert id
$db->insert_id;

// Number of affected rows
$db->affected_rows;
```

These values are reset every time a new query is executed.

### Helper Methods

To get a count of rows in a table.

```php
$count = $db->from('user')->count();
```

To get the minimum value from a table.

```php
$min = $db->from('employee')->min('salary');
```

To get the maximum value from a table.

```php
$max = $db->from('employee')->max('salary');
```

To get the average value from a table.

```php
$avg = $db->from('employee')->avg('salary');
```

To get the sum value from a table.

```php
$avg = $db->from('employee')->sum('salary');
```

### Direct Access

You can also access the database object directly by using the  `getDb` function.

```php
$mysql = $db->getDb();

mysql_info($mysql);
```

## Caching

To enable caching, you need to use the `setCache` method with a connection string or connection object.

Using a connection string:

```php
$db->setCache('memcache://localhost:11211');
```

Using a cache object:

```php
$cache = new Memcache();
$cache->addServer('localhost', 11211);

$db->setCache($cache);
```

You can then pass a cache key to the query functions and DBEngine will try to fetch from the cache before executing the query. If there is a cache miss, DBEngine will execute the query and store the results using the specified cache key.

```php
$key = 'all_users';

$users = $db->from('user')->many($key);
```

### Cache Types

The supported caches are `redis`, `memcache`, `memcached`, `apc`, `xcache`, `file` and `memory`.

To use `memcache` or `memcached`, you need to use the following connection string:

`protocol://hostname:port`

To use `apc` or `xcache`, just pass in the cache name:

```php
$db->setCache('apc');
```

To use the filesystem as a cache, pass in a directory path:

```php
$db->setCache('/usr/local/cache');

$db->setCache('./cache');
```

Note that local directories must be prefixed with `./`.

The default cache is `memory` and only lasts the duration of the script.

### Cache Expiration

To cache data only for a set period of time, you can pass in an additional parameter which represents the expiraton time in seconds.

```php
$key = 'top_users';
$expire = 600;

$users = $db->from('user')
    ->sortDesc('score')
    ->limit(100)
    ->many($key, $expire);
```

In the above example, we are getting a list of the top 100 highest scoring users and caching it for 600 seconds (10 minutes).
You can pass the expiration parameter to any of the query methods that take a cache key parameter.

### Direct Access

You can access the cache object directly by using the `getCache` function.

```php
$memcache = $db->getCache();

echo $memcache->getVersion();
```

You can manipulate the cache data directly as well. To cache a value use the `store` function.

```php
$db->store('id', 123);
```

To retrieve a cached value use the `fetch` function.

```php
$id = $db->fetch('id');
```

To delete a cached value use the `clear` function.

```php
$db->clear('id');
```

To completely empty the cache use the `flush` function.

```php
$db->flush();
```

## Using Objects

DBEngine also provides some functionality for working with objects. Just define a class with public properties to represent database fields and static variables to describe the database relationship.

```php
class User {
    // Class properties
    public $id;
    public $name;
    public $email;

    // Class configuration
    static $table = 'user';
    static $id_field = 'id';
    static $name_field = 'name';
}
```

### Class Configuration

* The `table` property represents the database table. This property is required.
* The `id_field` property represents the auto-incrementing identity field in the table. This property is required for saving and deleting records.
* The `name_field` property is used for finding records by name. This property is optional.

### Loading Objects

To define the object use the `using` function and pass in the class name.

```php
$db->using('User');
```

After setting your object, you can then use the `find` method to populate the object. If you pass in an int DBEngine will search using the id field.

```php
$user = $db->find(123);
```

This will execute:

```sql
SELECT * FROM user WHERE id = 123
```

If you pass in a string DBEngine will search using the name field.

```php
$user = $db->find('Bob');
```

This will execute:

```sql
SELECT * FROM user WHERE name = 'Bob';
```

If you pass in an array DBEngine will use the fields specified in the array.

```php
$user = $db->find(
    ['email' => 'bob@aol.com']
);
```

This will execute:

```sql
SELECT * FROM user WHERE email = 'bob@aol.com'
```

If the `find` method retrieves multiple records, it will return an array of objects instead of a single object.

### Saving Objects

To save an object, just populate your object properties and use the `save` function.

```php
$user = new User();
$user->name = 'Bob';
$user->email = 'bob@aol.com';

$db->save($user);
```

This will execute:

```sql
INSERT INTO user (name, email) VALUES ('Bob', 'bob@aol.com')
```

To update an object, use the `save` function with the `id_field` property populated.

```php
$user = new User();
$user->id = 123;
$user->name = 'Bob';
$user->email = 'bob@aol.com';

$db->save($user);
```

This will execute:

```sql
UPDATE user SET name = 'Bob', email = 'bob@aol.com' WHERE id = 123
```

To update an existing record, just fetch an object from the database, update its properties, then save it.

```php
// Fetch an object from the database
$user = $db->find(123);

// Update the object
$user->name = 'Fred';

// Update the database
$db->save($user);
```

By default, all of the object's properties will be included in the update. To specify only specific fields, pass in an additional array of fields to the `save` function.

```php
$db->save($user, ['email']);
```

This will execute:

```sql
UPDATE user SET email = 'bob@aol.com' WHERE id = 123
```

### Deleting Objects

To delete an object, use the `remove` function.

```php
$user = $db->find(123);

$db->remove($user);
```

### Advanced Finding

You can use the sql builder functions to further define criteria for loading objects.

```php
$db->using('User')
    ->where('id >', 10)
    ->sortAsc('name')
    ->find();
```

This will execute:

```sql
SELECT * FROM user WHERE id > 10 ORDER BY name ASC
```

You can also pass in raw SQL to load your objects.

```php
$db->using('User')
    ->sql('SELECT * FROM user WHERE id > 10')
    ->find();
```

## Statistics

DBEngine has built in query statistics tracking. To enable it, just set the `stats_enabled` property.

```php
$db->stats_enabled = true;
```

After running your queries, get the stats array:

```php
$stats = $db->getStats();
```

The stats array contains the total time for all queries and an array of all queries executed with individual query times.

```php
array(6) {
  ["queries"]=>
  array(2) {
    [0]=>
    array(4) {
      ["query"]=>
          string(38) "SELECT * FROM user WHERE uid=1"
      ["time"]=>
          float(0.00016617774963379)
      ["rows"]=>
          int(1)
      ["changes"]=>
          int(0)
    }
    [1]=>
    array(4) {
      ["query"]=>
          string(39) "SELECT * FROM user WHERE uid=10"
      ["time"]=>
          float(0.00026392936706543)
      ["rows"]=>
          int(0)
      ["changes"]=>
          int(0)
    }
  }
  ["total_time"]=>
      float(0.00043010711669922)
  ["num_queries"]=>
      int(2)
  ["num_rows"]=>
      int(2)
  ["num_changes"]=>
      int(0)
  ["avg_query_time"]=>
      float(0.00021505355834961)
}
```

## Debugging

When DBEngine encounters an error while executing a query, it will raise an exception with the database error message. If you want to display the generated SQL along with the error message, set the `show_sql` property.

```php
$db->show_sql = true;
```

# Routing

Routing in rocphp is done by matching a URL pattern with a callback function.

```php
Roc::route('/', function(){
    echo 'hello world!';
});
```

The callback can be any object that is callable. So you can use a regular function:

```php
function hello(){
    echo 'hello world!';
}

Roc::route('/', 'hello');
```

Or a class method:

```php
class Greeting
{
    public static function hello()
    {
        echo 'hello world!';
    }
}

Roc::route('/', ['Greeting', 'hello']);
```

Or an object method:

```php
class Greeting
{
    public function __construct()
    {
        $this->name = 'John Doe';
    }

    public function hello()
    {
        echo "Hello, {$this->name}!";
    }
}

$greeting = new Greeting();

Roc::route('/', [$greeting, 'hello']);
```

Routes are matched in the order they are defined. The first route to match a request will be invoked.

## Method Routing

By default, route patterns are matched against all request methods. You can respond to specific methods by placing an identifier before the URL.

```php
Roc::route('GET /', function(){
    echo 'I received a GET request.';
});

Roc::route('POST /', function(){
    echo 'I received a POST request.';
});
```

You can also map multiple methods to a single callback by using a `|` delimiter:

```php
Roc::route('GET|POST /', function(){
    echo 'I received either a GET or a POST request.';
});
```

## Regular Expressions

You can use regular expressions in your routes:

```php
Roc::route('/user/[0-9]+', function(){
    // This will match /user/1234
});
```

## Named Parameters

You can specify named parameters in your routes which will be passed along to your callback function.

```php
Roc::route('/@name/@id', function($name, $id){
    echo "hello, $name ($id)!";
});
```

You can also include regular expressions with your named parameters by using the `:` delimiter:

```php
Roc::route('/@name/@id:[0-9]{3}', function($name, $id){
    // This will match /bob/123
    // But will not match /bob/12345
});
```

## Optional Parameters

You can specify named parameters that are optional for matching by wrapping segments in parentheses.

```php
Roc::route('/blog(/@year(/@month(/@day)))', function($year, $month, $day){
    // This will match the following URLS:
    // /blog/2012/12/10
    // /blog/2012/12
    // /blog/2012
    // /blog
});
```

Any optional parameters that are not matched will be passed in as NULL.

## Wildcards

Matching is only done on individual URL segments. If you want to match multiple segments you can use the `*` wildcard.

```php
Roc::route('/blog/*', function(){
    // This will match /blog/2000/02/01
});
```

To route all requests to a single callback, you can do:

```php
Roc::route('*', function(){
    // Do something
});
```

## Passing

You can pass execution on to the next matching route by returning `true` from your callback function.

```php
Roc::route('/user/@name', function($name){
    // Check some condition
    if ($name != "Bob") {
        // Continue to next route
        return true;
    }
});

Roc::route('/user/*', function(){
    // This will get called
});
```

## Route Info

If you want to inspect the matching route information, you can request for the route object to be passed to your callback by passing in `true` as the third parameter in the route method. The route object will always be the last parameter passed to your callback function.

```php
Roc::route('/', function($route){
    // Array of HTTP methods matched against
    $route->methods;

    // Array of named parameters
    $route->params;

    // Matching regular expression
    $route->regex;

    // Contains the contents of any '*' used in the URL pattern
    $route->splat;
}, true);
```

# Extending

Rocphp is designed to be an extensible framework. The framework comes with a set of default methods and components, but it allows you to map your own methods, register your own classes, or even override existing classes and methods.

## Mapping Methods

To map your own custom method, you use the `map` function:

```php
// Map your method
Roc::map('hello', function($name){
    echo "hello $name!";
});

// Call your custom method
Roc::hello('Bob');
```

## Registering Classes

To register your own class, you use the `register` function:

```php
// Register your class
Roc::register('user', 'User');

// Get an instance of your class
$user = Roc::user();
```

The register method also allows you to pass along parameters to your class constructor. So when you load your custom class, it will come pre-initialized. You can define the constructor parameters by passing in an additional array.
Here's an example of loading a database connection:

```php
// Register class with constructor parameters
Roc::register('db', 'PDO', ['mysql:host=localhost;dbname=test','user','pass']);

// Get an instance of your class
// This will create an object with the defined parameters
//
//     new PDO('mysql:host=localhost;dbname=test','user','pass');
//
$db = Roc::db();
```

If you pass in an additional callback parameter, it will be executed immediately after class construction. This allows you to perform any set up procedures for your new object. The callback function takes one parameter, an instance of the new object.

```php
// The callback will be passed the object that was constructed
Roc::register('db', 'PDO', array('mysql:host=localhost;dbname=test','user','pass'), function($db){
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
});
```

By default, every time you load your class you will get a shared instance.
To get a new instance of a class, simply pass in `false` as a parameter:

```php
// Shared instance of the class
$shared = Roc::db();

// New instance of the class
$new = Roc::db(false);
```

Keep in mind that mapped methods have precedence over registered classes. If you declare both using the same name, only the mapped method will be invoked.

# Overriding

Rocphp allows you to override its default functionality to suit your own needs, without having to modify any code.

For example, when Rocphp cannot match a URL to a route, it invokes the `notFound` method which sends a generic `HTTP 404` response. You can override this behavior by using the `map` method:

```php
Roc::map('notFound', function(){
    // Display custom 404 page
    include 'errors/404.html';
});
```

Rocphp also allows you to replace core components of the framework.
For example you can replace the default Router class with your own custom class:

```php
// Register your custom class
Roc::register('router', 'MyRouter');

// When Rocphp loads the Router instance, it will load your class
$myrouter = Roc::router();
```

Framework methods like `map` and `register` however cannot be overridden. You will get an error if you try to do so.

# Filtering

Rocphp allows you to filter methods before and after they are called. There are no predefined hooks you need to memorize. You can filter any of the default framework methods as well as any custom methods that you've mapped.

A filter function looks like this:

```php
function(&$params, &$output) {
    // Filter code
}
```

Using the passed in variables you can manipulate the input parameters and/or the output.

You can have a filter run before a method by doing:

```php
Roc::before('start', function(&$params, &$output){
    // Do something
});
```

You can have a filter run after a method by doing:

```php
Roc::after('start', function(&$params, &$output){
    // Do something
});
```

You can add as many filters as you want to any method. They will be called in the order that they are declared.

Here's an example of the filtering process:

```php
// Map a custom method
Roc::map('hello', function($name){
    return "Hello, $name!";
});

// Add a before filter
Roc::before('hello', function(&$params, &$output){
    // Manipulate the parameter
    $params[0] = 'Fred';
});

// Add an after filter
Roc::after('hello', function(&$params, &$output){
    // Manipulate the output
    $output .= " Have a nice day!";
});

// Invoke the custom method
echo Roc::hello('Bob');
```

This should display:

    Hello Fred! Have a nice day!

If you have defined multiple filters, you can break the chain by returning `false` in any of your filter functions:

```php
Roc::before('start', function(&$params, &$output){
    echo 'one';
});

Roc::before('start', function(&$params, &$output){
    echo 'two';

    // This will end the chain
    return false;
});

// This will not get called
Roc::before('start', function(&$params, &$output){
    echo 'three';
});
```

Note, core methods such as `map` and `register` cannot be filtered because they are called directly and not invoked dynamically.

# Variables

Rocphp allows you to save variables so that they can be used anywhere in your application.

```php
// Save your variable
Roc::set('id', 123);

// Elsewhere in your application
$id = Roc::get('id');
```
To see if a variable has been set you can do:

```php
if (Roc::has('id')) {
     // Do something
}
```

You can clear a variable by doing:

```php
// Clears the id variable
Roc::clear('id');

// Clears all variables
Roc::clear();
```

Rocphp also uses variables for configuration purposes.

```php
Roc::set('system.log_errors', true);
```

# Views

Rocphp provides some basic templating functionality by default. To display a view template call the `render` method with the name of the template file and optional template data:

```php
Roc::render('hello.php', array('name' => 'Bob'));
```

The template data you pass in is automatically injected into the template and can be reference like a local variable. Template files are simply PHP files. If the content of the `hello.php` template file is:

```php
Hello, '<?php echo $name; ?>'!
```

The output would be:

    Hello, Bob!

You can also manually set view variables by using the set method:

```php
Roc::view()->set('name', 'Bob');
```

The variable `name` is now available across all your views. So you can simply do:

```php
Roc::render('hello');
```

Note that when specifying the name of the template in the render method, you can leave out the `.php` extension.

By default Rocphp will look for a `views` directory for template files. You can set an alternate path for your templates by setting the following config:

```php
Roc::set('system.views.path', '/path/to/views');
```

## Layouts

It is common for websites to have a single layout template file with interchanging content. To render content to be used in a layout, you can pass in an optional parameter to the `render` method.

```php
Roc::render('header', ['heading' => 'Hello'], 'header_content');
Roc::render('body', ['body' => 'World'], 'body_content');
```

Your view will then have saved variables called `header_content` and `body_content`.
You can then render your layout by doing:

```php
Roc::render('layout', ['title' => 'Home Page']);
```

If the template files looks like this:

`header.php`:

```php
<h1><?php echo $heading; ?></h1>
```

`body.php`:

```php
<div><?php echo $body; ?></div>
```

`layout.php`:

```php
<html>
<head>
<title><?php echo $title; ?></title>
</head>
<body>
<?php echo $header_content; ?>
<?php echo $body_content; ?>
</body>
</html>
```

The output would be:
```html
<html>
<head>
<title>Home Page</title>
</head>
<body>
<h1>Hello</h1>
<div>World</div>
</body>
</html>
```

You can also use some custom template rules.

`{$name}` eq.
```
<?php echo $name; ?>
```

`{$arr.key}` eq.
```
<?php echo $arr[key]; ?>
```

`{$arr.key.key2}` eq.
```
<?php echo $arr[key][key2]; ?>
```

`{:strip_tags($a)}` eq.
```
<?php echo strip_tags($a); ?>
```

`{~var_dump($a)}` eq.
```
<?php var_dump($a); ?>
```

`{loop $array $vaule}{/loop}` eq.
````   
<?php
    if (is_array($array))
        foreach($array as $value) {

        }
?>
```

`{loop $array $key $value}{/loop}` eq.
```
<?php
    if (is_array($array))
        foreach($array as $key => $value) {

        }
?>
```



`{if condition} {elseif condition} {else} {/if}` eq.
```
<?php
    if (condition) {

    } else if (condition) {

    } else {

    }
?>
```


## Custom Views

Rocphp allows you to swap out the default view engine simply by registering your own view class. Here's how you would use the [Smarty](http://www.smarty.net/) template engine for your views:

```php
// Load Smarty library
require './Smarty/libs/Smarty.class.php';

// Register Smarty as the view class
// Also pass a callback function to configure Smarty on load
Roc::register('view', 'Smarty', [], function($smarty){
    $smarty->template_dir = './templates/';
    $smarty->compile_dir = './templates_c/';
    $smarty->config_dir = './config/';
    $smarty->cache_dir = './cache/';
});

// Assign template data
Roc::view()->assign('name', 'Bob');

// Display the template
Roc::view()->display('hello.tpl');
```

For completeness, you should also override Rocphp's default render method:

```php
Roc::map('render', function($template, $data){
    Roc::view()->assign($data);
    Roc::view()->display($template);
});
```
# Error Handling

## Errors and Exceptions

All errors and exceptions are caught by Rocphp and passed to the `error` method.
The default behavior is to send a generic `HTTP 500 Internal Server Error` response with some error information.

You can override this behavior for your own needs:

```php
Roc::map('error', function(Exception $ex){
    // Handle error
    echo $ex->getTraceAsString();
});
```

By default errors are not logged to the web server. You can enable this by changing the config:

```php
Roc::set('system.log_errors', true);
```

## Not Found

When a URL can't be found, Rocphp calls the `notFound` method. The default behavior is to send an `HTTP 404 Not Found` response with a simple message.

You can override this behavior for your own needs:

```php
Roc::map('notFound', function(){
    // Handle not found
});
```

# Redirects

You can redirect the current request by using the `redirect` method and passing
in a new URL:

```php
Roc::redirect('/new/location');
```

By default Rocphp sends a HTTP 303 status code. You can optionally set a
custom code:

```php
Roc::redirect('/new/location', 401);
```

# Requests

Rocphp encapsulates the HTTP request into a single object, which can be accessed by doing:

```php
$request = Roc::request();
```

The request object provides the following properties:

```
url - The URL being requested
base - The parent subdirectory of the URL
method - The request method (GET, POST, PUT, DELETE)
referrer - The referrer URL
ip - IP address of the client
ajax - Whether the request is an AJAX request
scheme - The server protocol (http, https)
user_agent - Browser information
type - The content type
length - The content length
query - Query string parameters
data - Post data or JSON data
cookies - Cookie data
files - Uploaded files
secure - Whether the connection is secure
accept - HTTP accept parameters
proxy_ip - Proxy IP address of the client
```

You can access the `query`, `data`, `cookies`, and `files` properties as arrays or objects.

So, to get a query string parameter, you can do:

```php
$id = Roc::request()->query['id'];
```

Or you can do:

```php
$id = Roc::request()->query->id;
```

## RAW Request Body

To get the raw HTTP request body, for example when dealing with PUT requests, you can do:

```php
$body = Roc::request()->getBody();
```

## JSON Input

If you send a request with the type `application/json` and the data `{"id": 123}` it will be available
from the `data` property:

```php
$id = Roc::request()->data->id;
```

# HTTP Caching

Rocphp provides built-in support for HTTP level caching. If the caching condition is met, Rocphp will return an HTTP `304 Not Modified` response. The next time the client requests the same resource, they will be prompted to use their locally cached version.

## Last-Modified

You can use the `lastModified` method and pass in a UNIX timestamp to set the date and time a page was last modified. The client will continue to use their cache until the last modified value is changed.

```php
Roc::route('/news', function(){
    Roc::lastModified(1234567890);
    echo 'This content will be cached.';
});
```

## ETag

`ETag` caching is similar to `Last-Modified`, except you can specify any id you want for the resource:

```php
Roc::route('/news', function(){
    Roc::etag('my-unique-id');
    echo 'This content will be cached.';
});
```

Keep in mind that calling either `lastModified` or `etag` will both set and check the cache value. If the cache value is the same between requests, Rocphp will immediately
send an `HTTP 304` response and stop processing.

# Stopping

You can stop the framework at any point by calling the `halt` method:

```php
Roc::halt();
```

You can also specify an optional `HTTP` status code and message:

```php
Roc::halt(200, 'Be right back...');
```

Calling `halt` will discard any response content up to that point. If you want to stop the framework and output the current response, use the `stop` method:

```php
Roc::stop();
```

# JSON

Rocphp provides support for sending JSON and JSONP responses. To send a JSON response you pass some data to be JSON encoded:

```php
Roc::json(['id' => 123]);
```

For JSONP requests you, can optionally pass in the query parameter name you are using to define your callback function:

```php
Roc::jsonp(['id' => 123], 'q');
```

So, when making a GET request using `?q=my_func`, you should receive the output:

```
my_func({"id":123});
```

If you don't pass in a query parameter name it will default to `jsonp`.


# Configuration

You can customize certain behaviors of Rocphp by setting configuration values through the `set` method.

```php
Roc::set('system.log_errors', true);
```

The following is a list of all the available configuration settings:

    system.base_url - Override the base url of the request. (default: null)
    system.case_sensitive - Case sensitive matching for URLs. (default: false)
    system.handle_errors - Allow Rocphp to handle all errors internally. (default: true)
    system.log_errors - Log errors to the web server's error log file. (default: false)
    system.views.path - Directory containing view template files. (default: ./views)
    system.views.extension - View template file extension. (default: .php)

# Framework Methods

Rocphp is designed to be easy to use and understand. The following is the complete set of methods for the framework. It consists of core methods, which are regular static methods, and extensible methods, which are mapped methods that can be filtered or overridden.

## Core Methods

```php
Roc::map($name, $callback) // Creates a custom framework method.
Roc::register($name, $class, [$params], [$callback]) // Registers a class to a framework method.
Roc::before($name, $callback) // Adds a filter before a framework method.
Roc::after($name, $callback) // Adds a filter after a framework method.
Roc::path($path) // Adds a path for autoloading classes.
Roc::get($key) // Gets a variable.
Roc::set($key, $value) // Sets a variable.
Roc::has($key) // Checks if a variable is set.
Roc::clear([$key]) // Clears a variable.
Roc::init() // Initializes the framework to its default settings.
Roc::app() // Gets the application object instance
```

## Extensible Methods

```php
Roc::start() // Starts the framework.
Roc::stop() // Stops the framework and sends a response.
Roc::halt([$code], [$message]) // Stop the framework with an optional status code and message.
Roc::route($pattern, $callback) // Maps a URL pattern to a callback.
Roc::redirect($url, [$code]) // Redirects to another URL.
Roc::render($file, [$data], [$key]) // Renders a template file.
Roc::error($exception) // Sends an HTTP 500 response.
Roc::notFound() // Sends an HTTP 404 response.
Roc::etag($id, [$type]) // Performs ETag HTTP caching.
Roc::lastModified($time) // Performs last modified HTTP caching.
Roc::json($data, [$code], [$encode]) // Sends a JSON response.
Roc::jsonp($data, [$param], [$code], [$encode]) // Sends a JSONP response.
```

Any custom methods added with `map` and `register` can also be filtered.
