<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Converter\IContent;

	/**
	 * General response (result) from an application. It can be handled by an arbitrary service.
	 */
	interface IResponse extends IContent {
	}
