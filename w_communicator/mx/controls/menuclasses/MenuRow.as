class mx.controls.menuclasses.MenuRow extends mx.controls.listclasses.SelectableRow
{
    var cell, state, owner, icon_branch, branch, type, iconID, icon_mc, icon_sep, createObject, __height, __width, idealWidth;
    function MenuRow()
    {
        super();
    } // End of the function
    function setValue(itemObj, sel)
    {
        var _loc7 = cell;
        var _loc6 = this.itemToString(itemObj);
        if (_loc7.getValue() != _loc6)
        {
            _loc7.setValue(_loc6, itemObj, state);
        } // end if
        var _loc8 = itemObj.hasChildNodes();
        var _loc5 = mx.controls.Menu.isItemEnabled(itemObj);
        var _loc4 = itemObj.attributes.type;
        if (_loc4 == undefined)
        {
            _loc4 = "normal";
        } // end if
        var _loc9 = mx.controls.Menu.isItemSelected(itemObj);
        var _loc3 = owner.__iconFunction(itemObj);
        if (_loc3 == undefined)
        {
            _loc3 = itemObj.attributes[owner.__iconField];
        } // end if
        if (_loc3 == undefined)
        {
            _loc3 = owner.getStyle("defaultIcon");
        } // end if
        if (icon_branch && (_loc8 != branch || _loc5 != isEnabled || type == "separator"))
        {
            icon_branch.removeMovieClip();
            delete this.icon_branch;
        } // end if
        if (_loc9 != selected || _loc3 != iconID || _loc4 != type || _loc5 != isEnabled && _loc4 != "normal")
        {
            icon_mc.removeMovieClip();
            icon_sep.removeMovieClip();
            delete this.icon_sep;
            delete this.icon_mc;
        } // end if
        branch = _loc8;
        isEnabled = _loc5;
        type = _loc4;
        selected = _loc9;
        iconID = _loc3;
        cell.__enabled = isEnabled;
        cell.setColor(isEnabled ? (owner.getStyle("color")) : (owner.getStyle("disabledColor")));
        if (sel == "highlighted")
        {
            if (isEnabled)
            {
                cell.setColor(owner.getStyle("textRollOverColor"));
            } // end if
        }
        else if (sel == "selected")
        {
            if (isEnabled)
            {
                cell.setColor(owner.getStyle("textSelectedColor"));
            } // end if
        } // end else if
        if (branch && icon_branch == undefined)
        {
            icon_branch = this.createObject("MenuBranch" + (isEnabled ? ("Enabled") : ("Disabled")), "icon_branch", 20);
        } // end if
        if (type == "separator")
        {
            if (icon_sep == undefined)
            {
                var _loc10 = this.createObject("MenuSeparator", "icon_sep", 21);
            } // end if
        }
        else if (icon_mc == undefined)
        {
            if (type != "normal")
            {
                if (selected)
                {
                    iconID = (type == "check" ? ("MenuCheck") : ("MenuRadio")) + (isEnabled ? ("Enabled") : ("Disabled"));
                }
                else
                {
                    iconID = undefined;
                } // end if
            } // end else if
            if (iconID != undefined)
            {
                icon_mc = this.createObject(iconID, "icon_mc", 21);
            } // end if
        } // end else if
        this.size();
    } // End of the function
    function itemToString(itmObj)
    {
        if (itmObj.attributes.type == "separator")
        {
            return (" ");
        }
        else
        {
            return (super.itemToString(itmObj));
        } // end else if
    } // End of the function
    function size(Void)
    {
        super.size();
        cell._x = lBuffer;
        cell.setSize(__width - rBuffer - lBuffer, Math.min(__height, cell.getPreferredHeight()));
        if (icon_branch)
        {
            icon_branch._x = __width - rBuffer / 2;
            icon_branch._y = (__height - icon_branch._height) / 2;
        } // end if
        if (icon_sep)
        {
            icon_sep._x = 4;
            icon_sep._y = (__height - icon_sep._height) / 2;
            icon_sep._width = __width - 8;
        }
        else if (icon_mc)
        {
            icon_mc._x = Math.max(0, (lBuffer - icon_mc._width) / 2);
            icon_mc._y = (__height - icon_mc._height) / 2;
        } // end else if
    } // End of the function
    function getIdealWidth(Void)
    {
        cell.draw();
        idealWidth = cell.getPreferredWidth() + 4 + lBuffer + rBuffer;
        return (idealWidth);
    } // End of the function
    var isEnabled = true;
    var selected = false;
    var lBuffer = 18;
    var rBuffer = 15;
} // End of Class
