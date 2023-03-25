//remove_action( 'template_redirect', 'redirect_canonical' );
add_filter( 'redirect_canonical', '__return_false' );
add_action( 'init', 'the_dramatist_fire_on_wp_initialization' );
function the_dramatist_fire_on_wp_initialization() {
	//print_r($_REQUEST);exit; 
	if( isset( $_REQUEST['_brand'] ) ){
		$url .= site_url().'/'.sanitize_title($_REQUEST['_brand']).'/';
		if( isset( $_REQUEST['parent'] ) ){
			$url .= sanitize_title($_REQUEST['parent']).'/';
		}
		//exit;
		?><script> window.location.href = "<?php echo $url; ?>"; </script><?php
	}
}

add_filter( 'template_include', 'my_callback' ); 
function my_callback( $original_template ) {
  if ( is_404() ) {
    return get_stylesheet_directory() . '/templates/brand.php';
  } else {
    return $original_template;
  }
}
add_filter('wpseo_title', 'add_to_page_titles');
function add_to_page_titles($title) {
	$brands = apiGetBrandsExt();
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $results = explode('/', trim($actual_link,'/'));  
	//print_r($results);  
	if( isset( $_SERVER['SERVER_NAME'] ) && $_SERVER['SERVER_NAME'] == "localhost" ){
		$results[3] = $results[4];
	}
    if( ! empty($brands) ){
        foreach ($brands as $key => $value) {            
            if( isset($results[3]) && $results[3] == $value['slug']  ){
                $_GET['cat'] = $value['categories'][0];
                $_GET['_brand'] = $value['name'];
                break;
            }                          
        }
    }   
    $query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
	parse_str($query, $params);
	print_r($params);
	$_brand = $params['_brand'];
	$parent = $params['parent'];

    if( ! empty( $_GET['_brand'] ) ){
    	$title_arr = array();
    	$title_arr[] =  $_GET['_brand'];
    	//$title = $_GET['_brand'] . ' - '. get_bloginfo( 'name' );
    	if( isset( $parent ) ){
    		$title_arr[] =  $parent;			
		}
    	$title_arr[] =  get_bloginfo( 'name' );

		$title = implode( ' - ', $title_arr );
    }


    return $title;
}
