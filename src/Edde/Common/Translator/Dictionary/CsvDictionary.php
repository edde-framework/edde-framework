<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator\Dictionary;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\File\CsvFile;
	use Edde\Common\Translator\AbstractDictionary;

	class CsvDictionary extends AbstractDictionary implements ILazyInject {
		use LazyResourceManagerTrait;
		use CacheTrait;
		/**
		 * @var IFile[]
		 */
		protected $fileList = [];
		/**
		 * @var string[]
		 */
		protected $dictionary;

		public function addFile(string $file) {
			$this->fileList[$file] = new CsvFile($file);
			return $this;
		}

		protected function prepare() {
			$this->cache();
			if (($this->translationList = $this->cache->load($cacheId = implode(',', array_keys($this->fileList)))) === null) {
				foreach ($this->fileList as $file) {
					$file->open('r');
					/** @var $langList array */
					$langList = $file->read();
					array_shift($langList);
					$iterator = new \IteratorIterator($file);
					$iterator->rewind();
					$iterator->next();
					while ($iterator->valid() && $line = $iterator->current()) {
						$id = array_shift($line);
						foreach ($langList as $i => $lang) {
							$this->translationList[$lang][$id] = $line[$i];
						}
						$iterator->next();
					}
				}
				$this->cache->save($cacheId, $this->translationList);
			}
		}
	}
