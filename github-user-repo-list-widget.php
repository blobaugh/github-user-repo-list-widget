<?php
/*
  Plugin Name: Github user repository list widget
  Plugin URI: https://github.com/blobaugh/github-user-repo-list-widget
  Description: WordPress widget that lists a user's Github repositories
  Version: 0.6
  Author: Ben Lobaugh
  Author URI: http://ben.lobaugh.net
 */

class Github_User_Repo_List_No_Cache extends WP_Widget {

    public function __construct() {
	parent::__construct(
		'github-user-repo-list-widget-no-cache', // Base ID
		'Github user repo list', // Name
		array('description' => __('Github user repo list'),) // Args
	);
    }

    public function widget($args, $instance) {
	extract($args);
	$user = apply_filters('widget_title', $instance['username']);

	echo $before_widget;
	//if ( ! empty( $user ) )
	echo $before_title . $user . ' Github Repos' . $after_title;
	
	if( empty( $user ) ) {
	    echo __( 'Setup a Github user in widget preferences' );
	} else {
	    $this->get_repos( $user );
	}
	echo $after_widget;
    }

    public function form($instance) {
	if (isset($instance['username'])) {
	    $user = $instance['username'];
	} else {
	    $user = '';
	}
	?>
	<p>
	    <label for="<?php echo $this->get_field_name('username'); ?>"><?php _e('Github username:'); ?></label> 
	    <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($user); ?>" />
	</p>
	<?php
    }

    public function get_repos($user) {
	$url = 'https://api.github.com/users/' . $user . '/repos';
	
	$response = wp_remote_get($url);
	
	if (is_wp_error($response)) {
	    $error_message = $response->get_error_message();
	    echo "Something went wrong: $error_message";
	} else if(  '404' == wp_remote_retrieve_response_code( $response ) ) {
	    echo 'Invalid user';
        }else {
	    $body = json_decode( wp_remote_retrieve_body( $response ) );
	    echo '<ul>';
	    foreach( $body AS $b ) {
		echo '<li>';
		echo '<a href="' . $b->html_url . '">' . $b->name . '</a>';
		echo '<br />' . $b->description;
		echo '</li>';
	    }
	    echo '</ul>';
	}
    }

}

add_action('widgets_init', function() {
	    register_widget('Github_User_Repo_List_No_Cache');
	});
