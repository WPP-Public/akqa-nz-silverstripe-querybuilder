# SilverStripe Query Builder

Provides a way to create queries that are flexible and reusable in SilverStripe. A lot like Search Filters.

## Installation (with composer)

Installing from composer is easy,

Create or edit a `composer.json` file in the root of your SilverStripe project, and make sure the following is present.

```json
{
    "require": {
        "heyday/silverstripe-querybuilder": "1.2.*"
    }
}
```

After completing this step, navigate in Terminal or similar to the SilverStripe root directory and run `composer install` or `composer update` depending on whether or not you have composer already in use.

## Overview

Query builder is a wrapper around the `SQLQuery` object, providing two ways to modify the `SQLQuery` object:

* `Heyday\QueryBuilder\Interfaces\QueryModifierInterface`
* Closure with function signiture `SQLQuery $query, array $data, QueryBuilderInterface $queryBuilder`

## Usage

### Implementing QueryModifierInterface

This is a very general modifier, modifiers you build might be more specific to your model.

```php
use Heyday\QueryBuilder\Interfaces\QueryBuilderInterface;
use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;

class LikeModifier extends QueryModifierInterface
{
	protected $column;
	public function __construct($column)
	{
		$this->column = $column;
	}
	public function modify(\SQLQuery $query, array $data, QueryBuilderInterface $queryBuilder)
	{
		if (isset($data['search']) && $data['search']) {
			$query->addWhere("{$this->column} LIKE '%{$data['search']}%'");
		}
	}
}
```

### Using a modifier with QueryBuilder

```php
use Heyday\QueryBuilder\QueryBuilder;

$qb = new QueryBuilder(
	'SiteTree',
	[new LikeModifier('SiteTree.Title')],
	['search' => $request->getVar('q')]
);

foreach ($qb as $page) {
	// Do something with page
}
```

## License

SilverStripe Query Builder is licensed under an [MIT license](http://heyday.mit-license.org/)
