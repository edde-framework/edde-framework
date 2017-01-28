<?php
	declare(strict_types=1);

	namespace Edde\Common\Xml;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	class XmlExport extends AbstractXmlExport {
		public function export(\Iterator $iterator, IFile $file): IFile {
			$file->open('w+');
			$level = -1;
			/**
			 * @var $node INode
			 */
			foreach ($iterator as $node) {
				$content = [];
				$content[] = $indentation = str_repeat("\t", $node->getLevel());
				$content[] = '<' . $node->getName();
				foreach ($node->getAttributeList() as $name => $list) {
					$content[] = ' ' . $name . '="' . htmlspecialchars($list, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '"';
				}
				if (($isPair = $node->getMeta('pair'))) {
					$content[] = '>';
					$newline = "\n";
					if (($value = $node->getValue()) !== null) {
						$newline = null;
						$indentation = null;
						$content[] = htmlspecialchars($value, ENT_XML1, 'UTF-8');
					}
					if ($node->isLeaf() && $isPair === true) {
						$newline = null;
						$indentation = null;
					}
					$content[] = $newline;
					if ($isPair && $node->getLevel() < $level) {
						$content[] = $indentation . '</' . $node->getName() . ">\n";
					}
				} else {
					$content[] = '/>';
				}
				$level = $node->getLevel();
				$file->write(implode('', $content));
			}
			$file->close();
			return $file;
		}
	}
