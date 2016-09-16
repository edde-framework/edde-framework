<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Web\ICompiler;
	use Edde\Common\Resource\ResourceList;

	abstract class AbstractCompiler extends ResourceList implements ICompiler, ILazyInject {
	}
