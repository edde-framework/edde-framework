<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Filter\IFilter;
	use Edde\Api\Usable\IUsable;

	/**
	 * Control template will fill target control with predefined controls (some sort of factory).
	 */
	interface ITemplateFactory extends IUsable {
		/**
		 * register filter; the required name is intentional (to keep filters independent)
		 *
		 * @param string $name
		 * @param IFilter $filter
		 *
		 * @return ITemplateFactory
		 */
		public function registerFilter(string $name, IFilter $filter): ITemplateFactory;

		/**
		 * @param ITemplate $template
		 *
		 * @return ITemplateFactory
		 */
		public function build(ITemplate $template): ITemplateFactory;
	}
