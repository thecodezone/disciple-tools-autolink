<?php

namespace DT\Plugin;

function container() {
	return Plugin::$instance->container;
}

function plugin() {
	return Plugin::$instance;
}
