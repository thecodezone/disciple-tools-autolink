<?php

namespace DT\Plugin\Conditions;

use DT\Plugin\Illuminate\Support\Str;
use Illuminate\Http\Request;

class IsPluginPath implements Condition {

	protected Request $request;

	public function __construct( Request $request ) {
		$this->request = $request;
	}

	public function test(): bool {
		return Str::startsWith( $this->request->path(), 'dt/plugin' );
	}
}