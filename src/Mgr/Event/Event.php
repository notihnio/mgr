<?php
/**
    @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio
 
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
    public static array $events = [];

    /**
     * @name trigger
     * @description triggers an event
     *
     * @param string $event the name of the event
     * @param array  $args  the event passing arguments
     */
    public static function trigger(string $event, array $args = []): void
    {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $callback) {
                $callback($args);
            }
        }
    }

    /**
     * @param string   $event    the name of the event
     * @param \Closure $callback the callback function
     *
     * @description binds an event
     *
     */
    public static function bind(string $event, \Closure $callback) : void
    {
        self::$events[$event][] = $callback;
    }

}
