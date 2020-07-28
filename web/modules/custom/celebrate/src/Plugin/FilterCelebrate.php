<?php
namespace Drupal\celebrate\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * @Filter(
 *   id = "filter_celebrate",
 *   title = @Translation("Celebrate Filter"),
 *   description = @Translation("Help this text format celebrate good times!"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class FilterCelebrate extends FilterBase {
  public function process($text, $langcode) {
    return new FilterProcessResult($text);
  }
}
