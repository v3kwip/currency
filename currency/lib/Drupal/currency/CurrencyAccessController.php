<?php

/**
 * @file
 * Definition of Drupal\currency\CurrencyAccessController.
 */

namespace Drupal\currency;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Language\Language;

/**
 * Defines the default list controller for ConfigEntity objects.
 */
class CurrencyAccessController extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, $langcode = Language::LANGCODE_DEFAULT, User $account = NULL) {
    return user_access('currency.currency.' . $operation, $account) && parent::access($entity, $operation, $langcode, $account);
  }
}
