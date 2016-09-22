<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\File\HomeDirectoryTrait;
	use Edde\Common\Html\AbstractHtmlTemplate;

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
			$this->compiler->setVariable('file', $file = $this->homeDirectory->file(($class = 'Template_' . $this->compiler->getVariable('name')) . '.php'));
			$file->openForWrite();
			$file->enableWriteCache();
			$this->write('<?php');
			$this->write("declare(strict_types = 1);\n", 1);
			$this->write('/**', 1);
			$this->write(sprintf(' * @generated at %s', (new \DateTime())->format('Y-m-d H:i:s')), 1);
			$this->write(' * automagically generated template file from the following source list:', 1);
			foreach ($this->compiler->getVariable('name-list') as $name) {
				$this->write(sprintf(' *   - %s', $name), 1);
			}
			$this->write(' */', 1);
			$this->write(sprintf('class %s extends %s {', $class, AbstractHtmlTemplate::class), 1);
			$this->write(sprintf("public function snippet(%s \$root, string \$snippet = null): %s {", IHtmlControl::class, IHtmlControl::class), 2);
			$this->write(sprintf("\$stack = new SplStack();
			\$stack->push(\$parent = \$root);
			switch (\$snippet) {
				case null:", TemplateException::class), 3);
			$this->compile();
			$this->write('break;', 5);
			$caseList = [null];
			foreach ($this->compiler->getVariable('block-list', []) as $id => $nodeList) {
				if (isset($caseList[$id])) {
					continue;
				}
				$caseList[$id] = $id;
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
			$this->write('}', 1);
			$file->close();
			return $file;
		}

		protected function prepare() {
			$this->home('.template');
		}
	}
