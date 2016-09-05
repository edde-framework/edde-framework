<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	interface IHtmlTemplate {
		public function use (string $file): IHtmlTemplate;

		public function getBlockList(): array;

		public function block(string $name, IHtmlControl $parent): IHtmlTemplate;

		public function template(IHtmlControl $htmlControl, array $useList = []);
	}
