<?php
	namespace Edde\Common\ContainerTest;

	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\AbstractCacheStorage;
	use Edde\Common\Container\LazyInjectTrait;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class AlphaDependencyClass {
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class BetaDependencyClass {
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class TestCommonClass {
		private $foo;
		private $bar;
		private $cloned = false;

		public function __construct($foo, $bar) {
			$this->foo = $foo;
			$this->bar = $bar;
		}

		public function getFoo() {
			return $this->foo;
		}

		public function getBar() {
			return $this->bar;
		}

		public function isCloned() {
			return $this->cloned;
		}

		public function __clone() {
			$this->cloned = true;
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class RecursiveClass {
		public function __construct(RecursiveClass $recursiveClass) {
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class TestMagicFactory {
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class MagicFactory {
		private $flag = false;

		public function hasFlag() {
			return $this->flag;
		}

		public function __invoke() {
			$this->flag = true;
			return new TestMagicFactory();
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class DummyCacheStorage extends AbstractCacheStorage {
		public function save($id, $save) {
		}

		public function load($id) {
		}

		public function invalidate() {
		}

		protected function prepare() {
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class LazyInjectTraitClass extends AbstractObject {
		use LazyInjectTrait;

		/**
		 * @var BetaDependencyClass
		 */
		protected $betaDependencyClass;

		/**
		 * @param BetaDependencyClass $betaDependencyClass
		 */
		public function lazyDependency(BetaDependencyClass $betaDependencyClass) {
			$this->betaDependencyClass = $betaDependencyClass;
		}

		public function foo() {
			$this->lazyEnablePropertyBypass('betaDependencyClass');
			return $this->betaDependencyClass;
		}
	}
