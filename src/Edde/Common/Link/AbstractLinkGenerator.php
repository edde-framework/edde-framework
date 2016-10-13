<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Common\AbstractObject;

	abstract class AbstractLinkGenerator extends AbstractObject implements ILinkGenerator, ILazyInject {
		/**
		 * shorthand for translating generate ILink to values
		 *
		 * @param mixed $generate
		 * @param array $parameterList
		 *
		 * @return array
		 */
		protected function list($generate, array $parameterList) {
			return $generate instanceof ILink ? [
				$generate->getLink(),
				$generate->getParameterList(),
			] : [
				$generate,
				$parameterList,
			];
		}
	}
