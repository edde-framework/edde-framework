<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\ICompiler;
	use Edde\Common\AssetsDirectory;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Node\Node;
	use phpunit\framework\TestCase;

	class CompilerTest extends TestCase {
		/**
		 * @var ICompiler
		 */
		protected $compiler;

		public function testDelimite() {
			self::assertEquals("'foo'", $this->compiler->delimite('foo'));
			self::assertEquals("'fo\\'o'", $this->compiler->delimite("fo'o"));
			self::assertEquals('$this->methodCall()', $this->compiler->delimite('method-call()'));
			self::assertEquals('$this->methodCall(', $this->compiler->delimite('method-call('));
			self::assertEquals('$simpleVariable', $this->compiler->delimite('$simple-variable'));
			self::assertEquals('->someCall()', $this->compiler->delimite('->some-call()'));
			self::assertEquals("'foo boo (a'", $this->compiler->delimite('foo boo (a'));
			self::assertEquals("'foo boo ('", $this->compiler->delimite('foo boo ('));
			self::assertEquals('->someCall(', $this->compiler->delimite('->some-call('));
			self::assertEquals('->someCall2', $this->compiler->delimite('->some-call2'));
			self::assertEquals('->fooVariableHere', $this->compiler->delimite('->foo-variable-here'));
			self::assertEquals("'foo/boo'", $this->compiler->delimite('foo/boo'));
			self::assertEquals("'" . FileUtils::normalize(__DIR__ . '/assets/template/button.xml') . "'", $this->compiler->delimite('edde://button.xml'));
			self::assertEquals("'" . FileUtils::normalize(__DIR__ . '/assets/template/id.xml') . "'", $this->compiler->delimite('./assets/template/id.xml'));
			self::assertEquals("'" . FileUtils::normalize(__DIR__ . '/assets/assets.php') . "'", $this->compiler->delimite('/assets/assets.php'));
		}

		protected function setUp() {
			$this->compiler = new Compiler(new Node(), new RootDirectory(__DIR__), new AssetsDirectory(__DIR__ . '/assets/template'), new File(__FILE__), new File(__DIR__ . '/foo'), 'foo');
		}
	}
