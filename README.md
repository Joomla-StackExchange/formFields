# Custom Joomla! form fields

This repository is holding all custom form fields.

## Available custom fields

Every form field is in it's own directory with instuctions to usage. Please see stucture of this repo.

## How to add custom form fields?

To add a custom form fields, you should add your fields directory to include path.

For example:
````php
JForm::addFieldPath(PATH_TO_MY_EXTENSION . '/fields');
````

For component, it usually means
````php
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
````

## Resources

[Creating a custom form field type][1]

[Standard form field types][2]

[Form field][3]

[Joomla! Developers Documentation][4]

[1]:https://docs.joomla.org/Creating_a_custom_form_field_type
[2]:https://docs.joomla.org/Standard_form_field_types
[3]:https://docs.joomla.org/Form_field
[4]:https://docs.joomla.org/Portal:Developers