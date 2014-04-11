<?php
/**
    @author Panagiotis Mastrandrikos <pmstrandrikos@gmail.com>  https://github.com/notihnio
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
 */
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
