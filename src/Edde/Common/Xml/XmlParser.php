<?php
	declare(strict_types = 1);

	namespace Edde\Common\Xml;

	use Edde\Api\Iterator\IIterator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Xml\IXmlHandler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Api\Xml\XmlParserException;
	use Edde\Common\AbstractObject;
	use Edde\Common\File\File;
	use Edde\Common\Iterator\ChunkIterator;
	use Edde\Common\Iterator\Iterator;
	use Edde\Common\Strings\StringUtils;

	class XmlParser extends AbstractObject implements IXmlParser {
		const XML_TYPE_WARP = null;
		const XML_TYPE_OPENTAG = 1;
		const XML_TYPE_CLOSETAG = 2;
		const XML_TYPE_SHORTTAG = 4;
		const XML_TYPE_DOCTYPE = 8;
		const XML_TYPE_CDATA = 16;
		const XML_TYPE_COMMENT = 32;
		const XML_TYPE_OPEN_COMMENT = 64;
		const XML_TYPE_CLOSE_COMMENT = 128;

		public function file(string $file, IXmlHandler $xmlHandler): IXmlParser {
			return $this->parse(new File($file), $xmlHandler);
		}

		public function parse(IResource $resource, IXmlHandler $xmlHandler): IXmlParser {
			$value = '';
			foreach ($iterator = new Iterator(new ChunkIterator([
				StringUtils::class,
				'createIterator',
			], $resource->getIterator())) as $char) {
				switch ($char) {
					case '<':
						if ($value !== '') {
							$xmlHandler->onTextEvent($value);
						}
						$this->parseTag($iterator->setContinue(), $xmlHandler);
						$value = '';
						break;
					default:
						$value .= $char;
				}
			}
			return $this;
		}

		protected function parseTag(IIterator $iterator, IXmlHandler $xmlHandler) {
			$last = null;
			$name = '';
			$attributeList = [];
			$type = self::XML_TYPE_WARP;
			foreach ($iterator as $char) {
				switch ($char) {
					case '<':
						$type = self::XML_TYPE_OPENTAG;
						$name = '';
						break;
					case '!':
						if ($last !== '<') {
							throw new XmlParserException(sprintf('Unexpected token [%s] while reading open tag.', $char));
						}
						$type = self::XML_TYPE_DOCTYPE;
						$name .= $char;
						break;
					case '-':
						switch ($type) {
							case self::XML_TYPE_DOCTYPE:
								$type = self::XML_TYPE_OPEN_COMMENT;
								break;
							case self::XML_TYPE_OPEN_COMMENT:
								$iterator->next();
								$this->parseComment($iterator->setContinue());
								$name = null;
								break;
							default:
								$name .= $char;
						}
						break;
					case '/':
						$type = ($last !== '<' ? self::XML_TYPE_SHORTTAG : self::XML_TYPE_CLOSETAG);
						break;
					case "\n":
					case ' ':
						if ($type === self::XML_TYPE_OPENTAG) {
							$attributeList = $this->parseAttributes($iterator->setContinue());
							break;
						}
						$name .= $char;
						break;
					case '>':
						switch ($type) {
							case self::XML_TYPE_DOCTYPE:
								$xmlHandler->onDocTypeEvent($name);
								break;
							case self::XML_TYPE_OPENTAG:
								$xmlHandler->onOpenTagEvent($name, $attributeList);
								break;
							case self::XML_TYPE_SHORTTAG:
								$xmlHandler->onShortTagEvent($name, $attributeList);
								break;
							case self::XML_TYPE_CLOSETAG:
								$xmlHandler->onCloseTagEvent($name);
								break;
						}
						return;
					default:
						$name .= $char;
				}
				$last = $char;
			}
		}

		protected function parseComment(IIterator $iterator) {
			$type = self::XML_TYPE_COMMENT;
			$close = false;
			foreach ($iterator as $char) {
				switch ($char) {
					case '-':
						switch ($type) {
							case self::XML_TYPE_COMMENT:
								$type = self::XML_TYPE_CLOSE_COMMENT;
								break;
							case self::XML_TYPE_CLOSE_COMMENT:
								$close = true;
								break;
						}
						break;
					case '>':
						if ($close) {
							return;
						}
						break;
					default:
						$close = false;
						$type = self::XML_TYPE_COMMENT;
				}
			}
		}

		protected function parseAttributes(IIterator $iterator) {
			$attributeList = [];
			foreach ($iterator as $char) {
				switch ($char) {
					case '/':
						$iterator->setSkipNext();
						return $attributeList;
					case '>':
						$iterator->setSkipNext();
						return $attributeList;
					case "\n":
					case "\t":
					case ' ':
						continue 2;
					default:
						$attributeList = array_merge($attributeList, $this->parseAttribute($iterator->setContinue()));
				}
			}
			return $attributeList;
		}

		protected function parseAttribute(IIterator $iterator) {
			$name = null;
			$open = false;
			$quote = null;
			$value = null;
			foreach ($iterator as $char) {
				switch ($char) {
					case '=':
						if ($open !== true) {
							$open = true;
							break;
						}
						$value .= $char;
						break;
					case '"':
					case "'":
						if ($char === $quote) {
							$iterator->next();
							$iterator->setSkipNext();
							return [$name => $value];
						}
						if ($quote !== null) {
							$value .= $char;
							break;
						}
						$quote = $char;
						break;
					case "\t":
					case "\n":
					case ' ':
						if ($open) {
							$value .= $char;
						}
						continue 2;
					default:
						if ($open) {
							$value .= $char;
						} else {
							$name .= $char;
						}
				}
			}
			$iterator->setSkipNext();
			return [];
		}
	}
