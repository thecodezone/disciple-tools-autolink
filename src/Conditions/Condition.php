<?php

namespace DT\Plugin\Conditions;

interface Condition {
	public function test(): bool;
}