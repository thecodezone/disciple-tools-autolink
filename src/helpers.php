<?php

namespace DT\Plugin;

use DT\Plugin\Illuminate\Container\Container;

function container() {
	return Container::getInstance();
}

function plugin() {
	return container()->make( Plugin::class );
}
