<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateProvider;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Node\AbstractTreeTraversal;

	abstract class AbstractTemplate extends AbstractTreeTraversal implements ITemplate {
		use ConfigurableTrait;
		use LazyTemplateDirectoryTrait;
		use LazyCryptEngineTrait;
		/**
		 * @var IMacro[]
		 */
		protected $macroList;
		/**
		 * @var ITemplateProvider
		 */
		protected $templateProvider;
		/**
		 * @var IResource[]
		 */
		protected $resourceList;
		/**
		 * @var IFile
		 */
		protected $file;
		protected $id;

		/**
		 * @inheritdoc
		 */
		public function registerMacro(string $name, IMacro $macro): ITemplate {
			$this->macroList[$name] = $macro;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplate {
			$this->templateProvider = $templateProvider;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function import(string $name, IResource $resource): ITemplate {
			$this->resourceList[$name] = $resource;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getMacro(INode $node): IMacro {
			if (isset($this->macroList[$name = $node->getName()]) === false) {
				throw new MacroException(sprintf('Unknown macro [%s] on node [%s].', $name, $node->getPath()));
			}
			return $this->macroList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function traverse(INode $node, ...$parameters): ITreeTraversal {
			$template = reset($parameters);
			return $template->getMacro($node);
		}

		/**
		 * @inheritdoc
		 */
		public function getClass(): string {
			return sprintf('Template_%s', str_replace('-', null, $this->getId()));
		}

		protected function getId() {
			if ($this->id !== null) {
				return $this->id;
			}
			$id = array_keys($this->resourceList);
			asort($id);
			return $this->id = $this->cryptEngine->guid(implode(', ', $id));
		}

		/**
		 * @inheritdoc
		 */
		public function getFile(): IFile {
			if ($this->file === null) {
				$this->file = $this->templateDirectory->file($this->getClass() . '.php');
				$this->file->openForWrite();
			}
			return $this->file;
		}
	}
