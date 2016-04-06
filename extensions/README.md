# Extensions dropdown list

This form field will show installed and enabled extensions

## Options (attributes)

**folder**: shows only plugins in specific folder

**extensiontype**: extension type. Defaults to `component`.

**clientid**: client id to retrieve extensions for. 1 - backend, 0 - frontend. Defaults to `1`.

> **Note:** For plugins and libraries this is forced to be `0`, since plugins and libraries can't specify client id, therefore it is always `0` and it would be pointless to specify.

**onlyusers**: show only user installed extensions. Defaults to `false`.

> **Note:** this just checks that extension manifest won't have Joomla! Project as an author. So if Joomla! developers would have a typo or change it to something else, this wont work.

**enabled**: should we show enabled or disabled extensions. Defaults to `true`.

## Example usage

````xml
<!-- Show all frontend enabled components -->
<field
  name="componentpicker"
  type="extensions"
  label="Choose component"
  >
  <option value="">Please choose component</option>
</field>

<!-- Show backend modules -->
<field
  name="modulepicker"
  type="extensions"
  extensiontype="module"
  clientid="1"
  label="Choose module"
  >
  <option value="">Please choose module</option>
</field>

<!-- Show all enabled plugins -->
<field
  name="pluginpicker"
  type="extensions"
  extensiontype="plugin"
  label="Choose plugin"
  >
  <option value="">Please choose plugin</option>
</field>
```

## Changelog

**2016-04-06** Initial release