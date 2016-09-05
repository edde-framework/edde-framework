<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\CompilerException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Node\Node;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Usable\AbstractUsable;

	class Compiler extends AbstractUsable implements ICompiler {
		/**
		 * @var INode
		 */
		protected $root;
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;
		/**
		 * @var IAssetsDirectory
		 */
		protected $assetsDirectory;
		/**
		 * @var IFile
		 */
		protected $source;
		/**
		 * @var IFile
		 */
		protected $destination;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];

		/**
		 * @param INode $root
		 * @param IRootDirectory $rootDirectory
		 * @param IAssetsDirectory $assetsDirectory
		 * @param IFile $source
		 * @param IFile $destination
		 * @param string $name
		 */
		public function __construct(INode $root, IRootDirectory $rootDirectory, IAssetsDirectory $assetsDirectory, IFile $source, IFile $destination, string $name) {
			$this->root = $root;
			$this->rootDirectory = $rootDirectory;
			$this->assetsDirectory = $assetsDirectory;
			$this->source = $source;
			$this->destination = $destination;
			$this->name = $name;
		}

		public function registerMacro(IMacro $macro): ICompiler {
			$this->macroList[] = $macro;
			return $this;
		}

		public function registerMacroList(array $macroList): ICompiler {
			$this->macroList = array_merge($this->macroList, $macroList);
			return $this;
		}

		public function getSource(): IFile {
			return $this->source;
		}

		public function getDestination(): IFile {
			return $this->destination;
		}

		public function getName(): string {
			return $this->name;
		}

		public function compile(): ITemplate {
			$this->use();
			$template = new Template($this->destination);
			$this->destination->enableWriteCache(3);
			try {
				$this->macro($this->root, $this->root);
			} catch (CompilerException $e) {
				throw new CompilerException(sprintf('Compilation of template [%s] failed: %s', (string)$this->source->getUrl(), $e->getMessage()), 0, $e);
			}
			$this->destination->close();
			return $template;
		}

		public function macro(INode $macro, INode $element) {
			if (isset($this->macroList[$name = $macro->getName()]) === false) {
				throw new CompilerException(sprintf('Unknown macro [%s] in [%s].', $macro->getName(), $element->getPath()));
			}
			if ($macro->hasAttributeList('m')) {
				$attributeList = $macro->getAttributeList();
				foreach ($macro->getAttributeList('m') as $attribute => $value) {
					/**
					 * m attributes can be changed in $this->macro calls, so it's important to check them every loop
					 */
					$macroAttributeList = $macro->getAttributeList('m');
					if (isset($macroAttributeList[$attribute]) === false) {
						continue;
					}
					unset($attributeList['m:' . $attribute]);
					$macro->setAttributeList($attributeList);
					$this->macro(new Node('m:' . $attribute, $value), $element);
				}
				return;
			}
			$this->macroList[$name]->macro($macro, $element, $this);
		}

		public function delimite(string $value): string {
			foreach ($this->macroList as $macro) {
				if (($item = $macro->variable($value, $this)) !== null) {
					return $item;
				}
			}
			if (strpos($value, 'edde://', 0) !== false) {
				return var_export($this->asset(str_replace('edde://', '', $value)), true);
			}
			if (strpos($value, '/', 0) !== false) {
				return var_export($this->file(substr($value, 1)), true);
			}
			if (strpos($value, '->', 0) !== false && strpos($value, '()') !== false) {
				return '->' . StringUtils::firstLower(StringUtils::camelize(substr($value, 2)));
			}
			if (strpos($value, '()') !== false) {
				return '$this->' . StringUtils::firstLower(StringUtils::camelize($value));
			}
			if ($value[0] === '$') {
				return $value;
			}
			return var_export($value, true);
		}

		public function asset(string $asset): string {
			return $this->assetsDirectory->filename($asset);
		}

		public function file(string $file): string {
			if ($file[0] === '/') {
				return $this->rootDirectory->filename($file);
			}
			return $this->source->getDirectory()
				->filename($file);
		}

		protected function prepare() {
			$macroList = $this->macroList;
			$this->macroList = [];
			foreach ($macroList as $macro) {
				foreach ($macro->getMacroList() as $name) {
					$this->macroList[$name] = $macro;
				}
			}
		}
	}
