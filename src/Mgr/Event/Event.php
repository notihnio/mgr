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
     * 
     * @param string $event the name of the event
     * @param type $args
     */
    public static function trigger($event, $args = array()) {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $callback) {
                call_user_func($callback, $args);
            }
        }
    }

    public static function bind($event, \Closure $callback) {
        self::$events[$event][] = $callback;
    }

}
