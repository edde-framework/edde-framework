<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Abstract class for all html package based macros.
	 */
	abstract class AbstractHtmlMacro extends AbstractMacro {
		protected function writeTextValue() {
			if (($value = $this->extract($this->macro, 'value', $this->macro->isLeaf() ? $this->macro->getValue() : null)) !== null) {
				$this->write(sprintf('$control->setText(%s);', ($helper = $this->compiler->helper($value)) ? $helper : var_export($value, true)), 5);
			}
		}

		protected function write(string $write, int $indents = null) {
			/** @var $file IFile */
			$file = $this->compiler->getVariable('file');
			$file->write(($indents ? str_repeat("\t", $indents) : '') . $write . "\n");
		}

		protected function writeAttributeList() {
			$attributeList = $this->getAttributeList(function ($value) {
				return var_export($value, true);
			});
			if (empty($attributeList) === false) {
				$attributes = [];
				foreach ($attributeList as $k => $v) {
					$attributes[] = var_export($k, true) . ' => ' . $v;
				}
				$this->write(sprintf('$control->setAttributeList([%s]);', implode(', ', $attributes)), 5);
			}
		}

		protected function compile() {
			foreach ($this->macro->getNodeList() as $node) {
				$this->compiler->runtimeMacro($node);
			}
		}
	}
