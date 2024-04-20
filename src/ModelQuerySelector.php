<?php

namespace MPhpMaster\ModelQuerySelector;

use MPhpMaster\ModelQuerySelector\TMakeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Stringable;

/**
 * @template C of string|class-string<\Illuminate\Database\Eloquent\Model>
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method static ModelQuerySelector qc(array $columns, string|Model|TExtraModelUtils|null $class_or_table = null, ?string $alias = null)
 * @method static ModelQuerySelector qc(string $columns, string|Model|TExtraModelUtils|null $class_or_table = null)
 *
 * @method ModelQuerySelector qc(array|string $columns, string|Model|TExtraModelUtils|null $class_or_table = null, ?string $alias = null)
 * @method ModelQuerySelector table(string|\Illuminate\Database\Eloquent\Model $class, ?string $alias = null)
 */
class ModelQuerySelector extends Stringable
{
	use  TMakeable;

	/**
	 * Constructs a new instance of the class.
	 *
	 * @param string|class-string<\Illuminate\Database\Eloquent\Model> $class The class name.
	 * @param string|null                                              $alias The alias for the class. Defaults to null.
	 */
	public function __construct(
		protected string  $class = "",
		protected ?string $alias = null,
	)
	{
		parent::__construct(
			$this->class ? (
			is_subclass_of($this->class, Model::class) ?
				($this->class::make()->getTable().($this->alias ? " as {$this->alias}" : '')) :
				$this->class.($this->alias ? " as {$this->alias}" : '')
			) : '',
		);
	}

	public static function __callStatic($method, $parameters): mixed
	{
		$result = null;
		$column = null;
		$classOrTable = null;
		$alias = null;

		if(count($parameters) === 1) {
			if(is_array(head($parameters))) {
				$parameters = head($parameters);
				$middle = count($parameters) >= 3 ? ($parameters[1] ?? null) : null;
				$last = count($parameters) >= 2 ? last($parameters) : null;

				return static::$method(...($method === 'qc' ? [ head($parameters), $middle, $last ] : $parameters));
			} else {
				if($method === 'table') {
					$column = null;
					$classOrTable = head($parameters);
					$alias = null;
				} else {
					$column = head($parameters);
					$classOrTable = null;
					$alias = null;
				}
			}
		}

		if($method === 'table' && count($parameters) >= 2) {
			$parameters = array_slice($parameters, 0, 2);

			$column = null;
			$classOrTable = $parameters[0] ?? null;
			$alias = $parameters[1] ?? null;

		} else if($method === 'qc') {
			if(count($parameters) > 3) {
				$parameters = array_slice($parameters, 0, 3);

				$column = $parameters[0] ?? null;
				$classOrTable = $parameters[1] ?? null;
				$alias = $parameters[2] ?? null;
			} else {
				$column = head($parameters);
				if(count($parameters) === 1) {
					$alias = null;
					$classOrTable = null;
				} else if(count($parameters) === 2) {
					$classOrTable = last($parameters);
					$alias = null;
				} else if(count($parameters) === 3) {
					if(is_subclass_of(last($parameters), Model::class)) {
						$classOrTable = last($parameters);
						$alias = $parameters[1] ?? null;
					} else {
						$classOrTable = $parameters[1] ?? null;
						$alias = last($parameters);
					}
				} else {
					$alias = last($parameters);
					$classOrTable = null;
				}

				if(isset($alias) && is_array($column) && count($column) > 1) {
					throw new \Exception('Invalid input: You cannot use an alias with multiple columns. Please provide only one column or remove the alias.');
				}
			}
		}

		if(!is_null($classOrTable) && is_null($alias)) {
			if(is_string($column) && is_subclass_of($column, Model::class)) {

			} else if(is_string($classOrTable)) {
				$_alias = $alias;
				$_column = $column;
				if(is_subclass_of($classOrTable, Model::class)) {
					if($method === 'table') {
						$column = null;
						$alias = $_alias ?? $_column;
					} else {
						$column = $_column ?? $_alias;
						$alias = null;
					}
				}
			} else {
				$result = static::make($column, $alias);

			}

		} elseif(is_string($column) && !is_null($classOrTable) && !is_subclass_of($classOrTable, Model::class) && is_subclass_of($column, Model::class)) {
			[ $classOrTable, $column ] = [ $column, $classOrTable ];
		} elseif($column && !$classOrTable && !$alias) {
			$classOrTable = $column;
			$column = $alias = null;

		} elseif(!$classOrTable && $column && $alias) {
			$classOrTable = $column;
			$column = null;

		} else {

			$_column ??= $alias ?? null;
			$classOrTable ??= $column ?? $alias ?? '';
			if($classOrTable === $column) {
				$column = $_column;
				$alias = null;
			}
		}

		$classOrTable = is_subclass_of($classOrTable, \Stringable::class) ? trim($classOrTable) : $classOrTable;
		if(
			!is_string($classOrTable) &&
			!(
				in_array(TExtraModelUtils::class, class_uses_recursive($classOrTable)) ||
				is_subclass_of($classOrTable, Model::class)
			) &&
			!(
				method_exists($classOrTable, 'table') ||
				method_exists($classOrTable, 'qc')
			)
		) {
			throw new \Exception('Class must implement TExtraModelUtils trait!');
		}

		$qcExists = !is_string($classOrTable) && method_exists($classOrTable, 'qc') || is_subclass_of($classOrTable, Model::class);
		$tableExists = method_exists($classOrTable, 'table') || is_subclass_of($classOrTable, Model::class);

		if($tableExists && $method === 'table') {
			$result = static::make($classOrTable, $alias);
		} elseif($classOrTable && $column && $method === 'table') {
			$result = static::make($classOrTable)->qc($column, $alias);
		} elseif($classOrTable && $method === 'table') {
			$result = static::make($classOrTable, $alias);
		} elseif($qcExists && $method === 'qc') {
			if(!$column && !$alias && $classOrTable) {
				$result = static::make('')->qc($classOrTable);
			} else {
				$result = static::make($classOrTable, is_array($column) ? $alias : null)
					->qc(is_null($column) ? $alias : $column, is_null($column) ? null : $alias);
			}
		} elseif(!$classOrTable && !$alias && $column && $method === 'qc') {
			$result = static::make($column);
		} elseif($classOrTable && $column && $method === 'qc') {
			$result = static::make($classOrTable)->qc($column, $alias);
		} elseif($classOrTable && $method === 'qc') {
			$result = static::make($classOrTable, $alias);
		} else {
			throw new \Exception("Unknown method {$method}!");
		}

		return $result;
	}

