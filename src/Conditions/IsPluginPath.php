<?php

namespace DT\Plugin\Conditions;

use DT\Plugin\Illuminate\Http\Request;
use DT\Plugin\Illuminate\Support\Str;
use DT\Plugin\Plugin;

class IsPluginPath implements Condition {

	protected Request $request;

	public function __construct( Request $request ) {
		$this->request = $request;
	}

	public function test(): bool {
		return Str::startsWith( $this->request->path(), Plugin::HOME_ROUTE );
	}
}