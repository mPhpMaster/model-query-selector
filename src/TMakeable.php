<?php
/*
 * Copyright © 2024. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\ModelQuerySelector;

trait TMakeable
{
	/**
	 * Create a new class instance.
	 *
	 * @param mixed ...$parameters
	 *
	 * @return static
	 */
	public static function make(...$parameters): static
	{
		return new static(...$parameters);
	}
}
