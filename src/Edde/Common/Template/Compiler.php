<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
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
		 * @param IFile $source
		 * @param IFile $destination
		 * @param string $name
		 */
		public function __construct(INode $root, IRootDirectory $rootDirectory, IFile $source, IFile $destination, string $name) {
			$this->root = $root;
			$this->rootDirectory = $rootDirectory;
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

		public function compile(): ITemplate {
			$this->use();
			$template = new Template($this->destination);
			$this->destination->enableWriteCache(3);
			$this->destination->write("<?php\n");
			$this->destination->write("\tdeclare(strict_types = 1);\n\n");
			$this->destination->write(sprintf("\t/** source = %s */\n\n", $this->source->getPath()));
			$this->destination->write(sprintf("\tclass %s {\n", $this->name));
			try {
				$this->macro($this->root, $this);
			} catch (CompilerException $e) {
				throw new CompilerException(sprintf('Compilation of template [%s] failed: %s', (string)$this->source->getUrl(), $e->getMessage()), 0, $e);
			}
			$this->destination->write("\t}\n");
			$this->destination->close();
			return $template;
		}

		public function macro(INode $root, ICompiler $compiler) {
			if (isset($this->macroList[$name = $root->getName()]) === false) {
				throw new CompilerException(sprintf('Unknown macro [%s].', $root->getPath()));
			}
			if ($root->hasAttributeList('m')) {
				$attributeList = $root->getAttributeList();
				foreach ($root->getAttributeList('m') as $attribute => $value) {
					/**
					 * m attributes can be changed in $this->macro calls, so it's important to check them every loop
					 */
					$macroAttributeList = $root->getAttributeList('m');
					if (isset($macroAttributeList[$attribute]) === false) {
						continue;
					}
					unset($attributeList['m:' . $attribute]);
					$root->setAttributeList($attributeList);
					$this->macro((new Node('m:' . $attribute, $value))->addNode($root), $this);
				}
				return;
			}
			$this->macroList[$name]->run($root, $this);
		}

		public function value(string $value): string {
			if (strpos($value, '()') !== false) {
				return '$this->' . StringUtils::firstLower(StringUtils::camelize($value));
			}
			if ($value === ':$') {
				return '$item';
			}
			if ($value === ':#') {
				return '$key';
			}
			if (strpos($value, ':$') !== false) {
				return '$item->' . $this->value(str_replace(':$', '', $value));
			}
			if ($value[0] === '$') {
				return substr($value, 1);
			}
			return "'$value'";
		}

		public function file(string $value): string {
			$filename = $this->source->getDirectory()
				->filename($value);
			if ($value[0] === '/') {
				$filename = $this->rootDirectory->filename($value);
			}
			return $filename;
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
