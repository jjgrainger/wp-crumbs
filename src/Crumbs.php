<?php
/*
    Crumbs
    Simple Worpdress Breadcrumbs
    http://github.com/jjgrainger/wp-crumbs/

    @author     jjgrainger
    @url        http://jjgrainger.co.uk
    @version    1.0.2
    @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

Class Crumbs {

    /*
        @var  array  $defaults
        default options
    */
    var $breadcrumbs = array();



    /*
        @var  array  $defaults
        default options
    */
    var $defaults = array(
        'home' => 'Home',
        'blog' => 'Blog',
        'blog_url' => '',
        'seperator' => '/',
        'class' => 'crumbs',
        'element' => 'nav'
    );



    /*
        @var  array  $options
        an array of merged user defined options with defaults
    */
    var $options = array();



    /*
        @function __contructor(@options)

        @param  array   $options            User submitted options
    */

    function __construct($options = array()) {

        // is blog/front page
        if(get_option('show_on_front') == 'page')  {
            $this->defaults['blog_url'] = get_permalink(get_option('page_for_posts'));
        } else {
            $this->defaults['blog_url'] = bloginfo('url');
        }

        // merge options
        $this->options = array_replace_recursive($this->defaults, $options);

        // start making crumbs
        $this->do_breadcrumbs();

    }



    /*
        @function the_breadcrumbs()
        outputs breadcrumb html
    */
    function the_breadcrumbs() {

        $breadcrumbs = $this->get_breadcrumbs();
        $count = count($breadcrumbs);
        $i = 1;
        $el = $this->options['element'];

        // start the element
        $html = sprintf('<%s class="%s">', $el, $this->options['class']);

        foreach($breadcrumbs as $crumb) {

            // if a list wrap each item in a <li>
            if($el === 'ul' || $el === 'ol') {
                $html .= '<li>';
            }

            // if crumb has a url make a link
            if($crumb['url']) {
                $html .= sprintf('<a href="%s">%s</a>', $crumb['url'], $crumb['title']);

            // otherwise make a span
            } else {
                $html .= sprintf('<span>%s</a>', $crumb['title']);
            }

            // add seperators
            if($i < $count && !empty($this->options['seperator'])) {
                $html .= sprintf('<span class="sep">%s</span>', $this->options['seperator']);
            }

            // if list, close <li> tag
            if($el === 'ul' || $el === 'ol') {
                $html .= '</li>';
            }

            $i++;

        }

        // close the element
        $html .= sprintf('</%s>', $el);

        echo $html;
    }



    /*
        @function get_breadcrumbs()
        returns breadcrumb array
    */
    function get_breadcrumbs() {
        $breadcrumbs = $this->breadcrumbs;
        $breadcrumbs = apply_filters('get_crumbs', $breadcrumbs);
        return $breadcrumbs;
    }



    /*
        @function add_breadcrumb(@title, @url)
        Adds a crumb to the breadcrumb array

        @param  string  $title          title for crumb link
        @param  string  $url            The crumb url

    */
    function add_breadcrumb($title, $url = false) {
        $this->breadcrumbs[] = array(
            'title' => $title,
            'url' => $url
        );
    }



    /*
        @function do_breadcrumbs()
        Create breadcrumbs
    */
    function do_breadcrumbs() {

        // if not on the front page, add a home crumb
        if(!is_front_page()) {
            $this->add_breadcrumb($this->options['home'], site_url('/'));
        }

        // for pages
        if(is_page() && !is_front_page()) {

            // get ancestors and add them to the trail
            $ancestors = get_ancestors(get_the_ID(), 'page');

            $ancestors = array_reverse($ancestors);

            if(count($ancestors) > 0) {
                foreach($ancestors as $page_id) {
                    $this->add_breadcrumb(get_the_title($page_id), get_permalink($page_id));
                }
            }

            // get this page title
            $this->add_breadcrumb(get_the_title());
        }

        // if is blog archive
        if(is_home()) {
            $this->add_breadcrumb($this->options['blog']);
        }

        // if a category archive
        if(is_category()) {

            $this->add_breadcrumb($this->options['blog'], $this->options['blog_url']);

            // get current category
            $cat = get_the_category();
            $cat = $cat[0];
            $cat_link = esc_url(get_category_link($cat->term_id));

            $this->add_breadcrumb(single_cat_title('', false));
        }

        // if tag archive
        if(is_tag()) {

            $tag_id = get_query_var('tag_id');
            $url = get_tag_link($tag_id);

            $this->add_breadcrumb(single_tag_title('', false));
        }

        // if is date archive
        if(is_date()) {
            $this->add_breadcrumb($this->options['blog'], $this->options['blog_url']);

            // for day
            if(is_day()) {
                $this->add_breadcrumb(get_the_date());
            }

            // for month
            if(is_month()) {
                $this->add_breadcrumb(get_the_date('F Y'));
            }

            // for year
            if(is_year()) {
                $this->add_breadcrumb(get_the_date('Y'));
            }
        }

        // single post
        if(is_singular('post')) {

            // add the archive to breadcrumbs
            $this->add_breadcrumb($this->options['blog'], $this->options['blog_url']);
            $this->add_breadcrumb(get_the_title());

        }


        // post types
        if(!is_singular('page') && !is_singular('post')) {

            if(is_post_type_archive()) {
                $this->add_breadcrumb(post_type_archive_title('', false));
            }

            if(is_tax()) {
                $taxonomy = get_query_var('taxonomy');
                $term = get_term_by('slug', get_query_var($taxonomy), $taxonomy);
                $tax_name = $term->name;

                $this->add_breadcrumb($tax_name);
            }

            if(is_singular()) {

                $post_type = get_post_type(get_the_ID());
                $obj = get_post_type_object($post_type);

                $this->add_breadcrumb($obj->labels->all_items, get_post_type_archive_link($post_type));
                $this->add_breadcrumb(get_the_title());
            }
        }
    }

}



