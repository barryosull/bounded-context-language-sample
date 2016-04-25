<?php

/**
 * Converted into code
 * - Environments
 * - Domain
 * - Aggregate
 * - Entities
 * - Invariants
 */

/**
 * To be converted into code
 * - ValueObjects
 * - Events
 * - Commands
 * - State properties
 * - Command Handlers
 * - Event Handlers
 */

# Environment
//create environment 'develop-0.0.1';
$environment = new Environment('develop-0.0.1');

# Domain and Context
//using environment 'develop-0.0.1';

$environment = $environment_repo->fetch('develop-0.0.1');
$environment->using();

//create domain 'e-commerce';

$environment->add_domain($domain);
$domain = new Domain('e-commerce');
//create context 'shopping' for domain 'e-commerce';

$domain->add_context($context);
$context = new Context('shopping');

//create aggregate 'carts';
$context->add_aggregate($aggregate);
$aggregate = new Aggregate('carts');

# Value Objects and Entities

//for domain 'e-commerce' in context 'shopping';

/*
create value 'quantity' validated by (
	<:
		check value > 0
	:>
);
*/

//create entity 'product' (id, quantity) as (identifier, value\quantity);
$context->add_entity($entity);

$entity = new Entity('product', $arguments);

$arguments = new Arguments();
$arguments->add('id', 'identifier');
$arguments->add('quantity', 'value\quantity');

/*
# Created
within aggregate 'carts':
{
	create boolean 'is_created' defaults (false);

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
 * 
	create event 'empty';
};

# Full
within aggregate 'carts':
{
	create event 'full';
};

# Products
within aggregate 'carts':
{
	create index 'products';

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