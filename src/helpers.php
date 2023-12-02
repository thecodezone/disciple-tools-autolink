<?php

namespace CZ\Plugins;

use CZ\Illuminate\Container\Container;
use CZ\Plugin\Plugin;

function container() {
	return Container::getInstance();
}

function plugin() {
	return container()->make( Plugin::class );
}
