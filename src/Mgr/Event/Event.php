<?php

namespace Mgr\Event;
/**
 * @name Event
 * @description Handles events
 * 
 * @usage

  Example 1:
  event::bind('blog.post.create', function($args = array())
  {
  mail('myself@me.com', 'Blog Post Published', $args['name'] . ' has been published');
  });

  Example 2:
  event::trigger('blog.post.create', $postInfo);

 */
class Event {

    /**
     *
     * @var array of events
     * 
     */
    public static $events = array();

    /**
     * @name trigger
     * @description triggers an event
     * @param string $event the name of the event
     * @param array $args the event passing arguments
     */
    public static function trigger($event, $args = array()) {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $callback) {
                call_user_func($callback, $args);
            }
        }
    }

     /**
     * @name bind
     * @description binds an event
     * @param string $event the name of the event
     * @param type $callback the callback function
     */
    public static function bind($event, \Closure $callback) {
        self::$events[$event][] = $callback;
    }

}
