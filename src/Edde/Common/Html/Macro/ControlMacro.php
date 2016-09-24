<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\File\HomeDirectoryTrait;
	use Edde\Common\Html\AbstractHtmlTemplate;

	/**
	 * Root control macro for template generation.
	 */
	class ControlMacro extends AbstractHtmlMacro {
		use HomeDirectoryTrait;

		/**
		 * Base 8 is just like base 10, if you are missing two fingers.
		 */
		public function __construct() {
			parent::__construct('control', false);
		}

		/**
		 * Execute template generation; this is "entry point" macro for template support over html controls.
		 *
		 * @return IFile
		 */
		public function onMacro() {
			if ($this->macro->isRoot() === false || $this->macro->getMeta('included', false)) {
				$this->compile();
				return null;
			}
			$this->use();
			$this->compiler->setVariable('file', $file = $this->homeDirectory->file(($class = 'Template_' . $this->compiler->getVariable('name')) . '.php'));
			$file->openForWrite();
			$file->enableWriteCache();
			$this->write('<?php');
			$this->write("declare(strict_types = 1);\n", 1);
			$this->write('/**', 1);
			$this->write(sprintf(' * @generated at %s', (new \DateTime())->format('Y-m-d H:i:s')), 1);
			$this->write(' * automagically generated template file from the following source list:', 1);
			/** @var $nameList array */
			$nameList = $this->compiler->getVariable('name-list', []);
			foreach ($nameList as $name) {
				$this->write(sprintf(' *   - %s', $name), 1);
			}
			$this->write(' */', 1);
			$this->write(sprintf('class %s extends %s {', $class, AbstractHtmlTemplate::class), 1);
			$this->write(sprintf("public function snippet(%s \$root, string \$snippet = null): %s {", IHtmlControl::class, IHtmlControl::class), 2);
			$this->write(sprintf("\$stack = new SplStack();
			\$stack->push(\$control = \$parent = \$root);
			switch (\$snippet) {
				case null:", TemplateException::class), 3);
			$this->writeTextValue();
			$this->writeAttributeList();
			foreach ($this->macro->getNodeList() as $node) {
				if ($node->getMeta('snippet', false)) {
					continue;
				}
				$this->compiler->runtimeMacro($node);
			}
			$this->write('break;', 5);
			$caseList = $this->compiler->getVariable($caseListId = (static::class . '/cast-list'), [null => null]);
			/** @var $nodeList INode[] */
			foreach ($this->compiler->getBlockList() as $id => $nodeList) {
				if (isset($caseList[$id])) {
					continue;
				}
				$caseList[$id] = $id;
				/** @noinspection DisconnectedForeachInstructionInspection */
				$this->compiler->setVariable($caseListId, $caseList);
				$this->write(sprintf('case %s:', var_export($id, true)), 4);
				foreach ($nodeList as $node) {
					$this->write(sprintf('// %s', $node->getPath()), 5);
					$this->compiler->runtimeMacro($node);
				}
				$this->write('break;', 5);
			}
			$this->write(sprintf("default:
					throw new %s(sprintf('Requested unknown snippet [%%s].', \$snippet));
			}", TemplateException::class), 4);
			$this->write("return \$root;", 3);
			$this->write('}', 2);
			$this->write('');
			$this->write('public function getBlockList(): array {', 2);
			unset($caseList[null]);
			$this->write('return ' . var_export(array_keys($caseList), true) . ';', 3);
			$this->write('}', 2);
			$this->write('}', 1);
			$file->close();
			return $file;
		}

		protected function prepare() {
			$this->home('.template');
		}
	}
