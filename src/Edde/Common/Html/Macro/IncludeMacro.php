<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Template\AbstractMacro;

	class IncludeMacro extends AbstractMacro {
		use LazyInjectTrait;

		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		public function __construct() {
			parent::__construct([
				'include',
				'm:include',
			]);
		}

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'include':
					$this->checkAttribute($macro, $element, 'src');
					$destination->write(sprintf("\t\t\t/** include %s */\n", $include = $compiler->file($macro->getAttribute('src'))));
					$this->resourceManager->file($include, null, $element);
					$this->element($element, $compiler);
					$destination->write(sprintf("\t\t\t/** done %s */\n", $include));
					break;
				case 'm:include':
					$this->checkValue($macro, $element);
					$include = $compiler->file($macro->getValue());
					$destination->write(sprintf("\t\t\t/** include %s */\n", $include));
					$this->resourceManager->file($include, null, $element);
					$compiler->macro($element, $element);
					$destination->write(sprintf("\t\t\t/** done %s */\n", $include));
					break;
			}
		}
	}
