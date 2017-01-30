<?php
	declare(strict_types=1);

	namespace Edde\Common\Xml;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	class XmlExport extends AbstractXmlExport {
		public function export(\Iterator $iterator, IFile $file): IFile {
			$file->open('w+');
			$closeList = [];
			/**
			 * @var $node INode
			 */
			foreach ($iterator as $node) {
				$indentation = str_repeat("\t", $node->getLevel());
				$isClosed = ($node->isLeaf() || $node->getMeta('pair', false));
				if ($isClosed === false) {
					$closeList[] = $indentation . '</' . $node->getName() . ">\n";
				}
				$content = [];
				$content[] = $indentation . '<' . $node->getName();
				foreach ($node->getAttributeList() as $name => $list) {
					$content[] = ' ' . $name . '="' . htmlspecialchars($list, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '"';
				}
				if ($isClosed) {
					$content[] = '/';
				}
				$content[] = ">\n";
				$file->write(implode('', $content));
			}
			foreach (array_reverse($closeList) as $node) {
				$file->write($node);
			}
			$file->close();
			return $file;
		}
	}
