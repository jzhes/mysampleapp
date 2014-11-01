<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/get-environment',function() {

    echo "Environment: ".App::environment();

});

// This route triggers a PHP error
Route::get('/trigger-error',function() {

    # Class Foobar should not exist, so this should create an error
    $foo = new Foobar;

});

Route::get('mysql-test', function() {

    # Print environment
    echo 'Environment: '.App::environment().'<br>';

    # Use the DB component to select all the databases
    $results = DB::select('SHOW DATABASES;');

    # If the "Pre" package is not installed, you should output using print_r instead
    echo Pre::render($results);

});

Route::get('/', function()
{
	return View::make('hello');
});


Route::get('/saygoodbye', function()
{
	return 'Goodbye World!';
});

Route::get('/new', function() {

    $view  = '<form method="POST" action=”/new”>';
    $view .= 'Title: <input type="text" name="title">';
    $view .= '<input type="submit">';
    $view .= '</form>';
    return $view;

});

Route::post('/new', function() {

    $input =  Input::all();
    print_r($input);

});

Route::get('/books', function() {
    return 'Here are all the books...';
}); 

Route::get('/books/{category}', function($category) {
        return 'Here are all the books in the category of '.$category;
}); 


Route::get('book/{id}', function($id) {
    return 'You requested:' .$id;
}); 

Route::post('/book', function() {
    return 'POST...';
}); 


/*** Begin of: Different approach to doing the above ***/
$logic = function()
{
    return 'Here is my test for logic function...';
};

Route::get('/testlogic', $logic ); 
/*** End of: Different approach to doing the above ***/


Route::get('user/{id}', function($id)
{
	return "User number: ".$id;
});


/** In above, if don't supply a parameter, i.e. id in the URL, will get an erro 
	so can get around this by using an optional parameter, i.e. adding a ? to the id.
	Also, need to prepare our function ($id=null) in case nothing is passed, see below:
**/

Route::get('profile/{name?}', function($name=null)
{
	if ($name == null) {
		return "Pick a name to see a profile!";
	}	
	return "Profile of: ".$name;
});

/** Could also use supply a default name rather than check for null, as shown below: **/

Route::get('profiledefault/{name?}', function($name="mydefaultname")
{
	return "Profile of: ".$name;
});

# simple HTML response #1
Route::get('/response', function()
{
	return '<!doctype html>
			<html lang="en">
				<head>
					<meta charset="UTF-8">
					<title>Alright!</title>
				</head>
				<body>
					<h1>Hey it\'s HTML!</h1>
					<p>Fancy, no?</p>
				</body>
			</html>';
});
			
# simple HTML response #2
Route::get('/response', function()
{
	return '<!doctype html>
			<html lang="en">
				<head>
					<meta charset="UTF-8">
					<title>Alright!</title>
				</head>
				<body>
					<h1>Hey it\'s HTML!</h1>
					<p>Fancy, no?</p>
				</body>
			</html>';
});				

Route::get('/responseView', function() {
	return View::make('responseView');
});

# passing data to views
Route::get('/howOld/{age}', function($age) {
	$data['age'] = $age;
	return View::make('howOld', $data); // $data is an array
});

# example to parse ages by if/else
# using a filter is better than doing below
Route::get('/howOldIf/{age}', function($age) {
	$data['age'] = $age;
	if ($age > 10) {
		return View::make('howOld', $data); // $data is an array
	} else {
		return "Hey youngin";
	}	
});

##### redirect examples #####

# without redirect
/*
Route::get('first', function() {
	return 'First Route';
});

Route::get('second', function() {
	return 'Second Route';
});
*/

# with redirect
Route::get('first', function() {
	return Redirect::to('second');
});

Route::get('second', function() {
	return 'Second Route';
});

##### filter examples #####

# with no filter
Route::get('/catsNoFilter', function() {
	return 'Secret cat club, no dogs allowed!!';
});

