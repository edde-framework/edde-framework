<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Control\IControl;
	use Edde\Api\Filter\IFilter;
	use Edde\Api\Usable\IUsable;

	/**
	 * Control template will fill target control with predefined controls (some sort of factory).
	 */
	interface ITemplate extends IUsable {
		/**
		 * register filter; the required name is intentional (to keep filters independent)
		 *
		 * @param string $name
		 * @param IFilter $filter
		 *
		 * @return ITemplate
		 */
		public function registerFilter(string $name, IFilter $filter): ITemplate;

		/**
		 * build the target control
		 *
		 * @param string $file resource url (for example template xml file)
		 * @param IControl $control
		 *
		 * @return ITemplate
		 */
		public function build(string $file, IControl $control): ITemplate;
	}
