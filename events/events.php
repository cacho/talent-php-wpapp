<?php
/*
Plugin Name: Events from Eventbrite
Plugin URI: 
Description: Get the events from Eventbrite
Version: 0.0.1
Author: Luis venegas
Author URI: https://www.neuronapixel.com
License: GPLv2 or later
Text Domain: events
*/
class Events{

    public function __construct(){
        
        add_filter( 'the_content', array($this,'addEventContent') );

        add_shortcode('twitterlink', array($this,'insertTwitterLink'));\
        add_action('init',array($this,'create_post_type'));

        wp_register_style(
            'events-stylesheet', // handle name
            plugin_dir_url(__FILE__) . '/css/styles.css' // the URL of the stylesheet
        );
        wp_enqueue_style( 'events-stylesheet' );
    }

    static public function addEventContent($content){
        if(is_single()){
            if ('events' === get_post_type()) {
                $addendum = " <h2>Events</h2>";
                $content .= $addendum;
                
                $events = $this->getDataFromAPI();
                $content.= '<div>';
                foreach ($events['items'] as $event) {
                    /**
                    
                    * Implement a style of your own to improve the look of the events list

                    */
                    
                    $content.= '<div>';

                    $content.= ' <div>
                                    <img src="'. $event['image_url'].'" alt="">
                                </div>';

                    $content.=  '<div>
                                    <h2>
                                        '. $event['name'].'
                                    </h2>
                                </div>';

                    $content.= '<div>
                                '. $event['start'].' ---  '. $event['end'].' 
                                </div>';

                    $content.= '<div>
                                    <p>
                                    '. $event['description'].'
                                    </p>
                                </div>';
                    
                    $content.= '<div>
                                    <div>
                                        <h4>
                                        '. $event['venue']['name'].'
                                        </h4>
                                        <p>
                                        '. $event['venue']['address'].'
                                        </p>
                                    </div>  
                                </div>';

                    $content.= '</div>';
                }
            $content.= '</div>';
            }
        }
        return $content;
    }

    private function getDataFromAPI(){
        $api_request = "http://159.89.138.233/events?page=1&size=10";
        $api_response = wp_remote_get( $api_request);
        $api_data = json_decode(wp_remote_retrieve_body( $api_response), true );
        return $api_data;
    }

    public function insertTwitterLink($attr){
        $attr = shortcode_atts(array('username'=>'cachitweet','text'=>'visit my twitter'),$attr);
        return '<div><a href="https://twitter.com/'.$attr['username'].'" target="_blank">'.'Follow '.$attr['username']." ".$attr['text'].'</a></div>';
    }

    function create_post_type() {
        register_post_type( 'events',
          array(
            'labels' => array(
              'name' => __( 'Event lists' ),
              'singular_name' => __( 'Events list' ),
            ),
            'public' => true,
            'has_archive' => false,
          )
        );
      }

}
$events = new Events();