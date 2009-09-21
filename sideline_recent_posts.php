<?php
/*
Plugin Name: Sideline Recent Posts Widget
Plugin URI: http://www.sideline.ca/
Description: Adds a widget that can be used multiple times to display the most recent entries in a specific category or in all categories.
Author: Michael McClenaghan
Version: 1.2
Author URI: http://www.sideline.ca/
*/

function widget_sideline_recent_posts($args, $number = 1) {
  extract($args);
	$options = get_option("widget_sideline_recent_posts");
  $showcount = $options[$number]['ShowCount'] ? '1' : '0';
  $numposts = empty($options[$number]['NumPosts']) ? __('5') : $options[$number]['NumPosts'];
	$title = empty($options[$number]['Title']) ? __('Recent Reads') : $options[$number]['Title'];
	$cat = (int) $options[$number]['Category'];
?>

	<?php echo $before_widget; ?>
	  <?php echo $before_title . $title . $after_title;	?>
	<ul>
		<?php query_posts("cat=$cat&showposts=$numposts"); ?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<li><a href="<?php the_permalink() ?>"><?php the_title() ?></a> <?php if ($showcount) echo '<span>' . comments_number('0', '1', '%') . '</span>' ?></li>
		<?php endwhile; endif; ?>
	</ul>
<?php
  echo $after_widget;
}

function sideline_recent_posts_control($number) {
  $options = $newoptions = get_option("widget_sideline_recent_posts");
  if ($_POST["sideline_recent_posts-Submit-$number"]) {
		$newoptions[$number]['ShowCount'] = isset($_POST["sideline_recent_posts-ShowCount-$number"]);
    $newoptions[$number]['NumPosts'] = strip_tags(stripslashes($_POST["sideline_recent_posts-NumPosts-$number"]));
    $newoptions[$number]['Title'] = strip_tags(stripslashes($_POST["sideline_recent_posts-Title-$number"]));
    $newoptions[$number]['Category'] = (int) $_POST["sideline_recent_posts-Category-$number"];
	}
	if ($options != $newoptions) {
		$options = $newoptions;
    update_option("widget_sideline_recent_posts", $options);
  }
  $showcount = $options[$number]['ShowCount'] ? 'checked="checked"' : '';
  $numposts = htmlspecialchars($options[$number]['NumPosts'], ENT_QUOTES);
	$title = htmlspecialchars($options[$number]['Title'], ENT_QUOTES);
	$cat = (int) $options[$number]['Category'];
?>
	<p>
		<label for="sideline_recent_posts-Title-<?php echo "$number"; ?>"><?php _e('Title:'); ?></label>
		<input type="text" id="sideline_recent_posts-Title-<?php echo "$number"; ?>" name="sideline_recent_posts-Title-<?php echo "$number"; ?>" value="<?php echo $title; ?>" />
	</p>
	
	<p>
		<label for="sideline_recent_posts-Category-<?php echo "$number"; ?>"><?php _e( 'Category:' ); ?></label>
		<?php wp_dropdown_categories( array( 'name' => "sideline_recent_posts-Category-$number", 'selected' => $cat , 'show_option_all' => 'All recent posts') ); ?>
	</p>	
	
  <p>
    <label for="sideline_recent_posts-NumPosts-<?php echo "$number"; ?>">Number of posts to show: </label>
    <input type="text" id="sideline_recent_posts-NumPosts-<?php echo "$number"; ?>" name="sideline_recent_posts-NumPosts-<?php echo "$number"; ?>" value="<?php echo $numposts; ?>" size="2" />
	</p>
	<p>	
		<label for="sideline_recent_posts-ShowCount-<?php echo "$number"; ?>">Show post counts </label>
		<input class="checkbox" type="checkbox" <?php echo $showcount; ?> id="sideline_recent_posts-ShowCount-<?php echo "$number"; ?>" name="sideline_recent_posts-ShowCount-<?php echo "$number"; ?>" />
	</p>	
  <input type="hidden" id="sideline_recent_posts-Submit-<?php echo "$number"; ?>" name="sideline_recent_posts-Submit-<?php echo "$number"; ?>" value="1" />
<?php	
}

function sideline_recent_posts_register() {
	$options = get_option('widget_sideline_recent_posts');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = array('Recent Reads %s', 'widgets', $i);
		register_sidebar_widget($name, $i <= $number ? 'widget_sideline_recent_posts' : /* unregister */ '', $i);
		register_widget_control($name, $i <= $number ? 'sideline_recent_posts_control' : /* unregister */ '', 410, 200, $i);
	}
}

add_action("plugins_loaded", "sideline_recent_posts_register");

?>