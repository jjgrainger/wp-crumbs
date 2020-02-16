# WP Crumbs v1.0.2

> Simple Wordpress Breadcrumbs

[![Build Status](https://travis-ci.org/jjgrainger/wp-crumbs.svg?branch=master)](https://travis-ci.org/jjgrainger/wp-crumbs) [![Total Downloads](https://poser.pugx.org/jjgrainger/wp-crumbs/downloads)](https://packagist.org/packages/jjgrainger/wp-crumbs) [![Latest Stable Version](https://poser.pugx.org/jjgrainger/wp-crumbs/v/stable)](https://packagist.org/packages/jjgrainger/wp-crumbs) [![License](https://poser.pugx.org/jjgrainger/wp-crumbs/license)](https://packagist.org/packages/jjgrainger/wp-crumbs)

## Requirements

* PHP >= 7.2
* [Composer](https://getcomposer.org/)
* [WordPress](https://wordpress.org) 5.3.2

## Installation

```
$ composer require jjgrainger/wp-crumbs
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
the_crumbs( [
    'home'      => 'Home',   // Title for the Home (front page) link
    'blog'      => 'Blog',   // Title for the blog home page
    'seperator' => '/',      // Seperator to use between each crumb (string or false)
    'class'     => 'crumbs', // The class(es) applied to the wrapper element ('crumbs', 'nav crumbs')
    'element'   => 'nav'     // The HTML element to use (div, nav, ol, ul)
] );
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

##### Example

```php
$breadcrumbs = get_crumbs();

print_r( $breadcrumbs );
```

##### Output
```php
Array(
    [0] => Array(
        [title] => "Home"
        [url] => "http://site.com/"
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

##### Example

```php

// Example: modify title for a custom post type
function modify_crumbs( $crumbs ) {
    // if on events archive change title to shows
    if ( is_post_type_archive( 'event' ) || is_singular( 'event' ) ) {
        for ( $i = 0; $i < count($crumbs); $i++ ) {
            if ( $crumbs[$i]['title'] === 'Events' ) {
                $crumbs[$i]['title'] = "Shows";
            }
        }
    }

    return $crumbs;
}

add_filter( 'get_crumbs', 'modify_crumbs' );
```

#### array_insert()

`array_insert()` is a function that allows you to insert a new element into an array at a specific index.
[You can see a gist of it here](https://gist.github.com/jjgrainger/845271930a319079b74b).

when modifying the crumb trail you can add new crumbs at specific points.

##### Example

```php
// Example: add post type archive on taxonomy archive page
function modify_crumbs( $crumbs ) {
    // if on the events category archive page
    if ( is_tax( 'event-categories' ) ) {
        // create a new crumb
        $crumb = [
            'title' => "Shows",
            'url'   => site_url( '/shows' ),
        ];

        // add the new crumb at the index of 1
        $crumbs = array_insert( $crumbs, $crumb, 1 );
    }

    return $crumbs;
}

add_filter( 'get_crumbs', 'modify_crumbs' );
```

## Notes

* Licensed under the [MIT License](https://github.com/jjgrainger/wp-crumbs/blob/master/LICENSE)
* Maintained under the [Semantic Versioning Guide](http://semver.org)

## Author

**Joe Grainger**

* [https://jjgrainger.co.uk](https://jjgrainger.co.uk)
* [https://twitter.com/jjgrainger](https://twitter.com/jjgrainger)