/*
    Functions
 */

/*
    @function the_crumbs($options)
    outputs breadcrumb html

    @param  array  $options     an array of options for crumbs
 */

if(!function_exists('the_crumbs')) {

    function the_crumbs($options = array()) {
        $Crumbs = new Crumbs($options);
        $Crumbs->the_breadcrumbs();
    }

}



/*
    @function get_crumbs($options)
    returns the array of crumbs

    @param  array  $options     an array of options for crumbs
 */
if(!function_exists('get_crumbs')) {

    function get_crumbs($options = array()) {
        $Crumbs = new Crumbs($options);
        return $Crumbs->get_breadcrumbs();
    }

}



/*
    @function array_insert()
    insert an element into an array at a specific position

    @param  array  $array      the array to add an element to
    @param  any    $element    the element to add to the array
    @param  int    $position   the position in the array to add the element

 */

if(!function_exists('array_insert')) {

    function array_insert($array, $element, $position) {

        // if the array is empty just add the element to it
        if(empty($array)) {

            $array[] = $element;

        // if the position is a negative number
        } elseif(is_numeric($position) && $position < 0) {

            // if negative position after count
            if(count($array) + $position < 0) {
                $position = 0;
            } else {
                $position = count($array) + $position;
            }

            // try again with a positive position
            $array = array_insert($array, $element, $position);

        // if array position already set
        } elseif(isset($array[$position])) {

            // split array into two parts
            $split1 = array_slice($array, 0, $position, true);
            $split2 = array_slice($array, $position, null, true);

            // add new array element at between two parts
            $array = array_merge($split1, array($position => $element), $split2);

        // if position not set add to end of array
        } elseif(is_null($position)) {

            $array[] = $element;

        // if the position is not set
        } elseif(!isset($array[$position])) {
            $array[$position] = $element;
        }

        // clean up indexes
        $array = array_values($array);

        return $array;

    }

}
