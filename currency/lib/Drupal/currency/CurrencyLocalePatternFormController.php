<?php

/**
 * @file
 * Definition of Drupal\currency\CurrencyLocalePatternFormController.
 */

namespace Drupal\currency;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityFormController;

/**
 * Provides a currency_locale_pattern add/edit form.
 */
class CurrencyLocalePatternFormController extends EntityFormController {

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, array &$form_state) {
    // @todo EntityFormController calls entity_form_submit_build_entity(),
    // which copies all top-level form state values to the entity. These values
    // include internal FAPI values and copying those pollutes the entity,
    // which is why we build the entity manually.
    $values = $form_state['values'];
    $currency = clone $this->getEntity($form_state);
    $currency->locale = $values['language_code'] . '_' . $values['country_code'];
    $currency->pattern = $values['pattern'];
    $currency->decimalSeparator = $values['decimal_separator'];
    $currency->groupingSeparator = $values['grouping_separator'];

    return $currency;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    require_once DRUPAL_ROOT . '/core/includes/standard.inc';
    $currency_locale_pattern = $this->getEntity();

    $form['locale'] = array(
      '#title' => t('Locale'),
      '#type' => 'details',
    );
    if ($currency_locale_pattern->locale) {
      list($locale_language_code, $locale_country_code) = explode('_', $currency_locale_pattern->locale);
    }
    else {
      $locale_language_code = NULL;
      $locale_country_code = NULL;
    }
    $options = array();
    foreach (standard_language_list() as $language_code => $language_names) {
      $options[$language_code] = $language_names[0];
    }
    natcasesort($options);
    $form['locale']['language_code'] = array(
      '#default_value' => $locale_language_code,
      '#empty_value' => '',
      '#options' => $options,
      '#required' => TRUE,
      '#title' => t('Language'),
      '#type' => 'select',
    );
    $form['locale']['country_code'] = array(
      '#default_value' => $locale_country_code,
      '#empty_value' => '',
      '#options' => country_get_list(),
      '#required' => TRUE,
      '#title' => t('Country'),
      '#type' => 'select',
    );
    $form['cldr'] = array(
      '#title' => t('Formatting'),
      '#type' => 'details',
    );
    $form['cldr']['pattern'] = array(
      '#default_value' => $currency_locale_pattern->pattern,
      '#description' => t('A Unicode <abbr title="Common Locale Data Repository">CLDR</abbr> <a href="http://cldr.unicode.org/translation/number-patterns">currency number pattern</a>. Non-standard characters are allowed. <code>[XXX]</code> and <code>[999]</code> will be replaced by the ISO 4217 currency code and number.'),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#title' => t('Pattern'),
      '#type' => 'textfield',
    );
    $form['cldr']['decimal_separator'] = array(
      '#default_value' => $currency_locale_pattern->decimalSeparator,
      '#maxlength' => 255,
      '#title' => t('Decimal separator'),
      '#type' => 'textfield',
    );
    $form['cldr']['grouping_separator'] = array(
      '#default_value' => $currency_locale_pattern->groupingSeparator,
      '#maxlength' => 255,
      '#title' => t('Group separator'),
      '#type' => 'textfield',
    );

    return parent::form($form, $form_state, $currency_locale_pattern);
  }

  /**
   * {@inheritdoc}
   */
  function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);
    // If this entity is new, its locale/ID has to be unique.
    $currency_locale_pattern = $this->buildEntity($form, $form_state);
    if ($currency_locale_pattern->isNew()) {
      $currency_locale_pattern_loaded = entity_load('currency_locale_pattern', $currency_locale_pattern->locale);
      if ($currency_locale_pattern_loaded) {
        form_set_error('locale', t(' A pattern for this locale already exists.'));
      }
    }
  }

  /**
   * {@inheritdoc}.
   */
  public function save(array $form, array &$form_state) {
    $currency = $this->getEntity($form_state);
    $currency->save();
    drupal_set_message(t('The locale pattern %label has been saved.', array(
      '%label' => $currency->label(),
    )));
    $form_state['redirect'] = 'admin/config/regional/currency_locale_pattern';
  }

  /**
   * {@inheritdoc}.
   */
  public function delete(array $form, array &$form_state) {
    $currency = $this->getEntity($form_state);
    $uri = $currency->uri();
    $form_state['redirect'] = $uri['path'] .= '/delete';
  }
}
