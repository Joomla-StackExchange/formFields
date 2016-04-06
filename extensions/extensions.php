<?php
/**
 * Custom Joomla! form field to generate extensions dropdown list
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016 All rights reserved.
 * @license MIT
 * @version 1.0.0
 *
 * Usage examples:
 *
 *  <field
 *    name="componentpicker"
 *    type="extensions"
 *    label="Choose component"
 *    >
 *    <option value="">Please choose component</option>
 *  </field>
 *
 *  <field
 *    name="modulepicker"
 *    type="extensions"
 *    extensiontype="module"
 *    clientid="1"
 *    label="Choose module"
 *    >
 *    <option value="">Please choose module</option>
 *  </field>
 *
 *  <field
 *    name="pluginpicker"
 *    type="extensions"
 *    extensiontype="plugin"
 *    clientid="1"
 *    label="Choose plugin"
 *    >
 *    <option value="">Please choose plugin</option>
 *  </field>
 *
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldExtensions extends JFormFieldList
{
  /**
   * The field type.
   *
   * @var    string
   */
  protected $type = 'Extensions';

  /**
   * The extension type to seaarch for.
   *
   * @var    string
   */
  protected $extensionType = 'component';

  /**
   * The path to folder for plugins.
   *
   * @var    string
   */
  protected $folder;

  /**
   * The client if to search extensions.
   *
   * 1 - backend, 0 - frontend
   *
   * @var    int
   */
  protected $clientId;

  /**
   * Show only user installed extensions
   *
   * @var    boolean
   */
  protected $onlyUsers;

  /**
   * Show only enabled extensions
   *
   * @var    boolean
   */
  protected $enabled;

  /**
   * Method to get certain otherwise inaccessible properties from the form field object.
   *
   * @param   string  $name  The property name for which to the the value.
   *
   * @return  mixed  The property value or null.
   */
  public function __get($name)
  {
    switch ($name)
    {
      case 'folder':
      case 'extensionType':
      case 'clientId':
      case 'onlyUsers':
      case 'enabled':
        return $this->{$name};
    }

    return parent::__get($name);
  }

  /**
   * Method to set certain otherwise inaccessible properties of the form field object.
   *
   * @param   string  $name   The property name for which to the the value.
   * @param   mixed   $value  The value of the property.
   *
   * @return  void
   */
  public function __set($name, $value)
  {
    switch ($name)
    {
      case 'folder':
        $this->folder = (string) $value;
        break;

      case 'extensiontype':
        $this->extensionType = (string) $value;
        break;

      case 'clientId':
        $this->clientId = (int) $value;
        break;

      case 'onlyUsers':
      case 'enabled':
        $this->{$name} = (boolean) $value;
        break;

      default:
        parent::__set($name, $value);
    }
  }

  /**
   * Method to attach a JForm object to the field.
   *
   * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
   * @param   mixed             $value    The form field value to validate.
   * @param   string            $group    The field name group control value. This acts as as an array container for the field.
   *                                      For example if the field has name="foo" and the group value is set to "bar" then the
   *                                      full field name would end up being "bar[foo]".
   *
   * @return  boolean  True on success.
   *
   * @see     JFormField::setup()
   */
  public function setup(SimpleXMLElement $element, $value, $group = null)
  {
    $return = parent::setup($element, $value, $group);

    if ($return)
    {
      $this->folder        = (string) $this->element['folder'];
      $this->extensionType = $this->element['extensiontype'] ? (string) $this->element['extensiontype'] : 'component';
      $this->clientId      = $this->element['clientid'] ? (int) $this->element['clientid'] : 1;
      $this->onlyUsers     = $this->element['onlyusers'] ? filter_var($this->element['onlyusers'], FILTER_VALIDATE_BOOLEAN) : false;
      $this->enabled       = $this->element['enabled'] ? filter_var($this->element['enabled'], FILTER_VALIDATE_BOOLEAN) : true;

      // Force client id to be 0 for plugins and libraries
      if ($this->extensionType === 'plugin' || $this->extensionType == 'library')
      {
        $this->clientId = 0;
      }
    }

    return $return;
  }

  /**
   * Method to get a list of options for a list input.
   *
   * @return  array  An array of JHtml options.
   */
  protected function getOptions()
  {
    $folder    = $this->folder;
    $type      = $this->extensionType;
    $clientId  = $this->clientId;
    $onlyUsers = $this->onlyUsers;
    $enabled   = $this->enabled;
    $options   = array();

    if (!empty($type))
    {
      // Get list of plugins
      $db    = JFactory::getDbo();
      $query = $db->getQuery(true)
        ->select('element AS value, name AS text, folder')
        ->from($db->quoteName('#__extensions'))
        ->where($db->quoteName('enabled') . ' = ' . $db->quote($enabled ? 1 : 0))
        ->order('ordering, name');

      if (strlen($type) > 0)
      {
        $query->where($db->quoteName('type') . ' = ' . $db->quote($type));
      }

      if ($type === 'plugin' && !empty($folder))
      {
        $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
      }

      if (in_array($clientId, array(0, 1)))
      {
        $query->where($db->quoteName('client_id') . ' = ' . $db->quote($clientId));
      }

      if ($onlyUsers)
      {
        $query->where($db->quoteName('manifest_cache') . " NOT LIKE '%Joomla! Project%'");
      }

      $db->setQuery($query);
      print_r($query->__toString());
      $options = $db->loadObjectList();

      $lang = JFactory::getLanguage();

      foreach ($options as $i => $item)
      {

        $extension = ($type === 'plugin') ? 'plg_' . $item->folder . '_' . $item->value : $item->text;

        if ($type === 'plugin')
        {
          $source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->value;
          $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true) || $lang->load($extension, $source, null, false, true);
        }
        else
        {
          $lang->load($extension . '.sys', $clientId === 1 ? JPATH_ADMINISTRATOR : JPATH_SITE, null, false, true) || $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true);
        }

        $options[$i]->text = JText::_($item->text);
      }
    }
    else
    {
      JLog::add(JText::_('No extensions found.'), JLog::WARNING, 'jerror');
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), (array) $options);

    return $options;
  }
}