# with filter
# second parameter can be an array, that's where you put the filters
/*
Route::get('/cats', array(
	'before' => 'dogs_day', // if this condition is met, filter logic is run
							// if not, the function below is run
	function() {
		return 'Secret cat club, no dogs allowed!!';
	}
));
*/

# multiple filters
Route::get('/cats', array(
	'before' => 'dogs_day|parrots_day', 
	function() {
		return 'Secret cat club, no dogs allowed!!';
	}
));

# run a filter on a 'group' of routes
/*
Route::group(array('before' => 'dogs_day'), function() 
{
	Route::get('/cats/only', function()
	{
		// Has dog filter
		return 'Secret cat club, no dogs allowed!!';
	});

	Route::get('/human/user/{id}', function($id)
	{
		// Has dog filter
		return 'Non-dog user id:'.$id;
	});
));
*/

Route::get('flights/{day_of_week}', function($day_of_week)
{
$days = Array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
if (in_array(strToLower($day_of_week), $days)) {
	return "here: $day_of_week...";
} else {
		return "Invalid day";
}
});		

/* Lecture 6 Examples */

#Homepage
Route::get('/', function() {
	return View::make('index');
});
	
// List all books/search
Route::get('/list/{format?}', function($format='html') {

	//Get the file
	//$books = File::get(app_path().'/database/books.json');
	
	// Convert to an array
	//$books = json_decode($books, true);

	$query = Input::get('query'); // to get the query string
	
	$library = new Library();
	$library->setPath(app_path().'/database/books.json');
	$books = $library->getBooks();
	
	if ($query) {
		$books = $library->search($query);
	}
		
	if ($format == 'pdf') {
		return 'PDF Version';
	}
	elseif ($format == 'json') {
		return 'JSON Version';
	}	
	else {
		// want to pass array to view so can process all boooks
		return View::make('list') 
				-> with('name', 'Susan')
				-> with('books', $books)
				-> with('query', $query);
	}	
});
	
// Display the form for a new book	
Route::get('/add', function() {
	
});
	
// Process form for a new book	
Route::post('/add', function() {
	
});

// Display the form to edit a book	
Route::get('/add', function() {
	
});
	
// Process form to edit a book	
Route::post('/edit/', function() {
	
});

// Determine where paths are in your application:
Route::get('/pathfinder', function() {
	echo app_path()."<br>";
	echo public_path()."<br>";
	echo base_path()."<br>";
	echo storage_path()."<br>";
});

// Get the contents for the book
Route::get('/data', function() {
	//Get the file
	//$books = File::get(app_path().'/database/books.json');
	
	// Convert to an array
	//$books = json_decode($books, true);
	
	// Return the file
	//echo Pre::render($books);
	
	// Just retrieve 1st book from array
	// $first_book = array_pop($books);
	// return $first_book;

	$library = new Library();
	$library->setPath(app_path().'/database/books.json');
	$books = $library->getBooks();

	// Return the file
	echo Pre::render($books);
	
});

Route::get('/debug', function() {

    echo '<pre>';

    echo '<h1>environment.php</h1>';
    $path   = base_path().'/environment.php';

    try {
        $contents = 'Contents: '.File::getRequire($path);
        $exists = 'Yes';
    }
    catch (Exception $e) {
        $exists = 'No. Defaulting to `production`';
        $contents = '';
    }

    echo "Checking for: ".$path.'<br>';
    echo 'Exists: '.$exists.'<br>';
    echo $contents;
    echo '<br>';

    echo '<h1>Environment</h1>';
    echo App::environment().'</h1>';

    echo '<h1>Debugging?</h1>';
    if(Config::get('app.debug')) echo "Yes"; else echo "No";

    echo '<h1>Database Config</h1>';
    print_r(Config::get('database.connections.mysql'));

    echo '<h1>Test Database Connection</h1>';
    try {
        $results = DB::select('SHOW DATABASES;');
        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
        echo "<br><br>Your Databases:<br><br>";
        print_r($results);
    } 
    catch (Exception $e) {
        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
    }

    echo '</pre>';

});







	
