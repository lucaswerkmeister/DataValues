<?php

/**
 * MediaWiki setup for the DataValues extension.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 0.1
 *
 * @file
 * @ingroup DataValues
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( !defined( 'DATAVALUES' ) ) {
	define( 'DATAVALUES', true );
}

global $wgExtensionCredits, $wgExtensionMessagesFiles, $wgAutoloadClasses, $wgHooks, $wgResourceModules;

$wgExtensionCredits['datavalues'][] = array(
	'path' => __DIR__,
	'name' => 'DataValues',
	'version' => DataValues_VERSION,
	'author' => array( '[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]' ),
	'url' => 'https://www.mediawiki.org/wiki/Extension:DataValues',
	'descriptionmsg' => 'datavalues-desc',
);

$wgExtensionMessagesFiles['DataValues'] = __DIR__ . '/DataValues.i18n.php';

foreach ( include( __DIR__ . '/DataValues.classes.php' ) as $class => $file ) {
	if ( !array_key_exists( $class, $GLOBALS['wgAutoloadLocalClasses'] ) ) {
		$wgAutoloadClasses[$class] = __DIR__ . '/' . $file;
	}
}

/**
 * Hook to add PHPUnit test cases.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
 *
 * @since 0.1
 *
 * @param array $files
 *
 * @return boolean
 */
$wgHooks['UnitTestsList'][] = function( array &$files ) {
	// @codeCoverageIgnoreStart
	$testFiles = array(
		'includes/values/BooleanValue',
		'includes/values/GeoCoordinateValue',
		'includes/values/IriValue',
		'includes/values/MediaWikiTitleValue',
		'includes/values/MonolingualTextValue',
		'includes/values/MultilingualTextValue',
		'includes/values/NumberValue',
		'includes/values/QuantityValue',
		'includes/values/StringValue',
		'includes/values/UnknownValue',

		'includes/DataValueFactory',
	);

	foreach ( $testFiles as $file ) {
		$files[] = __DIR__ . '/tests/' . $file . 'Test.php';
	}

	return true;
	// @codeCoverageIgnoreEnd
};

/**
 * Hook to add QUnit test cases.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ResourceLoaderTestModules
 * @since 0.1
 *
 * @param array &$testModules
 * @param \ResourceLoader &$resourceLoader
 * @return boolean
 */
$wgHooks['ResourceLoaderTestModules'][] = function ( array &$testModules, \ResourceLoader &$resourceLoader ) {
	$moduleTemplate = array(
		'localBasePath' => __DIR__,
		'remoteExtPath' => 'DataValues/DataValues',
	);

	$testModules['qunit']['ext.dataValues.DataValues'] = $moduleTemplate + array(
		'scripts' => array(
			'tests/qunit/DataValues.tests.js',
		),
		'dependencies' => array(
			'dataValues',
		),
	);

	$testModules['qunit']['ext.dataValues.DataValue'] = $moduleTemplate + array(
		'scripts' => array(
			'tests/qunit/DataValue.tests.js',
		),
		'dependencies' => array(
			'dataValues.values',
		),
	);

	$testModules['qunit']['ext.dataValues.values'] = $moduleTemplate + array(
		'scripts' => array(
			'tests/qunit/values/BoolValue.tests.js',
			'tests/qunit/values/MonolingualTextValue.tests.js',
			'tests/qunit/values/MultilingualTextValue.tests.js',
			'tests/qunit/values/StringValue.tests.js',
			'tests/qunit/values/NumberValue.tests.js',
			'tests/qunit/values/UnknownValue.tests.js',
		),
		'dependencies' => array(
			'ext.dataValues.DataValue',
		),
	);

	$testModules['qunit']['ext.dataValues.util'] = $moduleTemplate + array(
		'scripts' => array(
			'tests/qunit/dataValues.util.inherit.tests.js',
		),
		'dependencies' => array(
			'dataValues.util',
		),
	);

	return true;
};

/**
 * Called when generating the extensions credits, use this to change the tables headers.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ExtensionTypes
 *
 * @since 0.1
 *
 * @param array &$extensionTypes
 *
 * @return boolean
 */
$wgHooks['ExtensionTypes'][] = function( array &$extensionTypes ) {
	// @codeCoverageIgnoreStart
	$extensionTypes['datavalues'] = wfMessage( 'version-datavalues' )->text();

	return true;
	// @codeCoverageIgnoreEnd
};

// Resource Loader module registration
$wgResourceModules = array_merge(
	$wgResourceModules,
	include( __DIR__ . '/Resources.php' )
);
