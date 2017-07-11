<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Protocol\IElement;

	interface ITemplateContext extends IConfigurable {
		/**
		 * set the element with the data for this context (could be for example request element)
		 *
		 * @param IElement $element
		 *
		 * @return ITemplateContext
		 */
		public function setElement(IElement $element): ITemplateContext;
	}
