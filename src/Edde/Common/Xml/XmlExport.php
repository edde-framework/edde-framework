<?php
	declare(strict_types=1);

	namespace Edde\Common\Xml;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	class XmlExport extends AbstractXmlExport {
		public function export(\Iterator $iterator, IFile $file): IFile {
			$file->open('w+');
			$stack = new \SplStack();
			$level = -1;
			/**
			 * @var $node INode
			 */
			foreach ($iterator as $node) {
				$value = null;
				if ($node->getLevel() < $level) {
					$file->write($stack->pop());
				}
				$indentation = str_repeat("\t", $node->getLevel());
				$attributeList = $node->getAttributeList();
				$metaList = $node->getMetaList();
				$isClosed = (($value = $node->getValue()) === null) && ($node->isLeaf() || $metaList->get('pair', false));
				$close = '</' . $node->getName() . ">\n";
				if ($isClosed === false && ($node->isLeaf() === false || $value === null)) {
					$stack->push($indentation . $close);
				}
				$content = [];
				$content[] = $indentation . '<' . $node->getName();
				foreach ($node->getAttributeList() as $name => $list) {
					$content[] = ' ' . $name . '="' . htmlspecialchars($list, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '"';
				}
				if ($isClosed) {
					$content[] = '/';
				}
				$content[] = '>' . (($value && $node->isLeaf()) ? '' : "\n");
				$file->write(implode('', $content));
				if ($value && $node->isLeaf()) {
					$file->write($value);
				}
				if ($isClosed === false && $value && $node->isLeaf()) {
					$file->write($close);
				}
				$level = $node->getLevel();
			}
			while ($stack->isEmpty() === false) {
				$file->write($stack->pop());
			}
			$file->close();
			return $file;
		}
	}
