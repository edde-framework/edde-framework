<?php
	namespace Edde\Common\Query;

	use Edde\Api\Query\IStaticQuery;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Ext\Query\SqlQueryFactory;
	use phpunit\framework\TestCase;

	class QueryTest extends TestCase {
		public function testSimpleSelect() {
			$selectQuery = new SelectQuery();
			$selectQuery->select()
				->property('a')
				->from()
				->source('foo');
			$sqlQueyrFactory = new SqlQueryFactory();
			$staticQuery = $sqlQueyrFactory->create($selectQuery);
			$node = new Node('select-query');
			$node->addNodeList([
				(new Node('select'))->addNode(new Node('property', 'a', [
					'alias' => null,
					'prefix' => null,
				])),
				(new Node('from'))->addNode(new Node('source', 'foo', [
					'alias' => null,
				])),
				new Node('where'),
			]);
			/**
			 * this fucking foreach must be here because every node has set level, so it was failing
			 */
			foreach (NodeIterator::recursive($node) as $n) {
				$n->getLevel();
			}
			self::assertEquals($node, $selectQuery->getNode());
			self::assertInstanceOf(IStaticQuery::class, $staticQuery);
			self::assertEmpty($staticQuery->getParameterList());
			self::assertEquals('SELECT "a" FROM "foo"', $staticQuery->getQuery());
		}
	}
