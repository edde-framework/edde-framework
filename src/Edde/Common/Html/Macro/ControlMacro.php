<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\File\HomeDirectoryTrait;
	use Edde\Common\Html\AbstractHtmlTemplate;
	use Edde\Common\Node\NodeIterator;

	/**
	 * Root control macro for template generation.
	 */
	class ControlMacro extends AbstractHtmlMacro {
		use HomeDirectoryTrait;

		public function __construct() {
			parent::__construct('control', false);
		}

		public function onMacro() {
			if ($this->macro->isRoot() === false) {
				$this->compile();
				return null;
			}
			$this->use();
			$source = $this->compiler->getSource();
			$this->compiler->setVariable('file', $file = $this->homeDirectory->file(($class = 'Template_' . sha1($source->getPath())) . '.php'));
			$file->openForWrite();
			$file->enableWriteCache();
			$this->write('<?php');
			$this->write("declare(strict_types = 1);\n", 1);
			$this->write(sprintf('class %s extends %s {', $class, AbstractHtmlTemplate::class), 1);
			$this->write(sprintf("public function snippet(%s \$root, string \$snippet = null): %s {", IHtmlControl::class, IHtmlControl::class), 2);
			$this->write(sprintf("\$stack = new SplStack();
			\$stack->push(\$parent = \$root);
			switch (\$snippet) {
				case null:", TemplateException::class), 3);
			$this->compile();
			$this->write('break;', 5);
			$caseList = [null];

			foreach (NodeIterator::recursive($this->macro) as $node) {
				if (($id = $node->getMeta('id')) === null) {
					continue;
				}
				if (isset($caseList[$id])) {
					continue;
				}
				$caseList[$id] = $id;
				$this->write(sprintf('case %s:', var_export($id, true)), 4);
				$this->write(sprintf('// %s', $node->getPath()), 5);
				$this->compiler->runtimeMacro($node);
				$this->write('break;', 5);
			}
			foreach ($this->compiler->getVariable('block-list', []) as $nodeList) {
				foreach ($nodeList as $node) {
					if (isset($caseList[$id = $node->getMeta('id')])) {
						continue;
					}
					$caseList[$id] = $id;
					$this->write(sprintf('case %s:', var_export($id, true)), 4);
					$this->write(sprintf('// %s', $node->getPath()), 5);
					$this->compiler->runtimeMacro($node);
					$this->write('break;', 5);
				}
			}
			$this->write(sprintf("default:
					throw new %s(sprintf('Requested unknown snippet [%%s].', \$snippet));
			}", TemplateException::class), 4);
			$this->write("return \$root;", 3);
			$this->write('}', 2);
			$this->write('}', 1);
			$file->close();
			return $file;
		}

		protected function prepare() {
			$this->home('.template');
		}
	}
