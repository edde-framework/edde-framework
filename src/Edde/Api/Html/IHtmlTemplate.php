<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	interface IHtmlTemplate {
		public function getBlockList(): array;

		public function block(string $name, IHtmlControl $parent);

		public function template(IHtmlControl $htmlControl);
	}
