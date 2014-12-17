# WP Crumbs v1.0.1

Simple Wordpress Breadcrumbs

## Installation

#### Install with Composer

Add the package to your projects `composer.json` file. Visit [getcomposer.org](http://getcomposer.org/) more information.

```json
{
    "require": {
        "jjgrainger/wp-crumbs": "dev-master"
    }
}
```

#### Install Manually

Download and include the class file into your themes `functions.php`:

```php
include_once 'path/to/Crumbs.php';
```


## Usage

#### Basic usage

Use `the_crumbs` to display the breadcrumbs in your template.

```php
the_crumbs();
```

## Options

An optional array of arguments can be passed to modify the breadcrumb output.
The defaults for each option are display below.

##### Example

```php
the_crumbs(array(
    'home' => 'Home',       // Title for the Home (front page) link
    'blog' => 'Blog',       // Title for the blog home page
    'seperator' => '/',     // Seperator to use between each crumb (string or false)
    'class' => 'crumbs',    // The class(es) applied to the wrapper element ('crumbs', 'nav crumbs')
    'element' => 'nav'      // The HTML element to use (div, nav, ol, ul)
));
```

##### Output

```html
<nav class="crumbs">
    <a href="http://site.com/">Home</a>
    <span class="sep">/</span>
    <a href="http://site.com/blog/">Blog</a>
    <span class="sep">/</span>
    <span>Single Post Title</a>
</nav>
```


## Advanced usage

#### get_crumbs()

Use `get_crumbs()` to retrieve an array of crumbs. Each crumb has a `title` and `url`.

##### example

```php
$breadcrumbs = get_crumbs();

print_r($breadcrumbs);
```

##### output
```php
Array(
    [0] => Array(
        [title] => "Home"
        [url] => http://site.com/
    )
    [1] => Array(
        [title] => "Blog"
        [url] => "http://site.com/blog/"
    )
    [2] => Array(
        [title] => "My First Post"
        [url] => false
    )
)
```

You can modify the returned array and/or create a function to create an output of your choosing.

`get_crumbs` accepts the same aguments as `the_crumbs`.

#### Filters

You can further modify the crumbs array using a filter on `get_crumbs`. This will also effect the output of `the_crumbs`.

##### example

```php

// Example: modify title for a custom post type
function modify_crumbs($crumbs) {

    // if on events archive change title to shows
    if(is_post_type_archive('event') || is_singular('event')) {
        for($i = 0; $i < count($crumbs); $i++) {
            if($crumbs[$i]['title'] === 'Events') {
                $crumbs[$i]['title'] = "Shows";
            }
        }
    }

    return $crumbs;
}

add_filter('get_crumbs', 'modify_crumbs');
```

#### array_insert()

`array_insert()` is a function that allows you to insert a new element into an array at a specific index.
[You can see a gist of it here](https://gist.github.com/jjgrainger/845271930a319079b74b).

when modifying the crumb trail you can add new crumbs at specific points.

##### example

```php
// Example: add post type archive on taxonomy archive page
function modify_crumbs($crumbs) {

    // if on the events category archive page
    if(is_tax('event-categories')) {

        // create a new crumb
        $crumb = array(
            'title' => "Shows",
            'url' => site_url('/shows')
        );

        // add the new crumb at the index of 1
        $crumbs = array_insert($crumbs, $crumb, 1);
    }

    return $crumbs;

}

add_filter('get_crumbs', 'modify_crumbs');
```

## Notes

* Licensed under the [MIT License](https://github.com/jjgrainger/wp-crumbs/blob/master/LICENSE)
* Maintained under the [Semantic Versioning Guide](http://semver.org)

## Author

**Joe Grainger**
* [http://jjgrainger.co.uk](http://jjgrainger.co.uk)
* [http://twitter.com/jjgrainger](http://twitter.com/jjgrainger)
