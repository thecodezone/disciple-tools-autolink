<?php

namespace DT\Plugin\Conditions;

class IsFrontendPath implements Condition {
	public function test(): bool {
		return ! is_admin();
	}
}