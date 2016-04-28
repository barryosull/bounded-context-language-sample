<?php

/**
 * Notes on Invariant Generation:
 * - Need more examples of complex queries in order to figure out the shape
 * - If we can't do the above, the interpreter needs to be designed so that it's flexible
 * - Aggregator functions need to fleshed out
 *   - Eg. "count as cart_count"
 * - Need more examples of local invariants
 *   - Eg. Takes in arguments (imagine it will look exactly like from_all
 * - Need examples of editing an invariant
 *     Suggest a full replace, rather than an alter, easier to code
 */

$aggregate = $aggregate_repo->fetch('carts');

/*
within aggregate 'carts':
{
	create invariant 'shopper-has-active-cart' (shopper_id) as (identifier) satisfied by (
		<:
			from all
				count as cart_count
			where shopper_id == shopper_id
				and is_created == true
				and is_checked_out == false
			check cart_count > 0
		:>
	);
};
*/
$aggregate->add_invariant($invariant);

$invariant = new Invariant('shopper-has-active-cart', $parameters, $invariant_query);

$parameters = new Parameters();
$parameters->add('shopper_id', 'identifier');

$invariant_query = new InvariantQuery();
$invariant_query->from_all($aggregator_statement)
        ->where($where_statement)
        ->check($check_statement);

$aggregator_statement = new AggregatorStatement('count', 'as', 'cart_count');
$where_statment = new WhereStatement();
$where_statment->where('shopper_id', '==', 'shopper_id')
        ->and_where('is_created', '==', true)
        ->and_where('is_checked_out', '==', false);
$check_statement = new CheckStatement();
$check_statement->check('cart_count', '>', '0');


/*
within aggregate 'carts':
{
	create invariant 'created' satisfied by (
		<:
			from aggregate
			check is_created == true
		:>
	);
};
*/
$aggregate->add_invariant($invariant);

$invariant = new Invariant('created', $parameters, $invariant_query);

$parameters = new Parameters();

$invariant_query = new InvariantQuery();
$invariant_query->from_aggregate($check_statement);

$check_statement = new CheckStatement();
$check_statement->check('is_created', '==',true);


/*
within aggregate 'carts':
{
	create invariant 'checked-out' satisfied by (
		<:
			from aggregate
			check is_checked_out == true
		:>
	);
};
*/
$aggregate->add_invariant($invariant);

$invariant = new Invariant('checked-out', $parameters, $invariant_query);

$parameters = new Parameters();

$invariant_query = new InvariantQuery();
$invariant_query->from_aggregate($check_statement);

$check_statement = new CheckStatement();
$check_statement->check('is_checked_out', '==',true);

/*
within aggregate 'carts':
{
	create invariant 'is-empty' satisfied by (
		<:
			from aggregate
			check products_in_cart == 0
		:>
	);
};
*/
$aggregate->add_invariant($invariant);

$invariant = new Invariant('is-empty', $parameters, $invariant_query);

$parameters = new Parameters();

$invariant_query = new InvariantQuery();
$invariant_query->from_aggregate($check_statement);

$check_statement = new CheckStatement();
$check_statement->check('products_in_cart', '==', 0);


/*
within aggregate 'carts':
{
	create invariant 'is-full' satisfied by (
		<:
			from aggregate
			check products_in_cart == 10
		:>
	);
};
*/
$aggregate->add_invariant($invariant);

$invariant = new Invariant('is-full', $parameters, $invariant_query);

$parameters = new Parameters();

$invariant_query = new InvariantQuery();
$invariant_query->from_aggregate($check_statement);

$check_statement = new CheckStatement();
$check_statement->check('products_in_cart', '==', 10);
