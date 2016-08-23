<?php
	declare(strict_types = 1);

	use Edde\Common\Filter\GuidFilter;

	return [
		'name' => 'Foo',
		'property-list' => [
			[
				'name' => 'guid',
				'generator' => GuidFilter::class,
			],
		],
	];
