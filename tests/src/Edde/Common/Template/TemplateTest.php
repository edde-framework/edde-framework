<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\AssetsDirectory;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\ContainerControl;
	use Edde\Common\Html\Macro\ControlMacro;
	use Edde\Common\Html\Macro\TemplateMacro;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\TemplateControl;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Link\ControlLinkGenerator;
	use Edde\Common\Link\LinkFactory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class TemplateTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;
		/**
		 * @var \TestDocument
		 */
		protected $control;

		public function testDummy() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/dummy.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body></body>
</html>
', $this->control->render());
		}

		public function testSimpleTemplate() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/simple-template.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div id="first-one" class="simple node"></div>
		<div id="2">
			<div id="3">
				<div id="4" class="hidden one"></div>
			</div>
		</div>
		<div id="5">
			<div id="6">with value</div>
			<div id="7">another value</div>
			<div id="8">
				<div class="another hidden"></div>
			</div>
			<div id="9">foo</div>
		</div>
		<div id="10" class="bunch">
			<span id="11">foobar and so</span>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testSimpleId() {
			$this->expectException(TemplateException::class);
			$this->expectExceptionMessage('Requested unknown control block [2] on [/].');
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/simple-template.xml', $this->control));
			$template->template();
			/** @var $template IHtmlTemplate */
			$template->template();
			$template->snippet('2', $container = new ContainerControl());
		}

		public function testButton() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/button.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=on-update">foo</div>
	</body>
</html>
', $this->control->render());
		}

		public function testSwitchTemplate() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/switch-template.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html attribute="choo" title="poo">
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div id="to-be-switched">
			<div id="bar-id" class="the-second-bar">lorem ipsum</div>
			<div id="dummy-div" class="dummy-div">
				<div id="inner-bar" class="the-second">bar content</div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testSwitch2Template() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/switch2-template.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html attribute="choo" title="poo">
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div>
			<div class="the-second-bar">lorem ipsum</div>
			<div class="dummy-div">
				<div class="the-second">bar content</div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testId() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/id.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="dummy-div">
			<div id="blabla"></div>
			<div id="foo">
				<div class="children-node"></div>
				<span>hello world!</span>
			</div>
			<div class="hidden-button">
				<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo" data-bind="blabla">
					<span>even button has another contorls</span>
				</div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testSchema() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/schema.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div id="blabla" data-schema="Foo\Bar\Schema" data-property="bar">
			<span>value</span>
			<div class="foo">
				<div id="bar"></div>
			</div>
		</div>
		<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo" data-bind="blabla"></div>
	</body>
</html>
', $this->control->render());
		}

		public function testInput() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/input.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<input data-class="Edde.Common.Html.Input.TextControl" type="text" value="" data-schema="poo" data-property="text">
		<input data-class="Edde.Common.Html.Input.PasswordControl" type="password" class="class-here" data-schema="poo" data-property="password">
	</body>
</html>
', $this->control->render());
		}

		public function testSpan() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/span.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="foo">
			<span data-cheat="yep we will cheat!">some spanish span here</span>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testSpanStrangeAttributes() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/span-strange-attributes.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="foo">
			<span data-cheat="I\'m really happy here!">some spanish span here</span>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testInclude() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/include.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="simple-div">
			<div>simply included</div>
		</div>
		<span class="another-included">span here</span>
	</body>