	public function __call($method, $parameters)
	{
		if(count($parameters) === 0) {
			$parameters = [ null, $this->alias ]; // Set $this->alias as default
		} elseif(count($parameters) === 1) {
			$parameters[] = $this->alias;
		}

		/** @var \Illuminate\Database\Eloquent\Model $c */
		[ $columns, $alias ] = $parameters;
		if(is_array($columns)) {
			$is_associative = count(array_filter(array_keys($columns), 'is_string')) > 0;
			$columns = array_map(
				fn($key, $column) => is_string($key) ? "$column as $key" : $column,
				array_keys($columns),
				$columns
			);
			$columns = implode(', ', $columns);
		} else {
			$columns = starts_with($columns, '.') ? substr($columns, 1) : $columns;
		}

		$columns = array_wrap(
			is_subclass_of($columns, Model::class) ? $columns::make()->getTable() : $columns
		);

		$result = null;
		if(method_exists($this->class, 'qc')) {
			$result = $this->class::qc($columns, $alias);
		} else if(is_subclass_of($this->class, Model::class)) {
			/** @var \Illuminate\Database\Eloquent\Model $instance */
			$instance = $this->class::make();
			$_alias = ($this->alias ?? $instance->getTable());
			$_alias = is_subclass_of($_alias, Model::class) ? $_alias::make()->getTable() : $_alias;

			$oldTable = $instance->getTable();
			$instance->setTable($_alias);
			$columns = $instance->qualifyColumns(array_wrap($columns));
			$instance->setTable($oldTable);

			$result = count($columns) > 1 ? $columns : head($columns);

			if(
				count(array_diff(
					array_map(fn($column) => $_alias ? str_after($column, "{$_alias}.") : '', array_wrap($alias)),
					array_map(fn($column) => $_alias ? str_after($column, "{$_alias}.") : '', array_wrap($columns)),
				)) || count(array_diff(array_wrap($result), array_wrap($columns)))
			) {
				$result .= $alias ? " as $alias" : '';
			}
		} else if(is_string($this->class)) {
			/** @var \Illuminate\Database\Eloquent\Model $instance */
			$instance = new class extends Model {

			};

			$instance->setTable(is_subclass_of($this->class, Model::class) ? $this->class::make()->getTable() : $this->class);
			$columns = $instance->qualifyColumns(array_wrap($columns), $alias ?? $this->alias);
			if(count($columns) > 1) {
				$result = array_map(fn($column) => starts_with($column, '.') ? substr($column, 1) : $column, $columns);
			} else {
				$result = head($columns);
				$result = starts_with($result, '.') ? substr($result, 1) : $result;
				$result .= $alias ? " as $alias" : '';
			}

		}
		$result = starts_with($result, '.') ? substr($result, 1) : $result;
		$this->value = $result;
		return $this;
	}
}