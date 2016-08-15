<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Strings\StringUtils;

	abstract class AbstractMacro extends AbstractObject implements IMacro {
		use LazyInjectTrait;
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * @var array
		 */
		protected $macroList = [];

		/**
		 * @param array $macroList
		 */
		public function __construct(array $macroList) {
			$this->macroList = $macroList;
		}

		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function getMacroList(): array {
			return $this->macroList;
		}

		protected function macro(INode $root, ITemplateManager $templateManager, ITemplate $template, IFile $file, ...$parameterList) {
			foreach ($root->getNodeList() as $node) {
				$templateManager->macro($node, $template, $file, ...$parameterList);
			}
		}

		protected function value($value) {
			if (strpos($value, '()') !== false) {
				return '$this->' . StringUtils::firstLower(StringUtils::camelize($value));
			}
			return "'$value'";
		}

		protected function file(string $value, IFile $file) {
			$filename = $file->getDirectory()
				->filename($value);
			if ($value[0] === '/') {
				$filename = $this->rootDirectory->filename($value);
			}
			return $filename;
		}
	}
