<?php

namespace DataValues\Test;
use DataValues\DataValue;

/**
 * Base for unit tests for DataValue implementing classes.
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
 * @file
 * @since 0.1
 *
 * @ingroup DataValueTest
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DataValueTest extends \MediaWikiTestCase {

	/**
	 * Returns the name of the concrete class tested by this test.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public abstract function getClass();

	/**
	 * First element can be a boolean indication if the successive values are valid,
	 * or a string indicating the type of exception that should be thrown (ie not valid either).
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public abstract function constructorProvider();

	/**
	 * Creates and returns a new instance of the concrete class.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function newInstance() {
		$reflector = new \ReflectionClass( $this->getClass() );
		$args = func_get_args();
		$instance = $reflector->newInstanceArgs( $args );
		return $instance;
	}

	/**
	 * @since 0.1
	 *
	 * @return array [instance, constructor args]
	 */
	public function instanceProvider() {
		$phpFails = array( $this, 'newInstance' );

		return array_filter( array_map(
			function( array $args ) use ( $phpFails ) {
				$isValid = array_shift( $args ) === true;

				if ( $isValid ) {
					return array( call_user_func_array( $phpFails, $args ), $args );
				}
				else {
					return false;
				}
			},
			$this->constructorProvider()
		), 'is_array' );
	}

	/**
	 * @dataProvider constructorProvider
	 *
	 * @since 0.1
	 */
	public function testConstructor() {
		$args = func_get_args();

		$valid = array_shift( $args );
		$pokemons = null;

		try {
			$dataItem = call_user_func_array( array( $this, 'newInstance' ), $args );
			$this->assertInstanceOf( $this->getClass(), $dataItem );
		}
		catch ( \Exception $pokemons ) {
			if ( $valid === true ) {
				throw $pokemons;
			}

			if ( is_string( $valid ) ) {
				$this->assertEquals( $valid, get_class( $pokemons ) );
			}
			else {
				$this->assertFalse( $valid );
			}
		}
	}

	/**
	 * @dataProvider instanceProvider
	 * @param DataValue $value
	 * @param array $arguments
	 */
	public function testImplements( DataValue $value, array $arguments ) {
		$this->assertInstanceOf( '\Immutable', $value );
		$this->assertInstanceOf( '\Hashable', $value );
		$this->assertInstanceOf( '\Comparable', $value );
		$this->assertInstanceOf( '\Serializable', $value );
		$this->assertInstanceOf( '\Copyable', $value );
		$this->assertInstanceOf( '\DataValues\DataValue', $value );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param DataValue $value
	 * @param array $arguments
	 */
	public function testSerialization( DataValue $value, array $arguments ) {
		$serialization = serialize( $value );
		$this->assertInternalType( 'string', $serialization );

		$unserialized = unserialize( $serialization );
		$this->assertInstanceOf( '\DataValues\DataValue', $unserialized );

		$this->assertTrue( $value->equals( $unserialized ) );
		$this->assertEquals( $value, $unserialized );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param DataValue $value
	 * @param array $arguments
	 */
	public function testEquals( DataValue $value, array $arguments ) {
		$this->assertTrue( $value->equals( $value ) );

		foreach ( array( true, false, null, 'foo', 42, array(), 4.2 ) as $otherValue ) {
			$this->assertFalse( $value->equals( $otherValue ) );
		}
	}

	/**
	 * @dataProvider instanceProvider
	 * @param DataValue $value
	 * @param array $arguments
	 */
	public function testGetHash( DataValue $value, array $arguments ) {
		$hash = $value->getHash();

		$this->assertInternalType( 'string', $hash );
		$this->assertEquals( $hash, $value->getHash() );
		$this->assertEquals( $hash, $value->getCopy()->getHash() );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param DataValue $value
	 * @param array $arguments
	 */
	public function testGetCopy( DataValue $value, array $arguments ) {
		$copy = $value->getCopy();

		$this->assertInstanceOf( '\DataValues\DataValue', $copy );
		$this->assertTrue( $value->equals( $copy ) );
	}

}