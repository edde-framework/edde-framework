<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Common\AbstractObject;

	abstract class AbstractLinkGenerator extends AbstractObject implements ILinkGenerator, ILazyInject {
	}
