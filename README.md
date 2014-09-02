# SilverStripe Query Builder

Provides a way to create queries that are flexible, reusable and composeable for SilverStripe

## Installation (with composer)

Installing from composer is easy,

Create or edit a `composer.json` file in the root of your SilverStripe project, and make sure the following is present.

```json
{
    "require": {
        "heyday/silverstripe-querybuilder": "1.0.*"
    }
}
```

After completing this step, navigate in Terminal or similar to the SilverStripe root directory and run `composer install` or `composer update` depending on whether or not you have composer already in use.


## Usage

```php

class PaginationQueryModifier implements QueryModifierInterface
{
    protected $limit;
    protected $start;

    /**
     * @param $limit
     * @param $start
     */
    public function __construct($limit = 20, $start = 0)
    {
        $this->limit = $limit;
        $this->start = $start;
    }

    protected function getLimit()
    {
        return $this->start . ', ' . (int) $this->limit;
    }

    public function modify(\SQLQuery $query, array $data, \Heyday\QueryBuilder\Interfaces\QueryBuilderInterface $queryBuilder)
    {
        $query->setLimit($this->getLimit());

        return $query;
    }
}

class Page_Controller extends ConentController {
	protected function getItems()
	{
		if ($this->sort === 2) {
				$sortDirection = 'DESC';
		} else {
				$sortDirection = 'ASC';
		}

		return Injector::inst()->create('QueryBuilder',
				'Product',
				array(
						new PaginationQueryModifier($this->itemsPerPage ? : 20, $this->start ? : 0),
						function ($query, $data) {
							$query->setOrderBy('Title ASC');
						}
				), array(
						'parentID' => $this->ID
				)
		);
	}
}
```

##License

SilverStripe Query Builder is licensed under an [MIT license](http://heyday.mit-license.org/)
