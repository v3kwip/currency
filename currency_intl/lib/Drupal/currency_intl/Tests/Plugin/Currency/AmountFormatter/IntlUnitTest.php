<?php

/**
 * @file
 * Contains \Drupal\currency_intl\Tests\Plugin\Currency\AmountFormatter\IntlUnitTest.
 */

namespace Drupal\currency_intl\Tests\Plugin\Currency\AmountFormatter;

use Drupal\currency\Plugin\Currency\AmountFormatter\Intl;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency_intl\Plugin\Currency\AmountFormatter\Intl.
 */
class IntlUnitTest extends UnitTestCase {

  /**
   * The formatter under test.
   *
   * @var \Drupal\currency\Plugin\Currency\AmountFormatter\Intl
   */
  protected $formatter;

  /**
   * The locale delegator used for testing.
   *
   * @var \Drupal\currency\LocaleDelegator|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $localeDelegator;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency_intl\Plugin\Currency\AmountFormatter\Intl unit test',
      'group' => 'Currency Intl',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomName();
    $plugin_definition = array();

    $this->localeDelegator = $this->getMockBuilder('\Drupal\currency\LocaleDelegator')
      ->disableOriginalConstructor()
      ->getMock();

    $this->formatter = new \Drupal\currency_intl\Plugin\Currency\AmountFormatter\Intl($configuration, $plugin_id, $plugin_definition, $this->localeDelegator);
  }

  /**
   * Test formatAmount().
   */
  function testFormatAmount() {
    $locale = 'nl_NL';
    $pattern = '¤-#,##0.00¤¤';
    $decimal_separator = '@';
    $grouping_separator ='%';
    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');
    $currency_locale->expects($this->any())
      ->method('getLocale')
      ->will($this->returnValue($locale));
    $currency_locale->expects($this->any())
      ->method('getPattern')
      ->will($this->returnValue($pattern));
    $currency_locale->expects($this->any())
      ->method('getDecimalSeparator')
      ->will($this->returnValue($decimal_separator));
    $currency_locale->expects($this->any())
      ->method('getGroupingSeparator')
      ->will($this->returnValue($grouping_separator));

    $this->localeDelegator->expects($this->any())
      ->method('getCurrencyLocale')
      ->will($this->returnValue($currency_locale));

    // ICU, the C library that PHP's Intl extension uses for formatting, is
    // known to have trouble formatting combinations of currencies and locales
    // that it does not know. In order to make sure this works, test such a
    // combination, such as the Ukrainian Hryvnia (UAH) with Dutch, Netherlands
    // (nl_NL).
    $currency_sign = '₴';
    $currency_code = 'UAH';
    $currency = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency->expects($this->any())
      ->method('getCurrencyCode')
      ->will($this->returnValue($currency_code));
    $currency->expects($this->any())
      ->method('getSign')
      ->will($this->returnValue($currency_sign));

    $results = array(
      // An amount with no decimals should be formatted without decimals and
      // decimal separator.
      '123' => '₴-123UAH',
      // An amount with three groupings should have two grouping separators. All
      // of its three decimals should be formatted, even if the currency only
      // has two.
      '1234567.890' => '₴-1%234%567@890UAH',
      // An amount with only one decimal should be formatted with only one.
      '.3' => '₴-0@3UAH',
    );
    foreach ($results as $amount=> $expected) {
      $formatted = $this->formatter->formatAmount($currency, $amount);
      $this->assertSame($formatted, $expected);
    }
  }
}
