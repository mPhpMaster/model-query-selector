# Laravel ModelQuerySelector - SQL Column Selection Utility

Laravel ModelQuerySelector is a powerful package designed to simplify the construction of SQL select queries in Laravel
applications. It offers a fluent interface for building column selections and table aliases, streamlining the creation
of complex queries.

## Features

- **Fluent Interface**: Easily construct SQL select queries using a fluent and intuitive syntax.
- **Column Selections**: Efficiently specify columns to be selected in the query.
- **Table Aliases**: Define table aliases for improved query readability and clarity.
- **Dynamic and Static Methods**: Use both static and dynamic method calls to create queries.

## Dependencies:

* php >=8.1 **REQUIRED IN YOUR PROJECT**
* laravel >=8 **REQUIRED IN YOUR PROJECT**
* illuminate/support >=8 _composer will install it automaticly_
* laravel/helpers ^1.5 _composer will install it automaticly_

## Installation

You can install the Laravel ModelQuerySelector package via Composer. Run the following command in your terminal:

```bash
composer require mphpmaster/model-query-selector
```

The package will automatically register its service provider.

## Usage

### Helper Function

You can use the `mqs` helper function to create an instance of `ModelQuerySelector`:

```php
use MPhpMaster\ModelQuerySelector\ModelQuerySelector;

// Usage example
$querySelector = mqs(User::class, 'u');
dump($querySelector); // Outputs: "users as u"
```

### Manual Instantiation

You can also manually instantiate `ModelQuerySelector` as follows:

```php
use MPhpMaster\ModelQuerySelector\ModelQuerySelector;
use App\Models\User;

// Static method call to qc()
$querySelector = ModelQuerySelector::qc(['column1', 'column2'], User::class);
dump($querySelector); // Outputs: "users.column1, users.column2"

// Static method call to table()
$querySelector = ModelQuerySelector::table(User::class, 'alias');
dump($querySelector); // Outputs: "users as alias"

// Dynamic method call to table()
$querySelector = new ModelQuerySelector();
$querySelector->table(User::class, 'alias');
dump($querySelector); // Outputs: "users as alias"

// Dynamic method call to qc()
$querySelector = new ModelQuerySelector(User::class);
$querySelector->qc(['column1', 'column2'], 'alias');
dump($querySelector); // Outputs: "alias.column1, alias.column2"
```

## License

The Laravel ModelQuerySelector package is open-source software licensed under
the [MIT license](https://opensource.org/licenses/MIT).

## Contributing

Contributions are welcome! Feel free to submit bug reports, feature requests, or pull requests
on [GitHub](https://github.com/mPhpMaster/model-query-selector).

## Support

For any questions or issues, please [open an issue](https://github.com/mPhpMaster/model-query-selector/issues) on
GitHub.

## Credits

This package was created and is maintained by [hlaCk](https://github.com/mPhpMaster).

## Acknowledgements

Special thanks to the Laravel community for their continued support and contributions.

***

## Stand with Palestine 🇵🇸 <i>#FreePalestine</i>

