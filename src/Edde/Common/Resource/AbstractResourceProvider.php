<?php
	declare(strict_types=1);

	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResourceProvider;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractResourceProvider extends Object implements IResourceProvider {
		use ConfigurableTrait;
	}