</html>
', $this->control->render());
		}

		public function testRootInclude() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/root-include.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="foo-bar"></div>
		<div>
			<span>lorem ipsum or something like that</span>
		</div>
		<div id="id"></div>
		<div class="qwerty"></div>
		<div>
			<span>foo</span>
			<div>
				<div></div>
				<div></div>
				<div class="soo empty div here"></div>
				<div></div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testMultiInclude() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/multi-include.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div>
			<div>
				<div>
					<div class="include here">
						<div class="simple-div">
							<div>simply included</div>
						</div>
						<span class="another-included">span here</span>
					</div>
				</div>
			</div>
		</div>
		<div class="alone here"></div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop01() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/loop-01.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="looping">
			<div>looped-one</div>
		</div>
		<div>
			<span class="looped-one">value</span>
		</div>
		<div class="looping">
			<div>another-looop</div>
		</div>
		<div>
			<span class="another-looop">value</span>
		</div>
		<div class="another-kind-of-loop">
			<div class="looping2" data-tribute="first">looped-one</div>
			<div class="looping2" data-tribute="second">another-looop</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop02() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/loop-02.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="looping">
			<div class="looped-one">
				<div class="simple-div">
					<div>simply included</div>
				</div>
				<span class="another-included">span here</span>
			</div>
		</div>
		<div class="looping">
			<div class="another-looop">
				<div class="simple-div">
					<div>simply included</div>
				</div>
				<span class="another-included">span here</span>
			</div>
		</div>
		<div class="another-kind-of-loop">
			<div class="looping2" data-tribute="first">looped-one</div>
			<div class="looping2" data-tribute="second">another-looop</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop03() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/loop-03.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button looped-one" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=first"></div>
		<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button another-looop" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=second"></div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop04() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/loop-04.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div>
			<div>looped-one</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>looped-one</div>
		</div>
		<div>
			<div>another-looop</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-looop</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop05() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/loop-05.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div>
			<div>item-value whee</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-item-value whee</div>
		</div>
		<div>
			<div>item-value foo</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-item-value foo</div>
		</div>
		<div>
			<div>item-value poo</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-item-value poo</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testHeader() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/header.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<h1>foo</h1>
		<h2>poo</h2>
		<h3>woo</h3>
		<h4>doo</h4>
		<h5>goo</h5>
		<h6>
			<span>spanish header</span>
		</h6>
	</body>
</html>
', $this->control->render());
		}

		public function testSimplePass() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/test-simple-pass.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="pass-me special-class">
			<span class="foo bar">some span here</span>
		</div>
		<div id="foo" class="poo"></div>
	</body>
</html>
', $this->control->render());
			self::assertNotEmpty($this->control->specialDiv);
			self::assertInstanceOf(DivControl::class, $this->control->specialDiv);
			self::assertContains('special-class', $this->control->specialDiv->getClassList());
		}

		public function testPass() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/pass.xml', $this->control));
			$template->template();
			self::assertNotEmpty($this->control->specialDiv);
			self::assertInstanceOf(DivControl::class, $this->control->specialDiv);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="pass-me special-class">
			<span class="foo bar">some span here</span>
		</div>
		<div class="pass-me">
			<span class="foo bar">some span here</span>
		</div>
		<div class="child-pass">
			<span class="first special-span-class"></span>
			<span class="second special-span-class"></span>
			<span class="third special-span-class">
				<div class="with internal div"></div>
			</span>
		</div>
		<div class="child-pass">
			<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button first special-button-class" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo"></div>
			<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button second special-button-class" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo"></div>
			<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button third special-button-class" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo"></div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testCustomControl() {
			$control = $this->container->create(TemplateControl::class);
			$control->setTemplate(__DIR__ . '/assets/template/custom.xml');
			$control->dirty();
			self::assertEquals('	<div class="will-use-custom-control">
			<div class="hello" attr="foo">custom control</div>
	</div>
', $control->render());
		}

		public function testRequire() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/require.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="foo-bar"></div>
		<div>
			<span>lorem ipsum or something like that</span>
		</div>
		<div id="id"></div>
		<div class="qwerty"></div>
		<div>
			<span>foo</span>
			<div>
				<div></div>
				<div></div>
				<div class="soo empty div here"></div>
				<div></div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testLayout() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/layout.xml', $this->control));
			$template->import(__DIR__ . '/assets/template/require.xml');
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="foo"></div>
		<div class="with-block">
			<div>
				<span>lorem ipsum or something like that</span>
			</div>
			<div class="bar"></div>
		</div>
		<div id="id"></div>
		<div id="id"></div>
		<div class="qwerty"></div>
		<div>
			<span>foo</span>
			<div>
				<div></div>
				<div></div>
				<div class="soo empty div here"></div>
				<div></div>
			</div>
		</div>
		<div class="something"></div>
	</body>
</html>
', $this->control->render());
		}

		public function testSnippet() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/snippet.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body></body>
