<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Abstract class for all html package based macros.
	 */
	abstract class AbstractHtmlMacro extends AbstractMacro {
		static protected $reference = [
			':' => '$control->getRoot()',
			'.' => '$root',
			'@' => '$control',
		];

		/**
		 * write (export) text value from macro node
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		protected function writeTextValue(INode $macro, ICompiler $compiler) {
			if (($value = $this->extract($macro, 'value', $macro->isLeaf() ? $macro->getValue() : null)) !== null) {
				$this->write($compiler, sprintf('$control->setText(%s);', ($helper = $compiler->helper($value)) ? $helper : var_export($value, true)), 5);
			}
		}

		/**
		 * shortcut for file write
		 *
		 * @param ICompiler $compiler
		 * @param string $write
		 * @param int|null $indents
		 */
		protected function write(ICompiler $compiler, string $write, int $indents = null) {
			/** @var $file IFile */
			$file = $compiler->getVariable('file');
			$file->write(($indents ? str_repeat("\t", $indents) : '') . $write . "\n");
		}

		/**
		 * export attribute list
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		protected function writeAttributeList(INode $macro, ICompiler $compiler) {
			$attributeList = $this->getAttributeList($macro, $compiler, function ($value) {
				return var_export($value, true);
			});
			if (empty($attributeList) === false) {
				$attributes = [];
				foreach ($attributeList as $k => $v) {
					$attributes[] = var_export($k, true) . ' => ' . $v;
				}
				$this->write($compiler, sprintf('$control->setAttributeList([%s]);', implode(', ', $attributes)), 5);
			}
		}
	}
