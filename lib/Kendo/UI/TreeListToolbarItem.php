<?php

namespace Kendo\UI;

class TreeListToolbarItem extends \Kendo\SerializableObject {
    function __construct($name = null) {
        $this->name($name);
    }
//>> Properties

    /**
    * Sets the click option of the TreeListToolbarItem.
    * The click handler of the toolbar command. Used for custom toolbar commands.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeListToolbarItem
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * The name of the toolbar command. Either a built-in ("create", "excel", "pdf") or custom. The name is reflected in one of the CSS classes, which is applied to the button - k-grid-name.
This class can be used to get a reference to the button (after TreeList initialization) and attach click handlers.
    * @param string $value
    * @return \Kendo\UI\TreeListToolbarItem
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * The text displayed by the command button. If not set the name` option would be used as the button text instead.
    * @param string $value
    * @return \Kendo\UI\TreeListToolbarItem
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

//<< Properties
}

?>
