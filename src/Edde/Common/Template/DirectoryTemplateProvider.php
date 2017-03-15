<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IDirectory;

	class DirectoryTemplateProvider extends AbstractTemplateProvider {
		/**
		 * @var IDirectory
		 */
		protected $directory;

		/**
		 * The man approached the very beautiful woman in the large supermarket and asked,
		 * “You know, I’ve lost my wife here in the supermarket.
		 * Can you talk to me for a couple of minutes?”
		 * “Why?”
		 * “Because every time I talk to a beautiful woman my wife appears out of nowhere.”
		 *
		 * @param IDirectory $directory
		 */
		public function __construct(IDirectory $directory) {
			$this->directory = $directory;
		}

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name) {
			$preg = sprintf('~^%s\.x(ht)?ml$~', preg_quote($name, '~'));
			foreach ($this->directory as $file) {
				if ($file->match($preg)) {
					return $file;
				}
			}
			return null;
		}
	}
