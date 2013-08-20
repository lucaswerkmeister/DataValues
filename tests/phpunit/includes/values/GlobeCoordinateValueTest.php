<?php

namespace DataValues\Test;

use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;

/**
 * @covers DataValues\GlobeCoordinateValue
 *
 * @file
 * @since 0.1
 *
 * @ingroup DataValue
 *
 * @group DataValue
 * @group DataValueExtensions
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GlobeCoordinateValueTest extends DataValueTest {

	/**
	 * @see DataValueTest::getClass
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getClass() {
		return 'DataValues\GlobeCoordinateValue';
	}

	/**
	 * @see DataValueTest::constructorProvider
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function constructorProvider() {
		$argLists = array();

		$argLists[] = array( true, new LatLongValue( 4.2, 4.2 ), 1 );
		$argLists[] = array( true, new LatLongValue( 4.2, 42 ), 1 );
		$argLists[] = array( true, new LatLongValue( 42, 4.2 ), 0.1 );
		$argLists[] = array( true, new LatLongValue( 42, 42 ), 0.1 );
		$argLists[] = array( true, new LatLongValue( -4.2, -4.2 ), 0.1 );
		$argLists[] = array( true, new LatLongValue( 4.2, -42 ), 0.1 );
		$argLists[] = array( true, new LatLongValue( -42, 4.2 ), 10 );
		$argLists[] = array( true, new LatLongValue( 0, 0 ), 0.001 );

		// TODO: test precisions that are out of the valid range

		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), null );
		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), 'foo' );
		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), true );
		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), array( 1 ) );
		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), '1' );

		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), 1, null );
		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), 1, array( 1 ) );
		$argLists[] = array( false, new LatLongValue( 4.2, 4.2 ), 1, 1 );

		$argLists[] = array( true, new LatLongValue( 4.2, 4.2 ), 1, GlobeCoordinateValue::GLOBE_EARTH );
		$argLists[] = array( true, new LatLongValue( 4.2, 4.2 ), 1, 'terminus' );
		$argLists[] = array( true, new LatLongValue( 4.2, 4.2 ), 1, "Schar's World" );
		$argLists[] = array( true, new LatLongValue( 4.2, 4.2 ), 1, 'coruscant' );

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 * @param GlobeCoordinateValue $geoCoord
	 * @param array $arguments
	 */
	public function testGetLatitude( GlobeCoordinateValue $geoCoord, array $arguments ) {
		$actual = $geoCoord->getLatitude();

		$this->assertInternalType( 'float', $actual );
		$this->assertEquals( $arguments[0]->getLatitude(), $actual );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param GlobeCoordinateValue $geoCoord
	 * @param array $arguments
	 */
	public function testGetLongitude( GlobeCoordinateValue $geoCoord, array $arguments ) {
		$actual = $geoCoord->getLongitude();

		$this->assertInternalType( 'float', $actual );
		$this->assertEquals( $arguments[0]->getLongitude(), $actual );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param GlobeCoordinateValue $geoCoord
	 * @param array $arguments
	 */
	public function testGetPrecision( GlobeCoordinateValue $geoCoord, array $arguments ) {
		$actual = $geoCoord->getPrecision();

		$this->assertTrue( is_float( $actual ) || is_int( $actual ), 'Precision is int or float' );
		$this->assertEquals( $arguments[1], $actual );
	}

	/**
	 * @dataProvider instanceProvider
	 * @param GlobeCoordinateValue $geoCoord
	 * @param array $arguments
	 */
	public function testGetGlobe( GlobeCoordinateValue $geoCoord, array $arguments ) {
		$expected = array_key_exists( 2, $arguments )
			? $arguments[2]
			: GlobeCoordinateValue::GLOBE_EARTH;

		$actual = $geoCoord->getGlobe();

		$this->assertTrue(
			is_string( $actual ),
			'getGlobe should return a string'
		);

		$this->assertEquals( $expected, $actual );
	}

}