<?php

/**
 * Converted into code
 * - Environments
 * - Domain
 * - Aggregate
 * - Entities
 * - Invariants
 * - ValueObjects
 */

/**
 * To be converted into code
 * - Events
 * - Commands
 * - State properties
 * - Command Handlers
 * - Event Handlers
 */

/**
 * Notes:
 * Need more examples of ValueObjects
 * Index should probably define its type, eg (Product)
 * Not happy with update statements, they need work/rejigging
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
/*
create value 'quantity' validated by (
	<:
		check value > 0
	:>
);
*/
$context->add_value_object($value_object);

$value_object = new ValueObject('quantity');
$value_object->validated_by($check_statement);
$check_statement = new CheckStatement();
$check_statement->check('>', '0');

//create entity 'product' (id, quantity) as (identifier, value\quantity);
$context->add_entity($entity);

$entity = new Entity('product', $parameters);
$parameters = new Parameters();
$parameters->add('id', 'identifier');
$parameters->add('quantity', 'value\quantity');


/*
# Created
within aggregate 'carts':
{
	create boolean 'is_created' defaults (false);
	create identifier 'shopper_id' defaults (null);
};
*/
$aggregate->add_property('is_created', 'boolean', false);
$aggregate->add_property('shopper_id', 'identifier', null);

/*
# Created
within aggregate 'carts':
{
	create event 'created' (shopper_id) as (identifier) handled by (
		<:
			update aggregate
				set 'is_created' = true
				set 'shopper_id' = shopper_id
		:>
	);
};
*/
$aggregate->add_event($event);
$event = new Event('created', $parameters);
$parameters = new Parameters();
$parameters->add('shopper_id', 'identifier');

$aggregate->add_event_handler($event_handler);
$event_handler = new EventHandler($event, $update_statment);
$update_statment = new UpdateStatement();
$update_statment->set('is_created', true);
$update_statment->set('shopper_id', 'shopper_id');

/*
# Checked Out
within aggregate 'carts':
{
	create boolean 'is_checked_out' defaults (false);
};
*/
$aggregate->add_value('is_checked_out', 'boolean', false);

/*
# Checked Out
within aggregate 'carts':
{
	create event 'checked-out' handled by (
		<:
			update aggregate
				set 'is_checked_out' = true
		:>
	);
};
*/
$aggregate->add_event($event);
$event = new Event('checked-out', $parameters);
$parameters = new Parameters();

$aggregate->add_event_handler($event_handler);
$event_handler = new EventHandler($event, $update_statment);
$update_statment = new UpdateStatement();
$update_statment->set('is_checked_out', true);

/*
# Empty
within aggregate 'carts':
{
	create counter 'products_in_cart' defaults (0);
	create event 'empty';
};
*/
$aggregate->add_property('products_in_cart', 'counter', 0);

$aggregate->add_event($event);
$event = new Event('empty', $parameters);
$parameters = new Parameters();

/*
# Full
within aggregate 'carts':
{
	create event 'full';
};
*/
$aggregate->add_event($event);
$event = new Event('full', $parameters);
$parameters = new Parameters();

/*
# Products
within aggregate 'carts':
{
	create index 'products';
    create event 'product-quantity-changed' (product_id, quantity) as (identifier, value\quantity);
};
*/
$aggregate->add_property('products', 'index');

$event = new Event('product-quantity-changed', $parameters);
$parameters = new Parameters();
$parameters->add('product_id', 'identifier');
$parameters->add('quantity', 'value\quantity');

/*
# Products
within aggregate 'carts':
{
	create event 'product-added' (product) as (entity\product) handled by (
		<:
			update aggregate
				increment 'products_in_cart'
				add to index 'products' (product.id)
		:>
	);
};
*/
$aggregate->add_event($event);
$event = new Event('product-added', $parameters);
$parameters = new Parameters();
$parameters->add('product', 'entity\product');

$aggregate->add_event_handler($event_handler);
$event_handler = new EventHandler($event, $update_statment);
$update_statment = new UpdateStatement();
$update_statment->increment('products_in_cart');
$update_statment->add_to_index('products', $index_key);
$index_key = new IndexKey('product', 'id');

/*
# Products
within aggregate 'carts':
{
	create event 'product-removed' (product_id) as (identifier) handled by (
		<:
			update aggregate
				decrement 'products_in_cart'
				remove product.id from index 'products'
		:>
	);
};
*/
$aggregate->add_event($event);
$event = new Event('product-removed', $parameters);
$parameters = new Parameters();
$parameters->add('product_id', 'identifier');

$aggregate->add_event_handler($event_handler);
$event_handler = new EventHandler($event, $update_statment);
$update_statment = new UpdateStatement();
$update_statment->decrement('products_in_cart');
$update_statment->remove_from_index('products', $index_key);
$index_key = new IndexKey('product', 'id');

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
};
*/
$aggregate->add_command($command);
$command = new Command('create', $parameters);
$parameters = new Parameters();
$parameters->add('shopper_id', 'identifier');

$aggregate->add_comamnd_handler($command_handler);
$command_handler = new CommandHandler($event, $update_statment);

$command_handler->add_invariant_assertion($asseration);
$assertion = new InvariantAssertion('created', 'not');

$command_handler->add_invariant_assertion($asseration);
$assertion = new InvariantAssertion('shopper-has-active-cart', 'not', $argument);
$argument = new Argument('command', 'shopper_id');

$command_handler->add_apply_event('created', $argument);
$argument = new Argument('command', 'shopper_id');

$command_handler->add_apply_event('empty');

/*
# Commands
within aggregate 'carts':
{
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


/**
 * Sending a command to this system
 */
$command = new Command('create', $arguments);
$arguments = new Arguments();
$arguments->add('shopper_id', '037c47d9-0a50-4a22-8a60-ec0f27f3d41e');

$aggregate->handle($command);