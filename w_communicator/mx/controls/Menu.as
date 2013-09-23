class mx.controls.Menu extends mx.controls.listclasses.ScrollSelectList
{
    var __activator, __set__visible, mask_mc, listContent, border_mc, __menuCache, getViewMetrics, __height, __width, layoutContent, invRowHeight, invUpdateSize, getFocusManager, __menuDataProvider, groupName, __get__visible, __menuBar, _x, _y, _width, attachMovie, popupMask, setMask, __get__width, height, getStyle, wasJustCreated, popupTween, isPressed, width, __activeChildren, __lastRowRolledOver, clearSelected, anchorRow, supposedToLoseFocus, __dataProvider, invalidate, getLength, __rowCount, setSize, rows, __namedItems, _loc6, __radioGroups, _loc5, _selection, _members, enabled, selectable, __anchor, __parentMenu, __anchorIndex, __vPosition, dragScrolling, __timer, __timeOut, focusManager, getSelectedIndex, wasKeySelected, selectRow, selectedIndex, selectedItem;
    function Menu()
    {
        super();
    } // End of the function
    static function createMenu(parent, mdp, initObj)
    {
        if (parent == undefined)
        {
            parent = _root;
        } // end if
        var pt = new Object();
        pt.x = parent._root._xmouse;
        pt.y = parent._root._ymouse;
        parent._root.localToGlobal(pt);
        if (mdp == undefined)
        {
            mdp = new XML();
        } // end if
        var _loc3 = mx.managers.PopUpManager.createPopUp(parent, mx.controls.Menu, false, initObj, true);
        if (_loc3 == undefined)
        {
        }
        else
        {
            _loc3.isPressed = true;
            _loc3.mouseDownOutsideHandler = function (event)
            {
                if (!this.isMouseOverMenu() && !__activator.hitTest(pt.x, pt.y))
                {
                    this.hideAllMenus();
                } // end if
            };
            _loc3.dataProvider = mdp;
        } // end else if
        return (_loc3);
    } // End of the function
    static function isItemEnabled(itm)
    {
        var _loc1 = itm.attributes.enabled;
        return ((_loc1 == undefined || _loc1 == true || _loc1.toLowerCase() == "true") && itm.attributes.type.toLowerCase() != "separator");
    } // End of the function
    static function isItemSelected(itm)
    {
        var _loc1 = itm.attributes.selected;
        return (_loc1 == true || _loc1.toLowerCase() == "true");
    } // End of the function
    function init(Void)
    {
        super.init();
        this.__set__visible(false);
    } // End of the function
    function createChildren(Void)
    {
        super.createChildren();
        listContent.setMask((MovieClip)(mask_mc));
        mask_mc.removeMovieClip();
        border_mc.move(0, 0);
        border_mc.borderStyle = "menuBorder";
    } // End of the function
    function propagateToSubMenus(prop, value)
    {
        for (var _loc5 in __menuCache)
        {
            var _loc2 = __menuCache[_loc5];
            if (_loc2 != this)
            {
                _loc2["set" + prop](value);
            } // end if
        } // end of for...in
    } // End of the function
    function setLabelField(lbl)
    {
        super.setLabelField(lbl);
        this.propagateToSubMenus("LabelField", lbl);
    } // End of the function
    function setLabelFunction(lbl)
    {
        super.setLabelFunction(lbl);
        this.propagateToSubMenus("LabelFunction", lbl);
    } // End of the function
    function setCellRenderer(cR)
    {
        super.setCellRenderer(cR);
        this.propagateToSubMenus("CellRenderer", cR);
    } // End of the function
    function setRowHeight(v)
    {
        super.setRowHeight(v);
        this.propagateToSubMenus("RowHeight", v);
    } // End of the function
    function setIconField(v)
    {
        super.setIconField(v);
        this.propagateToSubMenus("IconField", v);
    } // End of the function
    function setIconFunction(v)
    {
        super.setIconFunction(v);
        this.propagateToSubMenus("IconFunction", v);
    } // End of the function
    function size(Void)
    {
        super.size();
        var _loc3 = this.getViewMetrics();
        this.layoutContent(_loc3.left, _loc3.top, __width - _loc3.left - _loc3.right, __height - _loc3.top - _loc3.bottom);
    } // End of the function
    function draw(Void)
    {
        if (invRowHeight)
        {
            super.draw();
            listContent.setMask((MovieClip)(mask_mc));
            invUpdateSize = true;
        } // end if
        super.draw();
        if (invUpdateSize)
        {
            this.updateSize();
        } // end if
    } // End of the function
    function onSetFocus()
    {
        super.onSetFocus();
        this.getFocusManager().defaultPushButtonEnabled = false;
    } // End of the function
    function setDataProvider(dP)
    {
        if (typeof(dP) == "string")
        {
            dP = new XML(dP).firstChild;
        } // end if
        __menuDataProvider.removeEventListener("modelChanged", this);
        __menuDataProvider = dP;
        if (!(__menuDataProvider instanceof XML))
        {
            __menuDataProvider.isTreeRoot = true;
        } // end if
        __menuDataProvider.addEventListener("modelChanged", this);
        this.modelChanged({eventName: "updateTree"});
    } // End of the function
    function getDataProvider()
    {
        return (__menuDataProvider);
    } // End of the function
    function addMenuItem(arg)
    {
        return (__menuDataProvider.addMenuItem(arg));
    } // End of the function
    function addMenuItemAt(index, arg)
    {
        return (__menuDataProvider.addMenuItemAt(index, arg));
    } // End of the function
    function removeMenuItemAt(index)
    {
        var _loc2 = this.getMenuItemAt(index);
        if (_loc2 != undefined && _loc2 != null)
        {
            _loc2.removeMenuItem();
        } // end if
        return (_loc2);
    } // End of the function
    function removeMenuItem(item)
    {
        return (this.removeMenuItemAt(this.indexOf(item)));
    } // End of the function
    function removeAll(Void)
    {
        return (__menuDataProvider.removeAll());
    } // End of the function
    function getMenuItemAt(index)
    {
        return (__menuDataProvider.getMenuItemAt(index));
    } // End of the function
    function setMenuItemSelected(item, select)
    {
        if (item.attributes.type == "radio")
        {
            var _loc3 = this.getRootMenu();
            groupName = item.attributes.groupName;
            _loc3[groupName].setGroupSelection(item);
            return;
        } // end if
        if (select != item.attributes.selected)
        {
            item.attributes.selected = select;
            item.updateViews({eventName: "selectionChanged", node: item});
        } // end if
    } // End of the function
    function setMenuItemEnabled(item, enable)
    {
        if (enable != item.attributes.enabled)
        {
            item.attributes.enabled = enable;
            item.updateViews({eventName: "enabledChanged", node: item});
        } // end if
    } // End of the function
    function indexOf(item)
    {
        return (__menuDataProvider.indexOf(item));
    } // End of the function
    function show(x, y)
    {
        if (!this.__get__visible())
        {
            var _loc2 = this.getRootMenu();
            _loc2.dispatchEvent({type: "menuShow", menuBar: __menuBar, menu: this, menuItem: __menuDataProvider});
            if (x != undefined)
            {
                _x = x;
                if (y != undefined)
                {
                    _y = y;
                } // end if
            } // end if
            if (this != _loc2)
            {
                var _loc5 = _x + _width - Stage.width;
                if (_loc5 > 0)
                {
                    _x = _x - _loc5;
                    if (_x < 0)
                    {
                        _x = 0;
                    } // end if
                } // end if
            } // end if
            popupMask = this.attachMovie("BoundingBox", "pMask_mc", 6000);
            this.setMask(popupMask);
            var _loc3 = this.__get__width();
            if (_loc3 < 50)
            {
                _loc3 = 100;
            } // end if
            popupMask._width = _loc3;
            popupMask._height = height;
            popupMask._x = -popupMask._width;
            popupMask._y = -popupMask._height;
            var _loc4 = this.getStyle("popupDuration");
            if (wasJustCreated && _loc4 < 200)
            {
                _loc4 = 200;
                delete this.wasJustCreated;
            } // end if
            popupTween = new mx.effects.Tween(this, [popupMask._x, popupMask._y], [0, 0], _loc4);
            this.__set__visible(true);
            isPressed = true;
            if (!__menuBar && _loc2 == this)
            {
                Selection.setFocus(this);
            } // end if
        } // end if
    } // End of the function
    function onTweenUpdate(val)
    {
        popupMask._width = width;
        popupMask._x = val[0];
        popupMask._y = val[1];
    } // End of the function
    function onTweenEnd(val)
    {
        popupMask._x = val[0];
        popupMask._y = val[1];
        this.setMask(undefined);
        popupMask.removeMovieClip();
    } // End of the function
    function hide(Void)
    {
        if (this.__get__visible())
        {
            for (var _loc2 in __activeChildren)
            {
                __activeChildren[_loc2].hide();
            } // end of for...in
            __lastRowRolledOver = undefined;
            this.clearSelected();
            if (anchorRow != undefined)
            {
                anchorRow.highlight._visible = false;
            } // end if
            this.__set__visible(false);
            isPressed = false;
            __wasVisible = false;
            var _loc3 = this.getRootMenu();
            _loc3.dispatchEvent({type: "menuHide", menuBar: __menuBar, menu: this, menuItem: __menuDataProvider});
        } // end if
    } // End of the function
    function onKillFocus()
    {
        super.onKillFocus();
        this.getFocusManager().defaultPushButtonEnabled = true;
        if (supposedToLoseFocus == undefined)
        {
            this.hideAllMenus();
        } // end if
        delete this.supposedToLoseFocus;
    } // End of the function
    function modelChanged(eventObj)
    {
        var _loc4 = eventObj.eventName;
        if (_loc4 == "updateTree")
        {
            __dataProvider.removeAll();
            __dataProvider.addItemsAt(0, __menuDataProvider.childNodes);
            invUpdateSize = true;
            this.invalidate();
            super.modelChanged({eventName: "updateAll"});
            this.deinstallAllItems();
            this.installItem(__menuDataProvider);
            if (__menuCache == undefined)
            {
                __menuCache = new Object();
            } // end if
            __menuCache[__menuDataProvider.getID()] = this;
        }
        else if (_loc4 == "addNode" || _loc4 == "removeNode")
        {
            var _loc5 = eventObj.node;
            var _loc7 = eventObj.parentNode;
            var _loc8 = __menuCache[_loc7.getID()];
            if (_loc4 == "removeNode")
            {
                this.deleteDependentSubMenus(_loc5);
                _loc8.removeItemAt(eventObj.index);
                this.deinstallItem(_loc5);
            }
            else
            {
                _loc8.addItemAt(eventObj.index, _loc5);
                this.installItem(_loc5);
            } // end else if
            _loc8.invUpdateSize = true;
            _loc8.invalidate();
            var _loc6 = __menuCache[_loc7.parentNode.getID()];
            _loc6.invUpdateControl = true;
            _loc6.invalidate();
        }
        else if (_loc4 == "selectionChanged" || _loc4 == "enabledChanged")
        {
            _loc8 = __menuCache[eventObj.node.parentNode.getID()];
            _loc8.invUpdateControl = true;
            _loc8.invalidate();
        }
        else
        {
            super.modelChanged(eventObj);
        } // end else if
    } // End of the function
    function updateSize()
    {
        delete this.invUpdateSize;
        var _loc2 = this.calcHeight();
        if (this.getLength() != __rowCount)
        {
            this.setSize(0, _loc2);
        } // end if
        this.setSize(this.calcWidth(), _loc2);
    } // End of the function
    function calcWidth()
    {
        var _loc4 = -1;
        var _loc3;
        for (var _loc2 = 0; _loc2 < rows.length; ++_loc2)
        {
            _loc3 = rows[_loc2].getIdealWidth();
            if (_loc3 > _loc4)
            {
                _loc4 = _loc3;
            } // end if
        } // end of for
        var _loc5 = this.getStyle("textIndent");
        if (_loc5 == undefined)
        {
            _loc5 = 0;
        } // end if
        return (_loc4 + _loc5);
    } // End of the function
    function calcHeight()
    {
        var _loc2 = this.getViewMetrics();
        return (__dataProvider.length * __rowHeight + _loc2.top + _loc2.bottom);
    } // End of the function
    function invalidateStyle(propName)
    {
        super.invalidateStyle(propName);
        for (var _loc4 in __activeChildren)
        {
            __activeChildren[_loc4].invalidateStyle(propName);
        } // end of for...in
    } // End of the function
    function notifyStyleChangeInChildren(sheetName, styleProp, newValue)
    {
        super.notifyStyleChangeInChildren(sheetName, styleProp, newValue);
        for (var _loc6 in __activeChildren)
        {
            __activeChildren[_loc6].notifyStyleChangeInChildren(sheetName, styleProp, newValue);
        } // end of for...in
    } // End of the function
    function deleteDependentSubMenus(menuItem)
    {
        var _loc2 = menuItem.childNodes;
        for (var _loc3 in _loc2)
        {
            this.deleteDependentSubMenus(_loc2[_loc3]);
        } // end of for...in
        var _loc4 = __menuCache[menuItem.getID()];
        if (_loc4 != undefined)
        {
            _loc4.hide();
            delete __menuCache[menuItem.getID()];
        } // end if
    } // End of the function
    function installItem(item)
    {
        if (item.attributes.instanceName != undefined)
        {
            var _loc6 = item.attributes.instanceName;
            if (this[_loc6] != undefined)
            {
            } // end if
            if (__namedItems == undefined)
            {
                __namedItems = new Object();
            } // end if
            __namedItems[_loc6] = item;
            this[_loc6] = item;
        } // end if
        if (item.attributes.type == "radio" && item.attributes.groupName != undefined)
        {
            var _loc5 = item.attributes.groupName;
            var _loc2 = this[_loc5];
            if (_loc2 == undefined)
            {
                _loc2 = new Object();
                _loc2.name = _loc5;
                _loc2._rootMenu = this;
                _loc2._members = new Object();
                _loc2._memberCount = 0;
                _loc2.getGroupSelection = getGroupSelection;
                _loc2.setGroupSelection = setGroupSelection;
                _loc2.addProperty("selection", _loc2.getGroupSelection, _loc2.setGroupSelection);
                if (__radioGroups == undefined)
                {
                    __radioGroups = new Object();
                } // end if
                __radioGroups[_loc5] = _loc2;
                this[_loc5] = _loc2;
            } // end if
            _loc2._members[item.getID()] = item;
            ++_loc2._memberCount;
            if (mx.controls.Menu.isItemSelected(item))
            {
                _loc2.selection = item;
            } // end if
        } // end if
        var _loc3 = item.childNodes;
        for (var _loc7 in _loc3)
        {
            this.installItem(_loc3[_loc7]);
        } // end of for...in
    } // End of the function
    function deinstallItem(item)
    {
        var _loc2 = item.childNodes;
        for (var _loc5 in _loc2)
        {
            this.deinstallItem(_loc2[_loc5]);
        } // end of for...in
        if (item.attributes.instanceName != undefined)
        {
            var _loc7 = item.attributes.instanceName;
            delete this[_loc7];
            delete __namedItems[_loc7];
        } // end if
        if (item.attributes.type == "radio" && item.attributes.groupName != undefined)
        {
            var _loc6 = item.attributes.groupName;
            var _loc3 = this[_loc6];
            if (_loc3 == undefined)
            {
                return;
            } // end if
            delete _loc3._members[item.getID()];
            --_loc3._memberCount;
            if (_loc3._memberCount == 0)
            {
                delete this[_loc6];
                delete __radioGroups[_loc6];
            }
            else if (_loc3.selection == item)
            {
                delete _loc3._selection;
            } // end if
        } // end else if
    } // End of the function
    function deinstallAllItems(Void)
    {
        for (var _loc2 in __namedItems)
        {
            delete this[_loc2];
        } // end of for...in
        delete this.__namedItems;
        for (var _loc2 in __radioGroups)
        {
            delete this[_loc2];
        } // end of for...in
        delete this.__radioGroups;
    } // End of the function
    function getGroupSelection()
    {
        return (_selection);
    } // End of the function
    function setGroupSelection(item)
    {
        _selection = item;
        for (var _loc4 in _members)
        {
            var _loc2 = _members[_loc4];
            _loc2.attributes.selected = _loc2 == item;
        } // end of for...in
        item.updateViews({eventName: "selectionChanged", node: item});
    } // End of the function
    function onRowRelease(rowIndex)
    {
        if (!enabled || !selectable || !this.__get__visible())
        {
            return;
        } // end if
        var _loc5 = rows[rowIndex];
        var _loc2 = _loc5.item;
        if (_loc2 != undefined && mx.controls.Menu.isItemEnabled(_loc2))
        {
            var _loc10 = _loc2.attributes.type;
            var _loc4 = !_loc2.hasChildNodes() && _loc10 != "separator";
            if (_loc4)
            {
                this.hideAllMenus();
            } // end if
            var _loc6;
            var _loc3 = this.getRootMenu();
            if (_loc10 == "check" || _loc10 == "radio")
            {
                this.setMenuItemSelected(_loc2, !mx.controls.Menu.isItemSelected(_loc2));
            } // end if
            if (_loc4)
            {
                _loc3.dispatchEvent({type: "change", menuBar: __menuBar, menu: _loc3, menuItem: _loc2, groupName: _loc2.attributes.groupName});
            } // end if
        } // end if
    } // End of the function
    function onRowPress(rowIndex)
    {
        var _loc3 = rows[rowIndex].item;
        if (mx.controls.Menu.isItemEnabled(_loc3) && !_loc3.hasChildNodes())
        {
            super.onRowPress(rowIndex);
        } // end if
    } // End of the function
    function onRowRollOut(rowIndex)
    {
        if (!enabled || !selectable || !this.__get__visible())
        {
            return;
        } // end if
        super.onRowRollOut(rowIndex);
        var _loc4 = rows[rowIndex].item;
        if (_loc4 != undefined)
        {
            var _loc5 = this.getRootMenu();
            _loc5.dispatchEvent({type: "rollOut", menuBar: __menuBar, menu: this, menuItem: _loc4});
        } // end if
        var _loc3 = __activeChildren[_loc4.getID()];
        if (_loc4.hasChildNodes() > 0)
        {
            if (_loc3.isOpening || _loc3.isOpening == undefined)
            {
                this.cancelMenuDelay();
                _loc3.isOpening = false;
            } // end if
            if (_loc3.visible)
            {
                rows[rowIndex].drawRow(_loc4, "selected", false);
            } // end if
        }
        else if (_loc3.isClosing || _loc3.isClosing == undefined)
        {
            this.cancelMenuDelay();
            _loc3.isClosing = false;
        } // end else if
        this.setTimeOut(__closeDelay, _loc4.getID());
    } // End of the function
    function onRowRollOver(rowIndex)
    {
        if (!enabled || !selectable || !this.__get__visible())
        {
            return;
        } // end if
        var _loc2 = rows[rowIndex];
        var _loc8 = _loc2.item;
        var _loc6 = _loc8.getID();
        var _loc4 = __activeChildren[__anchor];
        var _loc5 = __activeChildren[_loc6];
        this.clearSelected();
        this.clearTimeOut();
        __lastRowRolledOver = rowIndex;
        if (anchorRow != undefined)
        {
            anchorRow.drawRow(anchorRow.item, "normal", false);
            delete this.anchorRow;
        } // end if
        if (__parentMenu)
        {
            var _loc3 = __parentMenu.rows[__anchorIndex];
            _loc3.drawRow(_loc3.item, "selected", false);
            __parentMenu.anchorRow = _loc3;
        } // end if
        if (_loc5.__activeChildren[_loc5.__anchor].visible)
        {
            _loc5.__activeChildren[_loc5.__anchor].hide();
        } // end if
        if (_loc4.visible && __anchor != _loc6)
        {
            _loc4.isClosing = true;
            this.setMenuDelay(__closeDelay, "closeSubMenu", {id: __anchor});
        } // end if
        if (_loc8 != undefined && mx.controls.Menu.isItemEnabled(_loc8))
        {
            var _loc7 = this.getRootMenu();
            _loc7.dispatchEvent({type: "rollOver", menuBar: __menuBar, menu: this, menuItem: _loc8});
            if (_loc8.hasChildNodes() > 0)
            {
                anchorRow = _loc2;
                _loc2.drawRow(_loc8, "selected", false);
                if (!_loc5.visible)
                {
                    _loc5.isOpening = true;
                    this.setMenuDelay(__openDelay, "openSubMenu", {item: _loc8, rowIndex: rowIndex});
                } // end if
            }
            else
            {
                _loc2.drawRow(_loc8, "highlighted", false);
            } // end if
        } // end else if
    } // End of the function
    function onRowDragOver(rowIndex)
    {
        var _loc4 = __dataProvider.getItemAt(rowIndex + __vPosition);
        if (mx.controls.Menu.isItemEnabled(_loc4))
        {
            super.onRowDragOver(rowIndex);
            this.onRowRollOver(rowIndex);
        } // end if
    } // End of the function
    function __onMouseUp()
    {
        clearInterval(dragScrolling);
        delete this.dragScrolling;
        delete this.isPressed;
        if (!selectable)
        {
            return;
        } // end if
        if (__wasVisible)
        {
            this.hide();
        } // end if
        __wasVisible = false;
    } // End of the function
    function setMenuDelay(delay, request, args)
    {
        if (__timer == null)
        {
            __timer = setInterval(this, "callMenuDelay", delay, request, args);
        }
        else
        {
            __delayQueue.push({delay: delay, request: request, args: args});
        } // end else if
    } // End of the function
    function callMenuDelay(request, args)
    {
        this[request](args);
        this.clearMenuDelay();
    } // End of the function
    function clearMenuDelay(Void)
    {
        clearInterval(__timer);
        __timer = null;
        this.runDelayQueue();
    } // End of the function
    function cancelMenuDelay(Void)
    {
        var _loc2 = __delayQueue.pop();
        this.clearMenuDelay();
    } // End of the function
    function runDelayQueue(Void)
    {
        if (__delayQueue.length == 0)
        {
            return;
        } // end if
        var _loc2 = __delayQueue.shift();
        var _loc4 = _loc2.delay;
        var _loc5 = _loc2.request;
        var _loc3 = _loc2.args;
        this.setMenuDelay(_loc4, _loc5, _loc3);
    } // End of the function
    function setTimeOut(delay, id)
    {
        this.clearTimeOut();
        __timeOut = setInterval(this, "callTimeOut", delay, id);
    } // End of the function
    function clearTimeOut(Void)
    {
        clearInterval(__timeOut);
        __timeOut = null;
    } // End of the function
    function callTimeOut(Void)
    {
        var _loc2 = __activeChildren[__anchor];
        this.clearTimeOut();
        if (!this.isMouseOverMenu() && _loc2)
        {
            var _loc3 = _loc2.__anchorIndex;
            var _loc5 = __dataProvider.getItemAt(_loc3 + __vPosition);
            var _loc4 = rows[_loc3];
            _loc4.drawRow(_loc5, "normal", false);
            _loc2.hide();
            __delayQueue.length = 0;
        } // end if
    } // End of the function
    function openSubMenu(o)
    {
        var _loc3 = this.getRootMenu();
        var _loc5 = rows[o.rowIndex];
        var _loc7 = o.item;
        var _loc6 = __anchor = _loc7.getID();
        var _loc2 = _loc3.__menuCache[_loc6];
        if (_loc2 == undefined)
        {
            _loc2 = mx.managers.PopUpManager.createPopUp(_loc3, mx.controls.Menu, false, {__parentMenu: this, __anchorIndex: o.rowIndex, styleName: _loc3}, true);
            _loc2.labelField = _loc3.__labelField;
            _loc2.labelFunction = _loc3.__labelFunction;
            _loc2.iconField = _loc3.__iconField;
            _loc2.iconFunction = _loc3.__iconFunction;
            _loc2.wasJustCreated = true;
            _loc2.cellRenderer = _loc3.__cellRenderer;
            _loc2.rowHeight = _loc3.__rowHeight;
            if (_loc3.__menuCache == undefined)
            {
                _loc3.__menuCache = new Object();
                _loc3.__menuCache[_loc3.__menuDataProvider.getID()] = _loc3;
            } // end if
            if (__activeChildren == undefined)
            {
                __activeChildren = new Object();
            } // end if
            _loc3.__menuCache[_loc6] = _loc2;
            __activeChildren[_loc6] = _loc2;
            _loc2.__dataProvider.addItemsAt(0, _loc7.childNodes);
            _loc2.invUpdateSize = true;
            _loc2.invalidate();
        } // end if
        _loc2.__menuBar = __menuBar;
        var _loc4 = {x: 0, y: 0};
        _loc5.localToGlobal(_loc4);
        _loc5._root.globalToLocal(_loc4);
        _loc2.focusManager.lastFocus = undefined;
        _loc2.show(_loc4.x + _loc5.__width, _loc4.y);
        focusManager.lastFocus = undefined;
        _loc2.isOpening = false;
    } // End of the function
    function closeSubMenu(o)
    {
        var _loc2 = __activeChildren[o.id];
        _loc2.hide();
        _loc2.isClosing = false;
    } // End of the function
    function moveSelBy(incr)
    {
        var _loc3 = this.getSelectedIndex();
        if (_loc3 == undefined)
        {
            _loc3 = -1;
        } // end if
        var _loc2 = _loc3 + incr;
        if (_loc2 > __dataProvider.length - 1)
        {
            _loc2 = 0;
        }
        else if (_loc2 < 0)
        {
            _loc2 = __dataProvider.length - 1;
        } // end else if
        wasKeySelected = true;
        this.selectRow(_loc2 - __vPosition, false, false);
        var _loc4 = __dataProvider.getItemAt(_loc2 + __vPosition);
        if (_loc4.attributes.type == "separator")
        {
            this.moveSelBy(incr);
        } // end if
    } // End of the function
    function keyDown(e)
    {
        if (__lastRowRolledOver != undefined)
        {
            selectedIndex = __lastRowRolledOver;
            __lastRowRolledOver = undefined;
        } // end if
        var _loc2 = selectedItem;
        if (Key.isDown(38))
        {
            var _loc4 = this.getRootMenu();
            var _loc3 = _loc4.__menuCache[_loc2.getID()];
            if (_loc2.hasChildNodes() && _loc3.visible)
            {
                supposedToLoseFocus = true;
                Selection.setFocus(_loc3);
                _loc3.selectedIndex = _loc3.rows.length - 1;
            }
            else
            {
                this.moveSelBy(-1);
            } // end if
        } // end else if
        if (Key.isDown(40))
        {
            _loc4 = this.getRootMenu();
            _loc3 = _loc4.__menuCache[_loc2.getID()];
            if (_loc2.hasChildNodes() && _loc3.visible)
            {
                supposedToLoseFocus = true;
                Selection.setFocus(_loc3);
                _loc3.selectedIndex = 0;
            }
            else
            {
                this.moveSelBy(1);
            } // end if
        } // end else if
        if (Key.isDown(39))
        {
            if (mx.controls.Menu.isItemEnabled(_loc2) && _loc2.hasChildNodes())
            {
                this.openSubMenu({item: _loc2, rowIndex: selectedIndex});
                _loc4 = this.getRootMenu();
                _loc3 = _loc4.__menuCache[_loc2.getID()];
                supposedToLoseFocus = true;
                Selection.setFocus(_loc3);
                _loc3.selectedIndex = 0;
            }
            else if (__menuBar)
            {
                supposedToLoseFocus = true;
                Selection.setFocus(__menuBar);
                __menuBar.keyDown(e);
            } // end if
        } // end else if
        if (Key.isDown(37))
        {
            if (__parentMenu)
            {
                supposedToLoseFocus = true;
                this.hide();
                Selection.setFocus(__parentMenu);
            }
            else if (__menuBar)
            {
                supposedToLoseFocus = true;
                Selection.setFocus(__menuBar);
                __menuBar.keyDown(e);
            } // end if
        } // end else if
        if (Key.isDown(13) || Key.isDown(32))
        {
            if (mx.controls.Menu.isItemEnabled(_loc2) && _loc2.hasChildNodes())
            {
                this.openSubMenu({item: _loc2, rowIndex: selectedIndex});
                _loc4 = this.getRootMenu();
                _loc3 = _loc4.__menuCache[_loc2.getID()];
                supposedToLoseFocus = true;
                Selection.setFocus(_loc3);
                _loc3.selectedIndex = 0;
            }
            else
            {
                this.onRowRelease(selectedIndex);
            } // end if
        } // end else if
        if (Key.isDown(27) || Key.isDown(9))
        {
            this.hideAllMenus();
        } // end if
    } // End of the function
    function hideAllMenus(Void)
    {
        this.getRootMenu().hide();
    } // End of the function
    function isMouseOverMenu(Void)
    {
        var _loc4 = new Object();
        _loc4.x = _root._xmouse;
        _loc4.y = _root._ymouse;
        _root.localToGlobal(_loc4);
        if (border_mc.hitTest(_loc4.x, _loc4.y))
        {
            return (true);
        } // end if
        var _loc5 = this.getRootMenu();
        for (var _loc6 in _loc5.__menuCache)
        {
            var _loc3 = _loc5.__menuCache[_loc6];
            if (_loc3.visible && _loc3.border_mc.hitTest(_loc4.x, _loc4.y))
            {
                return (true);
            } // end if
        } // end of for...in
        return (false);
    } // End of the function
    function getRootMenu(Void)
    {
        for (var _loc2 = this; _loc2.__parentMenu != undefined; _loc2 = _loc2.__parentMenu)
        {
        } // end of for
        return (_loc2);
    } // End of the function
    static var symbolName = "Menu";
    static var symbolOwner = mx.controls.Menu;
    var className = "Menu";
    static var version = "2.0.2.127";
    static var mixit = mx.controls.treeclasses.TreeDataProvider.Initialize(XMLNode);
    static var mixit2 = mx.controls.menuclasses.MenuDataProvider.Initialize(XMLNode);
    var __hScrollPolicy = "off";
    var __vScrollPolicy = "off";
    var __rowRenderer = "MenuRow";
    var __rowHeight = 19;
    var __wasVisible = false;
    var __enabled = true;
    var __openDelay = 250;
    var __closeDelay = 250;
    var __delayQueue = new Array();
    var __iconField = "icon";
} // End of Class