</html>
', $this->control->render());
			self::assertEmpty($this->control->snippy);
			$template->snippet('some-snippet-name', $this->control);
			self::assertNotEmpty($this->control->snippy);
			self::assertInstanceOf(DivControl::class, $this->control->snippy);
			$this->control->invalidate();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div id="moo" class="simple-div"></div>
	</body>
</html>
', $this->control->render());
			self::assertEquals('		<div id="moo" class="simple-div"></div>
', $this->control->snippy->render());
		}

		public function testComplexId() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/complex-id.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="a-little-bit-hidden-snippet"></div>
		<div class="edde-placeholder" id="message"></div>
	</body>
</html>
', $this->control->render());
			$template->snippet('message', $this->control);
			self::assertNotEmpty($this->control->message);
			self::assertInstanceOf(DivControl::class, $this->control->message);
			self::assertEquals($expect = '		<div class="alert"></div>
', $this->control->message->render());
			$this->control->dirty(false);
			$this->control->message->dirty();
			self::assertCount(1, $snippetList = $this->control->invalidate());
			$message = reset($snippetList);
			self::assertInstanceOf(DivControl::class, $message);
			self::assertEquals($expect, $message->render());
		}

		public function testTable() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/table.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<table>
			<tr>
				<th>head</th>
				<td>row</td>
			</tr>
			<tr>
				<td class="foo">td here</td>
			</tr>
		</table>
		<table class="full featured">
			<caption class="this should be a method on a table"></caption>
			<colgroup>
				<col class="foo" span="2">
			</colgroup>
			<thead>
				<tr>
					<th>foo</th>
					<td>bar</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>bar</td>
					<td>foo</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td>foot here</td>
					<td>boo here</td>
				</tr>
			</tfoot>
		</table>
	</body>
</html>
', $this->control->render());
		}

		public function testRootDefine() {
			/** @var $template IHtmlTemplate */
			self::assertInstanceOf(IHtmlTemplate::class, $template = $this->templateManager->template(__DIR__ . '/assets/template/root-define.xml', $this->control));
			$template->template();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div>
			<span>boo</span>
		</div>
	</body>
</html>
', $this->control->render());
			$template->snippet('foo', $this->control);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div>
			<span>boo</span>
		</div>
		<div>
			<span>boo</span>
		</div>
	</body>
</html>
', $this->control->render());
		}

		protected function setUp() {
			$this->container = $container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				ITemplateDirectory::class => function () {
					return new TemplateDirectory(__DIR__ . '/temp');
				},
				IRootDirectory::class => function () {
					return new RootDirectory(__DIR__);
				},
				ITempDirectory::class => function () {
					return new TempDirectory(__DIR__ . '/temp');
				},
				IAssetsDirectory::class => function (IRootDirectory $rootDirectory) {
					return $rootDirectory->directory('assets', AssetsDirectory::class);
				},
				\TestDocument::class,
				ICryptEngine::class => CryptEngine::class,
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
				ITemplateManager::class => TemplateManager::class,
				IXmlParser::class => XmlParser::class,
				ILinkFactory::class => function () {
					$linkFactory = new LinkFactory($hostUrl = HostUrl::create('https://127.0.0.1/foo?param=foo'));
					$linkFactory->registerLinkGenerator($controlLinkGenerator = new ControlLinkGenerator());
					$controlLinkGenerator->lazyHostUrl($hostUrl);
					return $linkFactory;
				},
			]);
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->create(XmlConverter::class));
			$this->templateManager = $this->container->create(ITemplateManager::class);
			$this->templateManager->onSetup(function (ITemplateManager $templateManager) {
				$templateManager->registerMacroList(TemplateMacro::macroList($this->container));
				$templateManager->registerMacroList([new ControlMacro('custom-control', \CustomControl::class)]);
			});
			$this->control = $this->container->create(\TestDocument::class);
		}
	}

