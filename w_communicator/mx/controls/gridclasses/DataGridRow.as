class mx.controls.gridclasses.DataGridRow extends mx.controls.listclasses.SelectableRow
{
    var setupBG, createEmptyMovieClip, colBG, cells, owner, backGround, createObject, createClassObject, createLabel, text, draw, textHeight, columnIndex, listOwner, __height, grandOwner, wasPressed, onPress;
    function DataGridRow()
    {
        super();
    } // End of the function
    function createChildren(Void)
    {
        this.setupBG();
        colBG = this.createEmptyMovieClip("colbG_mc", mx.controls.listclasses.SelectableRow.LOWEST_DEPTH + 1);
    } // End of the function
    function init(Void)
    {
        super.init();
        cells = new Array();
    } // End of the function
    function size(Void)
    {
        if (cells.length != owner.columns.length)
        {
            this.createCells();
        } // end if
        super.size();
    } // End of the function
    function createCells(Void)
    {
        this.clearCells();
        backGround.onRelease = startEditCell;
        var _loc7 = owner.columns.length;
        for (var _loc2 = 0; _loc2 < _loc7; ++_loc2)
        {
            var _loc4 = owner.columns[_loc2];
            var _loc5 = _loc4.__cellRenderer;
            if (_loc5 != undefined)
            {
                if (typeof(_loc5) == "string")
                {
                    var _loc3 = cells[_loc2] = this.createObject(_loc5, "fCell" + _loc2, 2 + _loc2, {styleName: _loc4});
                }
                else
                {
                    _loc3 = cells[_loc2] = this.createClassObject(_loc5, "fCell" + _loc2, 2 + _loc2, {styleName: _loc4});
                } // end else if
            }
            else
            {
                _loc3 = cells[_loc2] = this.createLabel("fCell" + _loc2, 2 + _loc2);
                _loc3.styleName = _loc4;
                _loc3.selectable = false;
                _loc3.backGround = false;
                _loc3.border = false;
                _loc3._visible = false;
                _loc3.getPreferredHeight = cellGetPreferredHeight;
            } // end else if
            _loc3.listOwner = owner;
            _loc3.columnIndex = _loc2;
            _loc3.owner = this;
            _loc3.getCellIndex = getCellIndex;
            _loc3.getDataLabel = getDataLabel;
        } // end of for
    } // End of the function
    function cellGetPreferredHeight()
    {
        var _loc3 = text;
        text = "^g_p";
        this.draw();
        var _loc2 = textHeight + 4;
        text = _loc3;
        return (_loc2);
    } // End of the function
    function getCellIndex(Void)
    {
        return ({columnIndex: columnIndex, itemIndex: owner.rowIndex + listOwner.__vPosition});
    } // End of the function
    function getDataLabel()
    {
        return (listOwner.columns[columnIndex].columnName);
    } // End of the function
    function clearCells()
    {
        for (var _loc2 = 0; _loc2 < cells.length; ++_loc2)
        {
            cells[_loc2].removeTextField();
            cells[_loc2].removeMovieClip();
        } // end of for
        cells.splice(0);
    } // End of the function
    function setValue(itmObj, state, transition)
    {
        var _loc7 = owner.columns;
        var _loc8 = _loc7.length;
        for (var _loc3 = 0; _loc3 < _loc8; ++_loc3)
        {
            var _loc6 = cells[_loc3];
            var _loc4 = _loc7[_loc3];
            var _loc2 = _loc4.__labelFunction(itmObj);
            if (_loc2 == undefined)
            {
                _loc2 = itmObj instanceof XMLNode ? (itmObj.attributes[_loc4.columnName]) : (itmObj[_loc4.columnName]);
            } // end if
            if (_loc2 == undefined)
            {
                _loc2 = " ";
            } // end if
            _loc6.setValue(_loc2, itmObj, state);
        } // end of for
    } // End of the function
    function drawCell(cellNum, xPos, w, bgCol)
    {
        var _loc2 = cells[cellNum];
        _loc2._x = xPos;
        _loc2._visible = true;
        _loc2.setSize(w - 2, Math.min(__height, _loc2.getPreferredHeight()));
        _loc2._y = (__height - _loc2.height) / 2;
        if (bgCol != undefined)
        {
            var _loc3 = Math.floor(xPos - 2);
            var _loc4 = Math.floor(_loc3 + w);
            colBG.moveTo(_loc3, 0);
            colBG.beginFill(bgCol);
            colBG.lineStyle(0, 0, 0);
            colBG.lineTo(_loc4, 0);
            colBG.lineTo(_loc4, __height);
            colBG.lineTo(_loc3, __height);
            colBG.endFill();
        } // end if
    } // End of the function
    function setState(newState, transition)
    {
        var _loc6 = owner.columns;
        var _loc4 = _loc6.length;
        if (newState != "normal" || !owner.enabled)
        {
            var _loc5;
            if (!owner.enabled)
            {
                _loc5 = owner.getStyle("disabledColor");
            }
            else if (newState == "highlighted")
            {
                _loc5 = owner.getStyle("textRollOverColor");
            }
            else if (newState == "selected")
            {
                _loc5 = owner.getStyle("textSelectedColor");
            } // end else if
            for (var _loc3 = 0; _loc3 < _loc4; ++_loc3)
            {
                cells[_loc3].setColor(_loc5);
                cells[_loc3].__enabled = owner.enabled;
            } // end of for
        }
        else
        {
            for (var _loc3 = 0; _loc3 < _loc4; ++_loc3)
            {
                cells[_loc3].setColor(_loc6[_loc3].getStyle("color"));
                cells[_loc3].__enabled = owner.enabled;
            } // end of for
        } // end else if
        super.setState(newState, transition);
    } // End of the function
    function startEditCell()
    {
        var _loc2 = grandOwner;
        _loc2.dontEdit = true;
        _loc2.releaseFocus();
        delete _loc2.dontEdit;
        if (_loc2.enabled && _loc2.editable && owner.item != undefined)
        {
            var _loc9 = owner.cells.length;
            for (var _loc3 = 0; _loc3 < _loc9; ++_loc3)
            {
                var _loc5 = _loc2.columns[_loc3];
                if (_loc5.editable)
                {
                    var _loc4 = owner._xmouse - owner.cells[_loc3]._x;
                    if (_loc4 >= 0 && _loc4 < _loc5.__width)
                    {
                        var _loc6 = owner.rowIndex + _loc2.__vPosition;
                        _loc2.setFocusedCell({itemIndex: _loc6, columnIndex: _loc3}, true);
                        if (wasPressed != true)
                        {
                            this.onPress();
                            _loc2.onMouseUp();
                        } // end if
                        delete this.wasPressed;
                        clearInterval(_loc2.dragScrolling);
                        delete _loc2.onMouseUp;
                        return;
                    } // end if
                } // end if
            } // end of for
        } // end if
    } // End of the function
    function bGOnPress(Void)
    {
        wasPressed = true;
        grandOwner.pressFocus();
        grandOwner.onRowPress(owner.rowIndex);
    } // End of the function
    function notifyStyleChangeInChildren(sheetName, styleProp, newValue)
    {
        var _loc6 = owner.columns;
        var _loc4 = cells.length;
        for (var _loc3 = 0; _loc3 < _loc4; ++_loc3)
        {
            var _loc2 = cells[_loc3];
            if (_loc2.stylecache != undefined)
            {
                delete _loc2.stylecache.tf;
            } // end if
            delete _loc2.enabledColor;
            _loc2.invalidateStyle(styleProp);
        } // end of for
    } // End of the function
} // End of Class
