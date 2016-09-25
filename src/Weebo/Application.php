<?php

namespace Yakovmeister\Weebo;

use Illuminate\Container\Container;

class Application extends Container
{
    const NAME = "butler";

    const VERSION = "1.2.0";

	public function __construct()
	{
		$this->registerBaseBindings();
	}

	/**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance("Illuminate\\Container\\Container", $this);
    }

    public function run()
    {
    	$console = $this->make("Yakovmeister\\Weebo\\Console\\Console");

        $console->boot();
    }
}