# PHP Standards for Event Espresso

Previously, our php coding standards closely mirrored the [WordPress PHP Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/php/).  We won't repeat all those here but emphasis, modifications, and additions are listed below. There is still some really old code in Event Espresso 4 that does not conform to these standards.

We have recently decided however to deviate from the above to follow coding standards, methodologies, and guidelines that comply with the greater PHP community. This means following [PHP Standards Recommendations](http://www.php-fig.org/psr/) and pursuing [modern day best practices](http://www.phptherightway.com/)

All code following the previous WordPress methodologies is now considered legacy code and will continue to follow those particular guidelines until it is replaced. All new classes and files will follow modern day best practices, including namespacing and PSR compliance.


## General Code Guidelines

 * Event Espresso supports PHP 5.3+ and WordPress 4.1+ so all functions and code used should be available in those libraries.
 * Class, Interface, Method, and Function names should be clear and concise
 * Methods and Functions should use suitable type hinting where applicable
 * Data types should be used consistently and avoid type switching as much as possible<br>
     ex: if an optional method parameter is intended to be a string, 
         then use `""` (empty string) for the default instead of `null` or `false` 
         This is especially important for return types in order to minimize the amount of type checking that client 
         code has to perform after receiving a value from a method
 * ALL user viewable strings should be translated using ` esc_html__() ` or a suitable equivalent
 * All comparisons should be using type safe equivalents<br>
    ex: `===` vs `==` or `in_array($int, $array, true)` vs `in_array($int, $array)`
    
    
### Formatting Overview

 * all non-legacy files are formatted according to the PSR specifications
 * names for non-legacy classes and interfaces follow StudlyCaps formatting
 * names for non-legacy methods and functions follow camelCase formatting
 * names for variables follow snake_case formatting
 * classes and interfaces have phpdoc comment blocks that contain:
     * a brief description of their purpose
     * an @package tag to indicate the vendor or plugin that the code belongs to
     * an @author tag to indicate the specific dev that first created the class
     * an @since tag listing the version that the code was first added
 * methods and functions have phpdoc comment blocks that contain:
     * a brief description of their purpose (not always necessary for really simple/small blocks of code)
     * a list of all parameters and their datatypes
     * an @return tag if applicable
     * an @throws tag for each exception type that could be thrown as a result of calling the function
     
     
### Design and Architecture

 * classes, methods, or functions should not be too large. Extract logic into more granular chunks if need be
 * dependencies that are required by a class should be injected into the constructor
 * optional dependencies can be injected into the constructor or via setters
 * static state should be avoided like the plague
 * non-legacy classes should follow PSR-4 compatible namespacing
 * use `use` statements for all classnames in a file and simplify namespaces in phpdocs 
 * can design patterns be utilized to provide a more eloquent versatile solution?
 * code should be DRY and not duplicate existing code
 * code should strive to follow the S.O.L.I.D. principles:
     * **S** - Single Responsibility Principle: each class should focus on doing ONE thing only and all functionality should be related to that one thing
     * **O** - Open-Closed Principle: Class internals should be hidden as much as possible. To add new functionality, write new class extensions instead of modifying existing code, ie: substitute OldClass with OldClassPlusLasers (which extends OldClass). Interfaces allow the substitution.
     * **L** - Liskov Substitution Principle: parent/child classes should be interchangeable, ie: OldClass parent or OldClassPlusLasers child class should both be usable by classes implementing their interface
     * **I** - Interface Segregation Principle: avoid big interfaces, use multiple smaller ones describing more granular behaviour so that classes don't have to implement methods they don't support.
     * **D** - Dependency Inversion Principle: inject dependencies into classes so they can be externally controlled. Required dependencies should use constructor injection, and optional dependencies can use setter injection.
 

### Backwards Compatibility

 * code should strive to be backwards compatible
 * classes, interfaces, methods, or functions that are no longer required should be deprecated instead of removed, and logic should be forwarded to replacements where applicable



### Yoda Conditionals.

See the [WordPress handbook for an example and info on what this is](http://make.wordpress.org/core/handbook/coding-standards/php/#yoda-conditions).  We have decided to not adopt the use of yoda conditionals in our code.

### DRY (Don't Repeat Yourself)

Most developers will be familiar with this principle.  If you find yourself writing code repeatedly consider whether its something that can be abstracted for reusability.

Consider the following bad example.

```php
function increment_foo($foo){
	return $foo++;
}
function increment_bar($bar){
	return $bar++;
}
```

Notice how these two methods `increment_foo()` and `increment_bar()` are basically doing the same thing - take an incoming variable and increment it by one.  The DRY way to do this is simply:

```php
function increment( $item ) {
	return $item+=1
}
```

Now this is probably an overtly obvious example of the DRY principle but it does illustrate what we mean.  The less code you write the less there is to maintain.  In this example we have one function that does incrementation and thus we only have to maintain that one function going forward.  DRY also leads to more bug free code, however when there are bugs, they are generally easier to fix because instead of fixing a bug in multiple places, you are more likely to only need to fix in one.  ([more reading on DRY here](http://programmer.97things.oreilly.com/wiki/index.php/Don't_Repeat_Yourself))

### Favor OOP over procedural

One of the major decisions made early in the development of Event Espresso 4, was to use general OOP (Object Oriented Programming) principles in the refactor.   What this means is that when designing and implementing systems, we favoured  using classes and objects over global scope functions and global variables.   This allows for clear separation of concerns and more testable and reusable code.

## Naming Conventions

### Function Naming

Any functions not found in a class should be prefixed with `espresso_`.  An example of this in use is the `espresso_version()` function.

Class Naming

All classes for Event Espresso should be prefixed with `EE_`.  An example of this in use is the `EE_Base_Class`.  Note, there are some other important naming schemas related to classes:

Type of Class | Naming Schema | Description | Example 
------------- | ------------- | ----------- | ------- 
Regular | EE_{class_name} | This is a class that is not a part of any system or library OR a core class. | EE_Registry
DB Model | EEM_{class_name} | These are part of the EE model system. | EEM_Event
DB Model Object (entity) | EE_{class_name} | These are part of the EE Model system. You can differentiate these from Regular classes in that Model Objects always extend the `EE_Base_Class` | EE_Event
Helper | EEH_{class_name} | Helper Classes. These classes usually contain static methods and are typically used for "helper" type methods. | EEH_Template
Admin | {class_name}_Admin_Page or {class_name}_Admin_Page_Init | These classes are a part of the EE Admin system | Event_Admin_Page or Event_Admin_Page_Init
CPT Strategy | EE_CPT_{class_name}_Strategy | Any class related to CPT's and Custom Taxonomies | EE_CPT_Event_Strategy
Data Migration | EE_DMS_{version_migration} | Classes used for Data Migration | EE_DMS_4_1_0
Messages Data Handler | EE_Messages_{data_source}_incoming_data | These are the data handler classes for the Messages system. | EE_Messages_Gateways_incoming_data
Messages Template Defaults | EE_Messages_{messenger}_{message_type}_Defaults | These classes define the defaults for the message templates | EE_Messages_Email_Cancelled_Registration_Defaults
Messages Message Type | EE_{message_type}_message_type | These classes represent message types. | EE_Cancelled_Registration_message_type
Messages Messenger | EE_{messenger}_messenger | These classes represent a messages system messenger | EE_Email_messenger
Messages Validators | EE_Messages_{messenger}_{message_type}_Validator | These classes represent the messages system validators (validate fields and shortcodes in templates) | EE_Email_Cancelled_Registration_Validator
Modules | EED_{Module_Name} | These classes are part of the EE Module system | EED_Event_Single
General Shortcodes (using WordPress shortcodes system) | EES_{Shortcode_Name} | These classes define and handle the various EE shortcodes. | EES_Espresso_Cancelled
Widgets | EEW_{Widget_Name} | These classes define and implement various EE Widgets for the WordPress widget system. | EEW_Upcoming_Events

### Class Property Method and Property Schema

All private or protected properties or methods are prefixed with an underscore.  Example Property: `$this->_property`.  Example method: `function _method()`.

All public properties or methods are not prefixed with an underscore.  Example property: `$this->property`.  Example method: `function method() {}`.

### File Naming Schema

File Type | Schema | Description | Example 
--------- | ------ | ----------- | ------- 
Regular | .php | As with normal php rules. All php files end with the `.php` extension | espresso.php
Core | .core.php | Core Classes (usually parent or "main" classes) | EE_Error.core.php
Template | template.php | Any template related code goes into a file with this extension | whats_new.template.php
Help Tabs | .help_tab.php | Files containing classes for the EE Help Tab system. | event_editor.help_tab.php
Non Specific Class | .class.php | Anything with a "class" extension contains a class (or classes). | Events_Admin.class.php
Library | .lib.php | Used for classes that are a part of a library. | EE_Event_Editor_Decaf_Tips.lib.php
Migration Scripts | .dms.php | Contains a data migration script class. | EE_DMS_4_1_0.dms.php
Model | .model.php | Contains a model class. | EEM_Answer.model.php
Helper | .helper.php | Contains a helper class. | EEH_DTT_Helper.helper.php
Module | .module.php | Contains a module class. | EED_Event_Single.module.php
Shortcodes | .shortcode.php | Contains an EE Shortcode Class | EES_Espresso_Events.shortcode.php
Widgets | .widget.php | Contains a widget class. | EEW_Upcoming_Events.widget.php

## White Space

### Indentation

Your indentation should always reflect logical structure. Use real tabs (1 tab = 4 spaces) and not spaces, as this allows the most flexibility across clients.

### Remove Trailing Spaces

> Make sure you remove trailing whitespace after closing PHP tags

Also remove trailing spaces at the end of each line of code.

### Space between functions/methods

For readability, put 5 lines of white space between functions/methods.

### Inline Spaces

Always put spaces after commas and on both sides of logical and assignment operators.

```php
$x === 23
$foo && $bar
! $foo
array( 1, 2, 3 )
```

Put spaces on both sides of the opening and closing parenthesis of `if`, `else if`, `foreach`, `for`, and `switch` blocks.

```php
foreach ( $foo as $bar ) { 
      ...
}
```

When defining a function, do it like so:

`function myfunction( $param1, $param2 = 'bar'  ) { ... }`

When calling a function, do it like so:

`myfunction( $param1, funcparam( $param2 ) );`

When performing logical comparisons:

`if ( ! $foo ) { ... }`

When type casting:

`foreach ( (array) $foo as $bar { ... }`

## PHP In Templates

In general, try to restrict usage of PHP code in templates to only using: `if`, `else`, `foreach`, `echo`.  Generally there should be no assigning of variables (except maybe an iteration counter).  There should definitely be no SQL queries, requires, or definition of any functions.  Also, define all the variables in the template requires at the beginning of the file and briefly explain what they are expected to contain.

```php
<?php
/**
 * Example template to show php usage
 *
 * Template: includes/template/example_template.template.php
 */

/**
 * Template vars in use
 * @var $event 	   An EE_Event object
 * @var $tickets   An array of EE_Ticket objects
 * @var $datetimes An array of EE_Datetime objects
 */
?>
<div class="container">
	<h2><?php echo $event->get('EVT_name'); ?></h2>
	<ul>
		<?php foreach ( $tickets as $ticket ) : ?>
		<li>Ticket: <?php echo $ticket->get('TKT_name'); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
```

## Database Queries

In Event Espresso 4 we spent a significant amount of time planning and building a model system for all db interactions.  The intent is that only the `EEM_` model singletons are used to interact with the database.  If a query you want doesn't exist, then generally, it should be added to the appropriate model (or improvement to the model to support that query).
