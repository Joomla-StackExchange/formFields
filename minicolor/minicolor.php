<?php
/**
 * Custom Joomla! form field to generate minicolors input with optional opacity slider
 *
 * NOTE: you must change custom jquery.minicolors.* files and custom.minicolors.css urls to point where your files are located
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016 All rights reserved.
 * @license MIT
 * @version 1.0.0
 *
 * Example usage:
 *
 * <field
 *  name="color"
 *  type="minicolor"
 *  label="Choose color"
 *  format="rgb"
 *  opacity="0.5" />
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('color');

class JFormFieldMinicolor extends JFormFieldColor {

  protected $type = 'Minicolor';

  /**
   * Method to get the field input markup.
   *
   * @return  string  The field input markup.
   *
   * @since   11.3
   */
  protected function getInput()
  {
    // Translate placeholder text
    $hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

    // Control value can be: hue (default), saturation, brightness, wheel
    $control = $this->control;

    // Valid options are hex and rgb.
    $format  = $this->element['format'];

    // Set to true to enable the opacity slider.
    $opacity = $this->element['opacity'];

    // Position of the panel can be: right (default), left, top or bottom
    $position = ' data-position="' . $this->position . '"';

    $onchange  = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';
    $class     = $this->class;
    $required  = $this->required ? ' required aria-required="true"' : '';
    $disabled  = $this->disabled ? ' disabled' : '';
    $autofocus = $this->autofocus ? ' autofocus' : '';

    $color = strtolower($this->value);

    if (!$color || in_array($color, array('none', 'transparent')))
    {
      $color = 'none';
    }

    $class        = ' class="' . trim('minicolors ' . $class) . '"';
    $control      = $control ? ' data-control="' . $control . '"' : '';
    $format       = $format ? ' data-format="' . $format . '"' : '';
    $hint         = $hint ? ' placeholder="' . $hint . '"' : ' placeholder="rgba(0, 0, 0, ' . ($opacity ? $opacity : '1') . ')"';
    $opacity      = $opacity ? ' data-opacity="' . $opacity . '"' : '';
    $readonly     = $this->readonly ? ' readonly' : '';
    $autocomplete = !$this->autocomplete ? ' autocomplete="off"' : '';

    // Including fallback code for HTML5 non supported browsers.
    JHtml::_('jquery.framework');
    JHtml::_('script', 'system/html5fallback.js', false, true);

    // Include jQuery
    JHtml::_('jquery.framework');

    // We must include our custom minicolors, since Joomla! has outdated version
    // See: https://github.com/claviska/jquery-minicolors/
    // NOTE: Change theses urls to work with your use case
    JHtml::_('script', 'jquery-minicolors/jquery.minicolors.min.js', false, true);
    JHtml::_('stylesheet', 'jquery-minicolors/jquery.minicolors.css', false, true);
    JHtml::_('stylesheet', 'custom.minicolors.css', false, true);

    JFactory::getDocument()->addScriptDeclaration("
        jQuery(document).ready(function (){
          jQuery('input.minicolors').each(function() {
            jQuery(this).minicolors({
              control: jQuery(this).attr('data-control') || 'hue',
              position: jQuery(this).attr('data-position') || 'right',
              format: jQuery(this).attr('data-format') || 'hex',
              opacity: jQuery(this).attr('data-opacity') || false,
              theme: 'bootstrap'
            });
          });
        });
      "
    );

    return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
      . htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '"' . $hint . $class . $position . $control . $format . $opacity
      . $readonly . $disabled . $required . $onchange . $autocomplete . $autofocus . '/>';
  }
}
