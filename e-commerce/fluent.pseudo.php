<?php

# Environment
//create environment 'develop-0.0.1';
$environment->create('develop-0.0.1');

# Domain and Context
//using environment 'develop-0.0.1';
$environment->using('develop-0.0.1');

//create domain 'e-commerce';
$domain->create('e-commerce');
//create context 'shopping' for domain 'e-commerce';

# Value Objects and Entities

//for domain 'e-commerce' in context 'shopping';

/*
create value 'quantity' validated by (
	<:
		check value > 0
	:>
);
*/

/*
create entity 'product' (id, quantity) as (identifier, value\quantity);

create aggregate 'carts';

# Created
within aggregate 'carts':
{
	create boolean 'is_created' defaults (false);

	create invariant 'created' satisfied by (
		<:
			from aggregate
			check is_created == true
		:>
	);

	create identifier 'shopper_id' defaults (null);

	create event 'created' (shopper_id) as (identifier) handled by (
		<:
			update aggregate
				set 'is_created' = true
				set 'shopper_id' = shopper_id
		:>
	);
};

# Checked Out
within aggregate 'carts':
{
	create boolean 'is_checked_out' defaults (false);

	create invariant 'checked-out' satisfied by (
		<:
			from aggregate
			check is_checked_out == true
		:>
	);

	create event 'checked-out' handled by (
		<:
			update aggregate
				set 'is_checked_out' = true
		:>
	);
};

# Empty
within aggregate 'carts':
{
	create counter 'products_in_cart' defaults (0);

	create invariant 'is-empty' satisfied by (
		<:
			from aggregate
			check products_in_cart == 0
		:>
	);

	create event 'empty';
};

# Full
within aggregate 'carts':
{
	create invariant 'is-full' satisfied by (
		<:
			from aggregate
			check products_in_cart == 10
		:>
	);

	create event 'full';
};

# Products
within aggregate 'carts':
{
	create index 'products';

	create invariant 'product-exists' (product_id) as (identifier) satisfied by (
		<:
			from aggregate
			check exists in index 'products' (product_id)
		:>
	);

	create event 'product-added' (product) as (entity\product) handled by (
		<:
			update aggregate
				increment 'products_in_cart'
				add to index 'products' (product.id)
		:>
	);

	create event 'product-removed' (product_id) as (identifier) handled by (
		<:
			update aggregate
				decrement 'products_in_cart'
				remove product.id from index 'products'
		:>
	);

	create event 'product-quantity-changed' (product_id, quantity) as (identifier, value\quantity);
};
*/

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
$aggregate = $aggregate_repo->fetch('carts');

$aggregate->add_invariant($invariant);

$invariant = new Invariant('shopper-has-active-cart', $arguments, $invariant_query);

$arguments = new Arguments();
$arguments->add('shopper_id', 'identifier');

$invariant_query = new InvariantQuery();
$invariant_query->from_all($select_statement)
        ->where($where_statement)
        ->check($check_statement);

$select_statement = new SelectStatement('count', 'as', 'cart_count');
$where_statment = new WhereStatement();
$where_statment->where('shopper_id', '==', 'shopper_id')
        ->and_where('is_created', '==', true)
        ->and_where('is_checked_out', '==', false);
$check_statement = new CheckStatement();
$check_statement->check('cart_count', '>', '0');

/*
# Commands
within aggregate 'carts':
{
	create command 'create' (shopper_id) as (identifier) handled by (
		<{
			assert invariant not 'created';
			assert invariant not 'shopper-has-active-cart' (command.shopper_id);

			apply event 'created' (command.shopper_id);
			apply event 'empty';
		}>
	);

	create command 'add-product' (product) as (entity\product) handled by (
		<{
			assert invariant not 'checked-out';
			assert invariant not 'is-full';
			assert invariant not 'product-exists' (command.product.id);

			apply event 'product-added' (command.product);

			apply event 'full' when assert invariant 'is-full';
		}>
	);

	create command 'change-product-quantity' (product_id, quantity) as (identifier, value\quantity) handled by (
		<{
			assert invariant not 'checked-out';
			assert invariant 'product-exists' (command.product_id); 

			apply event 'product-quantity-changed' (command.product_id, command.quantity); 
		}>
	);

	create command 'remove-product' (product_id) as (identifier) handled by (
		<{
			assert invariant not 'checked-out';
			assert invariant 'product-exists' (command.product_id);

			apply event 'product-removed' (command.product_id);

			apply event 'empty' when assert invariant 'is-empty';
		}>
	);

	create command 'checkout' handled by (
		<{
			assert invariant not 'checked-out';

			apply event 'checked-out';
		}>
	);
};

*/