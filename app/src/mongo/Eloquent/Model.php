<?php

namespace Matrix\MongoDb\Eloquent;

use Illuminate\Contracts\Events\Dispatcher;
use Matrix\MongoDb\DatabaseManger;

abstract class Model
{
    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected static $dispatcher;
    /**
     * The Mongo db manger instance
     *
     * @var \Matrix\MongoDb\DatabaseManger
     */
    protected static $manger;

    /**
     * Set the connection manger instance.
     *
     * @param  \Matrix\MongoDb\DatabaseManger $manger
     * @return void
     */
    public static function setConnectionManger(DatabaseManger $manger)
    {
        static::$manger = $manger;

    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $dispatcher
     * @return void
     */
    public static function setEventDispatcher(Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

}