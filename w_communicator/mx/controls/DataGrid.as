class mx.controls.DataGrid extends mx.controls.List
{
    var invInitHeaders, columns, __rowCount, invDrawCols, invalidate, getViewMetrics, __rowHeight, __width, setSize, invCheckCols, enabled, cellEditor, __dataProvider, getStateAt, rows, __vPosition, __hScrollPolicy, __maxHPosition, getRowCount, roundUp, setScrollProperties, oldVWidth, invLayoutContent, __height, border_mc, setMaxHPosition, getHPosition, getMaxHPosition, setHPosition, oldWidth, displayWidth, numberOfCols, invRowHeight, invSpaceColsEqually, invColChange, updateControl, __get__columnNames, totalWidth, listContent, lines_mc, __get__height, getStyle, headerCells, header_mc, dispatchEvent, __get__showHeaders, __get__headerHeight, __viewMetrics, sortArrow, sortIndex, layoutX, sortDirection, owner, column, _alpha, cell, asc, col, stretcher, _xmouse, _ymouse, oldX, stretchBar, colX, onRollOut, __get__focusedCell, __focusedCell, __hPosition, editorMask, editTween, getFocusManager, __tabHandlerCache, vScroller, hScroller, dontEdit, listOwner, activeGrid, getLength, releaseFocus, __get__columnCount, __set__columnNames, __set__focusedCell, __set__headerHeight, __set__showHeaders;
    function DataGrid()
    {
        super();
    } // End of the function
    function init()
    {
        super.init();
        invInitHeaders = true;
        columns = new Array();
    } // End of the function
    function layoutContent(x, y, tW, tH, dW, dH)
    {
        var _loc3 = __rowCount;
        if (__showHeaders)
        {
            y = y + __headerHeight;
            dH = dH - __headerHeight;
        } // end if
        super.layoutContent(x, y, tW, tH, dW, dH);
        if (tW != totColW)
        {
            this.drawHeaderBG();
        } // end if
        if (__rowCount > _loc3)
        {
            invDrawCols = true;
            this.invalidate();
        } // end if
    } // End of the function
    function setRowCount(rC)
    {
        if (isNaN(rC))
        {
            return;
        } // end if
        var _loc2 = this.getViewMetrics();
        this.setSize(__width, __rowHeight * rC + _loc2.top + _loc2.bottom + __headerHeight * __showHeaders);
    } // End of the function
    function setRowHeight(rH)
    {
        __rowHeight = rH;
        if (hasDrawn)
        {
            super.setRowHeight(rH);
        } // end if
    } // End of the function
    function setHScrollPolicy(policy)
    {
        super.setHScrollPolicy(policy);
        invCheckCols = true;
        this.invalidate();
    } // End of the function
    function setEnabled(v)
    {
        if (v == enabled)
        {
            return;
        } // end if
        super.setEnabled(v);
        if (__showHeaders)
        {
            this.enableHeader(v);
        } // end if
        if (cellEditor._visible == true)
        {
            this.disposeEditor();
        } // end if
        invDrawCols = true;
        this.invalidate();
    } // End of the function
    function modelChanged(eventObj)
    {
        if (eventObj.eventName == "updateField")
        {
            var _loc3 = eventObj.firstItem;
            var _loc5 = __dataProvider.getItemAt(_loc3);
            rows[_loc3 - __vPosition].drawRow(_loc5, this.getStateAt(_loc3));
            return;
        }
        else if (eventObj.eventName == "schemaLoaded")
        {
            this.removeAllColumns();
        } // end else if
        if (columns.length == 0)
        {
            this.generateCols();
        } // end if
        super.modelChanged(eventObj);
    } // End of the function
    function configureScrolling(Void)
    {
        var _loc3 = this.getViewMetrics();
        var _loc4 = __hScrollPolicy != "off" ? (__maxHPosition + __width - _loc3.left - _loc3.right) : (__width - _loc3.left - _loc3.right);
        var _loc2 = __dataProvider.length;
        if (_loc2 == undefined)
        {
            _loc2 = 0;
        } // end if
        if (__vPosition > Math.max(0, _loc2 - this.getRowCount() + roundUp))
        {
            this.setVPosition(Math.max(0, Math.min(_loc2 - this.getRowCount() + roundUp, __vPosition)));
        } // end if
        this.setScrollProperties(_loc4, 1, _loc2, __rowHeight, __headerHeight * __showHeaders);
        if (oldVWidth != _loc4)
        {
            invLayoutContent = true;
        } // end if
        oldVWidth = _loc4;
    } // End of the function
    function setVPosition(pos)
    {
        if (cellEditor != undefined)
        {
            this.disposeEditor();
        } // end if
        super.setVPosition(pos);
    } // End of the function
    function size(Void)
    {
        if (hasDrawn != true)
        {
            border_mc.setSize(__width, __height);
            return;
        } // end if
        if (cellEditor != undefined)
        {
            this.disposeEditor();
        } // end if
        if (__hScrollPolicy != "off")
        {
            var _loc5 = 0;
            var _loc6 = columns.length;
            for (var _loc3 = 0; _loc3 < _loc6; ++_loc3)
            {
                _loc5 = _loc5 + columns[_loc3].__width;
            } // end of for
            var _loc8 = this.getViewMetrics();
            var _loc9 = __width - _loc8.left - _loc8.right;
            this.setMaxHPosition(Math.max(_loc5 - _loc9, 0));
            var _loc7 = _loc9 - _loc5;
            if (_loc7 > 0)
            {
                columns[_loc6 - 1].__width = columns[_loc6 - 1].__width + _loc7;
            } // end if
            this.setHPosition(Math.min(this.getMaxHPosition(), this.getHPosition()));
        } // end if
        super.size();
        if (__hScrollPolicy == "off")
        {
            var _loc10 = new Array();
            _loc6 = columns.length;
            if (oldWidth == undefined)
            {
                oldWidth = displayWidth;
            } // end if
            var _loc4 = 0;
            for (var _loc3 = 0; _loc3 < _loc6; ++_loc3)
            {
                _loc4 = _loc4 + (columns[_loc3].__width = displayWidth * columns[_loc3].__width / oldWidth);
            } // end of for
            if (_loc4 != displayWidth)
            {
                columns[columns.length - 1].__width = columns[columns.length - 1].__width + (displayWidth - _loc4);
            } // end if
            totColW = numberOfCols = displayWidth;
        } // end if
        oldWidth = displayWidth;
        this.drawColumns();
        this.drawHeaderBG();
        this.invalidate();
    } // End of the function
    function draw()
    {
        if (invRowHeight)
        {
            super.draw();
            invInitHeaders = true;
            invDrawCols = true;
            delete this.cellEditor;
        } // end if
        if (invInitHeaders)
        {
            this.initHeaders();
            invLayoutContent = true;
        } // end if
        super.draw();
        if (invSpaceColsEqually)
        {
            delete this.invSpaceColsEqually;
            this.spaceColumnsEqually();
        } // end if
        if (invColChange)
        {
            delete this.invColChange;
            if (hasDrawn)
            {
                this.initHeaders();
                this.initRows();
                invDrawCols = true;
                this.updateControl();
                invCheckCols = true;
            } // end if
        } // end if
        if (invCheckCols)
        {
            if (totColW != displayWidth)
            {
                this.resizeColumn(columns.length - 1, columns[columns.length - 1].__width);
            } // end if
            delete this.invCheckCols;
        } // end if
        if (invDrawCols)
        {
            this.drawColumns();
        } // end if
        hasDrawn = true;
    } // End of the function
    function editField(index, colName, data)
    {
        __dataProvider.editField(index, colName, data);
    } // End of the function
    function get columnNames()
    {
        return (this.getColumnNames());
    } // End of the function
    function set columnNames(w)
    {
        this.setColumnNames(w);
        //return (this.columnNames());
        null;
    } // End of the function
    function setColumnNames(tmpArray)
    {
        for (var _loc2 = 0; _loc2 < tmpArray.length; ++_loc2)
        {
            this.addColumn(tmpArray[_loc2]);
        } // end of for
    } // End of the function
    function getColumnNames(Void)
    {
        var _loc3 = new Array();
        for (var _loc2 = 0; _loc2 < columns.length; ++_loc2)
        {
            _loc3[_loc2] = columns[_loc2].columnName;
        } // end of for
        return (_loc3);
    } // End of the function
    function addColumnAt(index, newCol)
    {
        if (index < columns.length)
        {
            columns.splice(index, 0, "tmp");
        } // end if
        var _loc4 = newCol;
        if (!(_loc4 instanceof mx.controls.gridclasses.DataGridColumn))
        {
            _loc4 = new mx.controls.gridclasses.DataGridColumn(_loc4);
        } // end if
        columns[index] = _loc4;
        _loc4.colNum = index;
        for (var _loc2 = index + 1; _loc2 < columns.length; ++_loc2)
        {
            ++columns[_loc2].colNum;
        } // end of for
        _loc4.parentGrid = this;
        totColW = totColW + _loc4.width;
        invColChange = true;
        this.invalidate();
        return (newCol);
    } // End of the function
    function addColumn(newCol)
    {
        return (this.addColumnAt(columns.length, newCol));
    } // End of the function
    function removeColumnAt(index)
    {
        var _loc4 = columns[index];
        columns.splice(index, 1);
        totColW = totColW - _loc4.width;
        for (var _loc2 = index; _loc2 < columns.length; ++_loc2)
        {
            --columns[_loc2].colNum;
        } // end of for
        invColChange = true;
        this.invalidate();
        return (_loc4);
    } // End of the function
    function removeAllColumns(Void)
    {
        totColW = 0;
        columns = new Array();
        invColChange = true;
        this.invalidate();
    } // End of the function
    function getColumnAt(index)
    {
        return (columns[index]);
    } // End of the function
    function getColumnIndex(name)
    {
        for (var _loc2 = 0; _loc2 < columns.length; ++_loc2)
        {
            if (columns[_loc2].columnName == name)
            {
                return (_loc2);
            } // end if
        } // end of for
    } // End of the function
    function get columnCount()
    {
        return (columns.length);
    } // End of the function
    function spaceColumnsEqually(Void)
    {
        if (displayWidth == undefined)
        {
            var _loc4 = this.getViewMetrics();
            displayWidth = __width - _loc4.left - _loc4.right;
        } // end if
        var _loc3 = Math.ceil(totalWidth / columns.length);
        for (var _loc2 = 0; _loc2 < columns.length; ++_loc2)
        {
            columns[_loc2].__width = _loc3;
        } // end of for
        totColW = totalWidth;
        invDrawCols = true;
        this.invalidate();
    } // End of the function
    function generateCols(Void)
    {
        if (columns.length == 0)
        {
            var _loc3 = __dataProvider.getColumnNames();
            if (_loc3 == undefined)
            {
                var _loc4 = __dataProvider.getItemAt(0);
                for (var _loc2 in _loc4)
                {
                    if (_loc2 != "__ID__")
                    {
                        this.addColumn(_loc2);
                    } // end if
                } // end of for...in
            }
            else
            {
                for (var _loc2 = 0; _loc2 < _loc3.length; ++_loc2)
                {
                    this.addColumn(_loc3[_loc2]);
                } // end of for
            } // end else if
            invSpaceColsEqually = true;
            invColChange = true;
            invCheckCols = true;
            this.invalidate();
        } // end if
    } // End of the function
    function resizeColumn(col, w)
    {
        if (__hScrollPolicy == "on" || __hScrollPolicy == "auto")
        {
            columns[col].__width = w;
            var _loc11 = 0;
            var _loc5 = columns.length;
            for (var _loc2 = 0; _loc2 < _loc5; ++_loc2)
            {
                _loc11 = _loc11 + columns[_loc2].__width;
            } // end of for
            this.setMaxHPosition(Math.max(_loc11 - displayWidth, 0));
            var _loc12 = displayWidth - _loc11;
            if (_loc12 > 0)
            {
                columns[_loc5 - 1].__width = columns[_loc5 - 1].__width + _loc12;
            } // end if
            this.setHPosition(Math.min(this.getMaxHPosition(), this.getHPosition()));
            invDrawCols = true;
            this.invalidate();
            return;
        } // end if
        var _loc10 = 0;
        for (var _loc2 = 0; _loc2 < col; ++_loc2)
        {
            _loc10 = _loc10 + columns[_loc2].__width;
        } // end of for
        var _loc8 = displayWidth + 2 - _loc10 - columns[col].__width;
        var _loc6 = displayWidth + 2 - _loc10 - w;
        columns[col].__width = w;
        _loc5 = columns.length;
        for (var _loc2 = col + 1; _loc2 < _loc5; ++_loc2)
        {
            if (!columns[_loc2].resizable)
            {
                _loc6 = _loc6 - columns[_loc2].__width;
                _loc8 = _loc8 - columns[_loc2].__width;
            } // end if
        } // end of for
        var _loc9 = 0;
        for (var _loc2 = col + 1; _loc2 < _loc5; ++_loc2)
        {
            if (columns[_loc2].resizable)
            {
                columns[_loc2].__width = columns[_loc2].width * _loc6 / _loc8;
                _loc9 = _loc9 + columns[_loc2].__width;
            } // end if
        } // end of for
        var _loc3 = 0;
        var _loc7 = false;
        for (var _loc2 = _loc5 - 1; _loc2 >= 0; --_loc2)
        {
            if (columns[_loc2].resizable)
            {
                if (!_loc7)
                {
                    columns[_loc2].__width = columns[_loc2].__width + (_loc6 - _loc9);
                    _loc7 = true;
                } // end if
                if (_loc3 > 0)
                {
                    columns[_loc2].__width = columns[_loc2].__width - _loc3;
                    _loc3 = 0;
                } // end if
                if (columns[_loc2].__width < minColWidth)
                {
                    _loc3 = _loc3 + (minColWidth - columns[_loc2].__width);
                    columns[_loc2].__width = minColWidth;
                } // end if
            } // end if
        } // end of for
        invDrawCols = true;
        this.invalidate();
    } // End of the function
    function drawColumns(Void)
    {
        delete this.invDrawCols;
        var _loc4 = lines_mc = listContent.createEmptyMovieClip("lines_mc", LINEDEPTH);
        var _loc9 = 7.500000E-001;
        var _loc5 = 1;
        var _loc15 = this.__get__height() - 1;
        var _loc12 = this.getStyle("vGridLineColor");
        var _loc14 = columns.length;
        this.placeSortArrow();
        for (var _loc7 = 0; _loc7 < _loc14; ++_loc7)
        {
            var _loc6 = columns[_loc7];
            var _loc13 = enabled ? ("backgroundColor") : ("backgroundDisabledColor");
            var _loc10 = _loc6.getStyle(_loc13);
            _loc9 = _loc9 + _loc6.__width;
            _loc4.moveTo(_loc5, 1);
            _loc4.lineStyle(0, _loc12, 0);
            var _loc11 = Math.floor(_loc9);
            _loc4.lineTo(_loc11, 1);
            if (_loc7 < columns.length - 1 && this.getStyle("vGridLines"))
            {
                _loc4.lineStyle(0, _loc12, 100);
            } // end if
            _loc4.lineTo(_loc11, this.__get__height());
            _loc4.lineStyle(0, _loc12, 0);
            _loc4.lineTo(_loc5, this.__get__height());
            _loc4.lineTo(_loc5, 1);
            if (__showHeaders)
            {
                var _loc3 = headerCells[_loc7];
                _loc3._x = _loc5 + 2;
                _loc3.hO._x = _loc5;
                _loc3.setSize(_loc6.__width - 5, Math.min(__headerHeight, _loc3.getPreferredHeight()));
                _loc3.hO._width = _loc6.__width - 2;
                _loc3.hO._height = __headerHeight;
                _loc3._y = (__headerHeight - _loc3._height) / 2;
                header_mc["sep" + _loc7]._x = _loc9 - 2;
                listContent.disableHeader._width = totalWidth;
            } // end if
            for (var _loc2 = 0; _loc2 < __rowCount; ++_loc2)
            {
                if (_loc7 == 0)
                {
                    rows[_loc2].colBG.clear();
                } // end if
                var _loc8 = _loc6.__width;
                rows[_loc2].drawCell(_loc7, _loc5, _loc8, _loc10);
            } // end of for
            _loc5 = _loc9;
        } // end of for
        if (this.getStyle("hGridLines"))
        {
            lines_mc.lineStyle(0, this.getStyle("hGridLineColor"));
            for (var _loc7 = 1; _loc7 < __rowCount; ++_loc7)
            {
                lines_mc.moveTo(4, rows[_loc7]._y);
                lines_mc.lineTo(totalWidth, rows[_loc7]._y);
            } // end of for
        } // end if
    } // End of the function
    function initRows(Void)
    {
        for (var _loc2 = 0; _loc2 < __rowCount; ++_loc2)
        {
            rows[_loc2].createCells();
        } // end of for
    } // End of the function
    function onRowPress(rowIndex)
    {
        super.onRowPress(rowIndex);
        if (!enabled)
        {
            return;
        } // end if
        var _loc11 = columns.length;
        var _loc6 = rows[rowIndex];
        for (var _loc3 = 0; _loc3 < _loc11; ++_loc3)
        {
            var _loc5 = columns[_loc3];
            var _loc4 = _loc6._xmouse - _loc6.cells[_loc3]._x;
            if (_loc4 >= 0 && _loc4 < _loc5.__width)
            {
                this.dispatchEvent({type: "cellPress", columnIndex: _loc3, view: this, itemIndex: rowIndex + __vPosition});
                return;
            } // end if
        } // end of for
    } // End of the function
    function get showHeaders()
    {
        return (this.getShowHeaders());
    } // End of the function
    function set showHeaders(w)
    {
        this.setShowHeaders(w);
        //return (this.showHeaders());
        null;
    } // End of the function
    function setShowHeaders(b)
    {
        __showHeaders = b;
        invInitHeaders = true;
        invDrawCols = true;
        this.invalidate();
    } // End of the function
    function getShowHeaders()
    {
        return (__showHeaders);
    } // End of the function
    function get headerHeight()
    {
        return (this.getHeaderHeight());
    } // End of the function
    function set headerHeight(w)
    {
        this.setHeaderHeight(w);
        //return (this.headerHeight());
        null;
    } // End of the function
    function setHeaderHeight(h)
    {
        __headerHeight = h;
        invInitHeaders = true;
        invDrawCols = true;
        this.invalidate();
    } // End of the function
    function getHeaderHeight(Void)
    {
        return (__headerHeight);
    } // End of the function
    function initHeaders(Void)
    {
        delete this.invInitHeaders;
        if (__showHeaders)
        {
            header_mc = listContent.createClassObject(mx.core.UIObject, "header_mc", HEADERDEPTH, {styleName: this});
            headerCells = new Array();
            for (var _loc2 = 0; _loc2 < columns.length; ++_loc2)
            {
                var _loc6 = columns[_loc2];
                var _loc4;
                var _loc7 = _loc6.__headerRenderer;
                if (_loc7 == undefined)
                {
                    _loc4 = headerCells[_loc2] = header_mc.createLabel("fHeaderCell" + _loc2, HEADERCELLDEPTH + _loc2);
                    _loc4.selectable = false;
                    _loc4.setStyle("styleName", _loc6);
                }
                else if (typeof(_loc7) == "string")
                {
                    _loc4 = headerCells[_loc2] = header_mc.createObject(_loc7, "fHeaderCell" + _loc2, HEADERCELLDEPTH + _loc2, {styleName: _loc6});
                }
                else
                {
                    _loc4 = headerCells[_loc2] = header_mc.createClassObject(_loc7, "fHeaderCell" + _loc2, HEADERCELLDEPTH + _loc2, {styleName: _loc6});
                } // end else if
                _loc4.setValue(_loc6.__get__headerText());
                _loc6.headerCell = _loc4;
                var _loc3 = header_mc.attachMovie("DataHeaderOverlay", "hO" + _loc2, HEADEROVERLAYDEPTH + _loc2);
                _loc4.hO = _loc3;
                _loc3.cell = _loc4;
                _loc4.column = _loc3.column = _loc6;
                _loc4.asc = _loc3.asc = false;
                _loc4.owner = _loc3.owner = this;
                _loc3._alpha = 0;
                if (_loc3.column.sortable && _loc3.onPress == undefined)
                {
                    _loc3.useHandCursor = false;
                    _loc3.onRollOver = headerRollOver;
                    _loc3.onRollOut = headerRollOut;
                    _loc3.onPress = headerPress;
                    _loc3.onRelease = headerRelease;
                    _loc3.onReleaseOutside = headerUp;
                    _loc3.headerUp = headerUp;
                } // end if
                if (_loc2 < columns.length - 1)
                {
                    var _loc5 = header_mc.attachMovie("DataHeaderSeperator", "sep" + _loc2, SEPARATORDEPTH + _loc2);
                    _loc5._height = __headerHeight;
                    if (_loc6.resizable && resizableColumns)
                    {
                        _loc5.useHandCursor = false;
                        _loc5.col = _loc2;
                        _loc5.owner = this;
                        _loc5.onRollOver = showStretcher;
                        _loc5.onPress = startSizing;
                        _loc5.onRelease = _loc5.onReleaseOutside = stopSizing;
                        _loc5.onRollOut = hideStretcher;
                    } // end if
                } // end if
            } // end of for
            this.drawHeaderBG();
        }
        else
        {
            header_mc.removeMovieClip();
        } // end else if
    } // End of the function
    function invalidateHeaderStyle(Void)
    {
        var _loc4 = columns.length;
        for (var _loc3 = 0; _loc3 < _loc4; ++_loc3)
        {
            var _loc2 = headerCells[_loc3];
            if (_loc2.stylecache != undefined)
            {
                delete _loc2.stylecache.tf;
            } // end if
            delete _loc2.enabledColor;
            _loc2.invalidateStyle();
            _loc2.draw();
        } // end of for
    } // End of the function
    function drawHeaderBG(Void)
    {
        var _loc2 = header_mc;
        _loc2.clear();
        var _loc5 = this.getStyle("headerColor");
        var _loc3 = __viewMetrics;
        var _loc4 = Math.max(totalWidth, displayWidth + 3);
        _loc2.moveTo(_loc3.left, _loc3.top);
        var _loc7 = {matrixType: "box", x: 0, y: 0, w: _loc4, h: __headerHeight + 1, r: 1.570796E+000};
        var _loc8 = [_loc5, _loc5, 16777215];
        var _loc9 = [0, 60, 255];
        var _loc6 = [100, 100, 100];
        _loc2.beginGradientFill("linear", _loc8, _loc6, _loc9, _loc7);
        _loc2.lineStyle(0, 0, 0);
        _loc2.lineTo(_loc4, _loc3.top);
        _loc2.lineTo(_loc4, __headerHeight + 1);
        _loc2.lineStyle(0, 0, 100);
        _loc2.lineTo(_loc3.left, __headerHeight + 1);
        _loc2.lineStyle(0, 0, 0);
        _loc2.endFill();
    } // End of the function
    function enableHeader(v)
    {
        if (v)
        {
            listContent.disableHeader.removeMovieClip();
        }
        else
        {
            var _loc2 = listContent.attachMovie("DataHeaderOverlay", "disableHeader", DISABLEDHEADERDEPTH);
            _loc2._width = totalWidth;
            _loc2._height = __headerHeight;
            var _loc3 = new Color(_loc2);
            _loc3.setRGB(this.getStyle("backgroundDisabledColor"));
            _loc2._alpha = 60;
        } // end else if
    } // End of the function
    function placeSortArrow(Void)
    {
        sortArrow.removeMovieClip();
        if (sortIndex == undefined)
        {
            return;
        } // end if
        if (columns[sortIndex].__width - headerCells[sortIndex].getPreferredWidth() <= 20)
        {
            return;
        } // end if
        sortArrow = header_mc.createObject("DataSortArrow", "sortArrow", SORTARROWDEPTH);
        var _loc3 = layoutX;
        for (var _loc2 = 0; _loc2 <= sortIndex; ++_loc2)
        {
            _loc3 = _loc3 + columns[_loc2].__width;
        } // end of for
        var _loc4 = sortDirection == "ASC";
        sortArrow._yscale = _loc4 ? (-100) : (100);
        sortArrow._x = _loc3 - sortArrow._width - 8;
        sortArrow._y = (__headerHeight - sortArrow._height) / 2 + _loc4 * sortArrow._height;
    } // End of the function
    function headerRollOver(Void)
    {
        var _loc2 = owner;
        if (!_loc2.enabled || _loc2.cellEditor != undefined || !_loc2.sortableColumns || !column.sortable)
        {
            return;
        } // end if
        var _loc3 = new Color(this);
        _loc3.setRGB(_loc2.getStyle("rollOverColor"));
        _alpha = 50;
    } // End of the function
    function headerRollOut(Void)
    {
        _alpha = 0;
    } // End of the function
    function headerPress(Void)
    {
        var _loc2 = owner;
        if (!column.sortable || !_loc2.sortableColumns || !_loc2.enabled)
        {
            return;
        } // end if
        cell._x = cell._x + 1;
        cell._y = cell._y + 1;
        var _loc3 = new Color(this);
        _loc3.setRGB(_loc2.getStyle("selectionColor"));
        _alpha = 100;
    } // End of the function
    function headerUp(Void)
    {
        if (!column.sortable || !owner.sortableColumns || !owner.enabled)
        {
            return;
        } // end if
        _alpha = 0;
        cell._x = cell._x - 1;
        cell._y = cell._y - 1;
    } // End of the function
    function headerRelease(Void)
    {
        var _loc2 = owner;
        var _loc3 = column;
        if (!_loc3.sortable || !_loc2.sortableColumns || !_loc2.enabled)
        {
            return;
        } // end if
        this.headerUp();
        asc = !asc;
        var _loc4 = asc ? ("ASC") : ("DESC");
        _loc2.sortIndex = _loc2.getColumnIndex(_loc3.columnName);
        _loc2.sortDirection = _loc4;
        _loc2.placeSortArrow();
        if (_loc3.sortOnHeaderRelease)
        {
            _loc2.sortItemsBy(_loc3.columnName, _loc4);
        } // end if
        _loc2.dispatchEvent({type: "headerRelease", view: _loc2, columnIndex: _loc2.getColumnIndex(_loc3.columnName)});
        _loc2.dontEdit = true;
    } // End of the function
    function isStretchable(col)
    {
        var _loc2 = true;
        if (!resizableColumns)
        {
            _loc2 = false;
        }
        else if (!columns[col].resizable)
        {
            _loc2 = false;
        }
        else if (col == columns.length - 2 && !columns[col + 1].resizable)
        {
            _loc2 = false;
        } // end else if
        return (_loc2);
    } // End of the function
    function showStretcher(Void)
    {
        var _loc2 = owner;
        if (!_loc2.isStretchable(col) || !_loc2.enabled || _loc2.cellEditor != undefined)
        {
            return;
        } // end if
        Mouse.hide();
        if (_loc2.stretcher == undefined)
        {
            _loc2.attachMovie("cursorStretch", "stretcher", _loc2.STRETCHERDEPTH);
        } // end if
        _loc2.stretcher._x = _loc2._xmouse;
        _loc2.stretcher._y = _loc2._ymouse;
        _loc2.stretcher._visible = true;
        _loc2.onMouseMove = function ()
        {
            stretcher._x = _xmouse;
            stretcher._y = _ymouse;
            updateAfterEvent();
        };
    } // End of the function
    function startSizing(Void)
    {
        var _loc2 = owner;
        if (!_loc2.isStretchable(col) || !_loc2.enabled)
        {
            return;
        } // end if
        _loc2.pressFocus();
        _loc2.attachMovie("DataStretchBar", "stretchBar", 999);
        _loc2.stretchBar._height = _loc2.height;
        _loc2.stretchBar._x = _loc2._xmouse;
        oldX = _loc2.stretchBar._x;
        _loc2.colX = oldX - _loc2.columns[col].width;
        _loc2.onMouseMove = function ()
        {
            stretcher._x = _xmouse;
            stretcher._y = _ymouse;
            stretchBar._x = Math.max(_xmouse, colX + minColWidth);
            if (__hScrollPolicy == "off")
            {
                stretchBar._x = Math.min(stretchBar._x, displayWidth - minColWidth);
            } // end if
            updateAfterEvent();
        };
    } // End of the function
    function stopSizing(Void)
    {
        var _loc2 = owner;
        var _loc3 = col;
        if (!_loc2.isStretchable(_loc3) || !_loc2.enabled)
        {
            return;
        } // end if
        _loc2.stretchBar._visible = false;
        this.onRollOut();
        var _loc4 = _loc2.stretchBar._x - oldX;
        _loc2.resizeColumn(_loc3, _loc2.columns[_loc3].width + _loc4);
        _loc2.dispatchEvent({type: "columnStretch", columnIndex: _loc3});
    } // End of the function
    function hideStretcher(Void)
    {
        owner.stretcher._visible = false;
        delete owner.onMouseMove;
        Mouse.show();
    } // End of the function
    function set focusedCell(obj)
    {
        this.setFocusedCell(obj);
        //return (this.focusedCell());
        null;
    } // End of the function
    function get focusedCell()
    {
        return (__focusedCell);
    } // End of the function
    function setFocusedCell(coord, broadCast)
    {
        if (!enabled || !editable)
        {
            return;
        } // end if
        if (coord == undefined && cellEditor != undefined)
        {
            this.disposeEditor();
            return;
        } // end if
        var _loc2 = coord.itemIndex;
        var _loc5 = coord.columnIndex;
        if (_loc2 == undefined)
        {
            _loc2 = 0;
        } // end if
        if (_loc5 == undefined)
        {
            _loc5 = 0;
        } // end if
        var _loc9 = columns[_loc5].columnName;
        if (__vPosition > _loc2)
        {
            this.setVPosition(_loc2);
        }
        else
        {
            var _loc11 = _loc2 - __vPosition - __rowCount + roundUp + 1;
            if (_loc11 > 0)
            {
                this.setVPosition(__vPosition + _loc11);
            } // end if
        } // end else if
        var _loc10 = columns[_loc5];
        var _loc8 = rows[_loc2 - __vPosition];
        var _loc3 = _loc8.cells[_loc5];
        if (_loc3._x > __hPosition + displayWidth || _loc3._x < __hPosition)
        {
            this.setHPosition(_loc3._x);
        } // end if
        var _loc4 = __dataProvider.getEditingData(_loc2, _loc9);
        if (_loc4 == undefined)
        {
            _loc4 = __dataProvider.getItemAt(_loc2)[_loc9];
        } // end if
        if (_loc4 == undefined)
        {
            _loc4 = " ";
        } // end if
        if (_loc3.isCellEditor != true)
        {
            if (cellEditor == undefined)
            {
                cellEditor = listContent.createClassObject(mx.controls.TextInput, "editor_mc", EDITORDEPTH, {styleName: _loc10, listOwner: this});
            } // end if
            cellEditor.backgroundColor = 16777215;
            cellEditor._visible = true;
            cellEditor.setSize(_loc10.__width, __rowHeight + 2);
            cellEditor._x = _loc3._x - 1;
            cellEditor.text = _loc4;
            editorMask = listContent.attachMovie("BoundingBox", "editorMask", 60001, {_alpha: 0});
            cellEditor.setMask(editorMask);
            editorMask._width = cellEditor.width;
            editorMask._height = cellEditor.height;
            editorMask._y = cellEditor._y = _loc8._y - 1;
            editorMask._x = cellEditor._x - editorMask._width;
            editTween = new mx.effects.Tween(this, cellEditor._x - editorMask._width, cellEditor._x, 150);
        }
        else
        {
            cellEditor = _loc3;
            cellEditor.setValue(_loc4, __dataProvider.getItemAt(_loc2));
        } // end else if
        var _loc6 = this.getFocusManager();
        _loc6.setFocus(cellEditor);
        _loc6.defaultPushButtonEnabled = false;
        if (_loc3.isCellEditor != true)
        {
            cellEditor.hPosition = 0;
            cellEditor.redraw();
            Selection.setSelection(0, cellEditor.length);
        } // end if
        __focusedCell = coord;
        if (__tabHandlerCache == undefined)
        {
            __tabHandlerCache = _loc6.tabHandler;
            _loc6.tabHandler = tabHandler;
        } // end if
        _loc6.activeGrid = this;
        cellEditor.addEventListener("keyDown", editorKeyDown);
        if (broadCast)
        {
            this.dispatchEvent({type: "cellFocusIn", itemIndex: _loc2, columnIndex: _loc5});
        } // end if
    } // End of the function
    function onMouseDown(Void)
    {
        if (cellEditor._visible && !cellEditor.hitTest(_root._xmouse, _root._ymouse))
        {
            this.editCell();
        } // end if
        if (vScroller.hitTest(_root._xmouse, _root._ymouse) || hScroller.hitTest(_root._xmouse, _root._ymouse) || header_mc.hitTest(_root._xmouse, _root._ymouse))
        {
            dontEdit = true;
        } // end if
    } // End of the function
    function editorKeyDown(Void)
    {
        if (Key.isDown(27))
        {
            listOwner.disposeEditor();
        }
        else if (Key.isDown(13) && Key.getCode() != 229)
        {
            listOwner.editCell();
            listOwner.findNextEnterCell();
        } // end else if
    } // End of the function
    function tabHandler(Void)
    {
        var _loc3 = -1;
        var _loc4 = -1;
        var _loc2 = activeGrid;
        if (_loc2.__focusedCell != undefined)
        {
            _loc3 = _loc2.__focusedCell.itemIndex;
            _loc4 = _loc2.__focusedCell.columnIndex;
        } // end if
        _loc2.editCell();
        _loc2.findNextCell(_loc3, _loc4);
    } // End of the function
    function findNextEnterCell(Void)
    {
        var _loc3 = Key.isDown(16) ? (-1) : (1);
        var _loc2 = __focusedCell.itemIndex + _loc3;
        if (_loc2 < this.getLength() && _loc2 >= 0)
        {
            __focusedCell.itemIndex = _loc2;
        } // end if
        this.setFocusedCell(__focusedCell, true);
    } // End of the function
    function findNextCell(index, colIndex)
    {
        if (index == undefined)
        {
            colIndex = -1;
            index = -1;
        } // end if
        var _loc5 = false;
        var _loc4 = Key.isDown(16) ? (-1) : (1);
        while (!_loc5)
        {
            colIndex = colIndex + _loc4;
            if (colIndex >= columns.length || colIndex < 0)
            {
                colIndex = colIndex < 0 ? (columns.length) : (0);
                index = index + _loc4;
                if (index >= this.getLength() || index < 0)
                {
                    if (this.getFocusManager().activeGrid != undefined)
                    {
                        this.disposeEditor();
                    } // end if
                    dontEdit = true;
                    Selection.setFocus(this);
                    delete this.dontEdit;
                    this.getFocusManager().tabHandler();
                    return;
                } // end if
            } // end if
            if (columns[colIndex].editable)
            {
                _loc5 = true;
                if (__tabHandlerCache != undefined)
                {
                    this.disposeEditor();
                } // end if
                this.setFocusedCell({itemIndex: index, columnIndex: colIndex}, true);
            } // end if
        } // end while
    } // End of the function
    function onSetFocus(Void)
    {
        super.onSetFocus();
        if (editable && dontEdit != true)
        {
            if (__focusedCell == undefined)
            {
                __focusedCell = {itemIndex: 0, columnIndex: 0};
            } // end if
            if (columns[__focusedCell.columnIndex].editable == true)
            {
                this.setFocusedCell(__focusedCell, true);
            }
            else
            {
                this.findNextCell(__focusedCell.itemIndex, __focusedCell.columnIndex);
            } // end if
        } // end else if
        delete this.dontEdit;
    } // End of the function
    function onTweenUpdate(val)
    {
        editorMask._x = val;
    } // End of the function
    function onTweenEnd(val)
    {
        editorMask._x = val;
        cellEditor.setMask(undefined);
        editorMask.removeMovieClip();
    } // End of the function
    function disposeEditor(Void)
    {
        cellEditor.removeEventListener("keyDown", editorKeyDown);
        this.dispatchEvent({type: "cellFocusOut", itemIndex: __focusedCell.itemIndex, columnIndex: __focusedCell.columnIndex});
        if (cellEditor.isCellEditor != true)
        {
            cellEditor._visible = false;
        } // end if
        var _loc3 = this.getFocusManager();
        if (__tabHandlerCache != undefined)
        {
            _loc3.tabHandler = __tabHandlerCache;
            delete this.__tabHandlerCache;
        } // end if
        _loc3.defaultPushButtonEnabled = true;
        if (border_mc.hitTest(_root._xmouse, _root._ymouse) && !vScroller.hitTest(_root._xmouse, _root._ymouse) && !hScroller.hitTest(_root._xmouse, _root._ymouse))
        {
            dontEdit = true;
            this.releaseFocus();
            delete this.dontEdit;
        } // end if
        delete this.cellEditor;
        delete _loc3.activeGrid;
    } // End of the function
    function editCell()
    {
        var _loc3 = __focusedCell.itemIndex;
        var _loc4 = columns[__focusedCell.columnIndex].columnName;
        var _loc2 = __dataProvider.getEditingData(_loc3, _loc4);
        if (_loc2 == undefined)
        {
            _loc2 = __dataProvider.getItemAt(_loc3)[_loc4];
        } // end if
        var _loc5 = cellEditor.isCellEditor ? (cellEditor.getValue()) : (cellEditor.text);
        if (_loc2 != _loc5)
        {
            this.editField(_loc3, _loc4, _loc5);
            this.dispatchEvent({type: "cellEdit", itemIndex: _loc3, columnIndex: __focusedCell.columnIndex, oldValue: _loc2});
        } // end if
        this.disposeEditor();
    } // End of the function
    function invalidateStyle(propName)
    {
        if (propName == "headerColor" || propName == "styleName")
        {
            this.drawHeaderBG();
        } // end if
        if (propName == "hGridLines" || propName == "hGridLineColor" || propName == "vGridLines" || propName == "vGridLineColor" || propName == "styleName" || propName == "backgroundColor")
        {
            invDrawCols = true;
            this.invalidate();
        } // end if
        if (mx.styles.StyleManager.TextStyleMap[propName] != undefined)
        {
            super.changeTextStyleInChildren(propName);
        } // end if
        if (propName == "styleName" || propName == "headerStyle")
        {
            this.invalidateHeaderStyle();
        } // end if
        super.invalidateStyle(propName);
    } // End of the function
    function notifyStyleChangeInChildren(sheetName, styleProp, newValue)
    {
        if (styleProp == "headerStyle")
        {
            this.invalidateHeaderStyle();
        } // end if
        if (sheetName != undefined)
        {
            for (var _loc4 = 0; _loc4 < columns.length; ++_loc4)
            {
                if (sheetName == columns[_loc4].styleName)
                {
                    this.invalidateStyle(styleProp);
                    for (var _loc3 = 0; _loc3 < rows.length; ++_loc3)
                    {
                        rows[_loc3].notifyStyleChangeInChildren(sheetName, styleProp, newValue);
                    } // end of for
                } // end if
            } // end of for
        } // end if
        super.notifyStyleChangeInChildren(sheetName, styleProp, newValue);
    } // End of the function
    static var symbolOwner = mx.controls.DataGrid;
    static var symbolName = "DataGrid";
    static var version = "2.0.2.127";
    var className = "DataGrid";
    var selectable = true;
    var resizableColumns = true;
    var __showHeaders = true;
    var sortableColumns = true;
    var autoHScrollAble = true;
    var editable = false;
    var minColWidth = 20;
    var totColW = 0;
    var __rowRenderer = "DataGridRow";
    var __headerHeight = 20;
    var hasDrawn = false;
    var minScrollInterval = 60;
    var HEADERDEPTH = 5001;
    var LINEDEPTH = 5000;
    var SORTARROWDEPTH = 5500;
    var EDITORDEPTH = 5002;
    var DISABLEDHEADERDEPTH = 5003;
    var HEADERCELLDEPTH = 4500;
    var HEADEROVERLAYDEPTH = 4000;
    var SEPARATORDEPTH = 5000;
    var STRETCHERDEPTH = 1000;
} // End of Class
