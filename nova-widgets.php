<?php 

/*
 * Plugin Name: Nova Widgets
 * Description: This is a plugin to setup Recent Post & Tags widget for Nova Theme
 * version: 1.0
 * Author: Rahatul Ahsan
 * Author URI: https://www.rahatulahsan.com
 */

 function nova_widget_css(){
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_style('nova_css',$plugin_url.'style.css');
 }

 add_action('wp_enqueue_scripts', 'nova_widget_css');

 
 class Nova_Widgets extends WP_Widget{

    public function __construct(){
        //initialize widget name, id, or other attributes
        parent:: __construct('nova-widgets', 'Nova Widgets');

        add_action('widgets_init', function(){
            register_widget('Nova_Widgets');
        });
    }

    public function form($instance){
        // admin panel layout

        $PostTitle = !empty($instance['post-title']) ? $instance['post-title'] : "";
        $TagTitle = !empty($instance['tag-title']) ? $instance['tag-title'] : "";

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title_for_posts'); ?>">Title For Posts</label>
            <input type="text" name ="<?php echo $this->get_field_name('title_for_posts'); ?>" 
            id ="<?php echo $this->get_field_id('title_for_posts'); ?>" value="<?php echo $PostTitle; ?>" 
            placeholder="Enter title for posts sidebar" class="widefat"/>
        </p>

        <p>
            <label for ="<?php echo $this->get_field_id('title_for_tags'); ?>">Title For Tags<label>
            <input type="text" name="<?php echo $this->get_field_name('title_for_tags'); ?>" 
            id="<?php echo $this->get_field_id('title_for_tags'); ?>" value="<?php echo $TagTitle; ?>" 
            placeholder="Enter name for tags sidebar" class="widefat"/>
        </p>
    <?php }

    public function widget($args, $instance){
        //frontend layout
        echo $args['before_widget'];

        if(!empty($instance['post-title'])){
            echo '<h3 class="sidebar-title">' . $instance['post-title'] . '</h3>';
        }

        $get_recent_posts = $this->recent_posts_query();

        while($get_recent_posts->have_posts()){
            $get_recent_posts->the_post(); ?>

                <div class="mt-3">

                    <div class="post-item blog mt-3">
                        <img src="<?php echo the_post_thumbnail_url('blog-sidebar'); ?>" alt="<?php the_title(); ?>" class="flex-shrink-0">
                        <div>
                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <time datetime="<?php the_time( 'F j, Y' );?>"><?php the_time( 'F j, Y' );?></time>
                        </div>
                    </div><!-- End recent post item-->

                </div>

        <?php }

        
        echo $args['after_widget'];

        //Showcase tags
        echo $args['before_widget'];
        if(!empty($instance['tag-title'])){
            echo '<h3 class="sidebar-title">' . $instance['tag-title'] . '</h3>';
        }
        $tags = get_tags(array(
            'taxonomy' => 'post_tag',
            'orderby' => 'name',
            'hide_empty' => false // for development
          ));
        
        echo '<ul class="mt-3 tags">';
        foreach($tags as $tag){
            $tag_link = get_tag_link($tag->term_id);?>
            <li><a href="<?php echo $tag_link; ?>"><?php echo $tag->name; ?></a></li>
        <?php }
        echo "</ul>";

        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance){
        //help to save in database
        $instance = array();

        $instance['post-title'] = isset($new_instance['title_for_posts']) ? strip_tags($new_instance['title_for_posts']) : "";

        $instance['tag-title'] = isset($new_instance['title_for_tags']) ? strip_tags($new_instance['title_for_tags']) : "";

        return $instance;
    }

    public function recent_posts_query(){

        // query to get recent posts
        $args = array(
            'posts_per_page' => 3,
            'post_type' => 'post'
        );

        return $recentPosts = new WP_Query($args);
    }
 }


 $nova_widget = new Nova_Widgets();