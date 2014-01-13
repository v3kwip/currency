<?php

/**
 * @file Contains \Drupal\currencu\ExchangeRateProviderInterface.
 */

namespace Drupal\currency;

/**
 * Describes a currency exchange rate provider.
 */
interface ExchangeRateProviderInterface {

  /**
   * Returns the exchange rate for two currencies.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   *
   * @return string|false
   *   A numeric string if the rate could be found, FALSE if it couldn't.
   */
  function load($currency_code_from, $currency_code_to);

  /**
   * Returns the exchange rates for multiple currency combinations.
   *
   * @param array $currency_codes
   *   Keys are the ISO 4217 codes of source currencies, values are arrays that
   *   contain ISO 4217 codes of destination currencies. Example:
   *   array(
   *     'EUR' => array('NLG', 'DEM', 'XXX'),
   *   )
   *
   * @return array
   *   Keys are the ISO 4217 codes of source currencies, values are arrays of
   *   which the keys are ISO 4217 codes of destination currencies and values
   *   are the exchange rates as numeric strings, or FALSE for combinations of
   *   currencies for which no exchange rate could be found. Example:
   *   array(
   *     'EUR' => array(
   *       'NLG' => 2.20371,
   *       'DEM' => 1.95583,
   *       'XXX' => NULL,
   *     ),
   *   )
   */
  function loadMultiple(array $currency_codes);
}
