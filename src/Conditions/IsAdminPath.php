<?php

namespace DT\Plugin\Conditions;

class IsAdminPath implements Condition {

	public function test(): bool {
		return is_admin();
	}
}