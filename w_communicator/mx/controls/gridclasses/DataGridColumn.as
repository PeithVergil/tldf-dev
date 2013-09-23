class mx.controls.gridclasses.DataGridColumn extends mx.styles.CSSStyleDeclaration
{
    var columnName, __set__headerText, parentGrid, colNum, __get__width, __header, headerCell, __get__headerText, __cellRenderer, __get__cellRenderer, __headerRenderer, __get__headerRenderer, __labelFunction, __get__labelFunction, styleName, __set__cellRenderer, __set__headerRenderer, __set__labelFunction, __set__width;
    function DataGridColumn(colName)
    {
        super();
        columnName = colName;
        this.__set__headerText(colName);
    } // End of the function
    function get width()
    {
        return (__width);
    } // End of the function
    function set width(w)
    {
        delete parentGrid.invSpaceColsEqually;
        if (parentGrid != undefined && parentGrid.hasDrawn)
        {
            var _loc2 = resizable;
            resizable = false;
            parentGrid.resizeColumn(colNum, w);
            resizable = _loc2;
        }
        else
        {
            __width = w;
        } // end else if
        //return (this.width());
        null;
    } // End of the function
    function set headerText(h)
    {
        __header = h;
        headerCell.setValue(h);
        //return (this.headerText());
        null;
    } // End of the function
    function get headerText()
    {
        return (__header == undefined ? (columnName) : (__header));
    } // End of the function
    function set cellRenderer(cR)
    {
        __cellRenderer = cR;
        parentGrid.invColChange = true;
        parentGrid.invalidate();
        //return (this.cellRenderer());
        null;
    } // End of the function
    function get cellRenderer()
    {
        return (__cellRenderer);
    } // End of the function
    function set headerRenderer(hS)
    {
        __headerRenderer = hS;
        parentGrid.invInitHeaders = true;
        parentGrid.invalidate();
        //return (this.headerRenderer());
        null;
    } // End of the function
    function get headerRenderer()
    {
        return (__headerRenderer);
    } // End of the function
    function set labelFunction(f)
    {
        __labelFunction = f;
        parentGrid.invUpdateControl = true;
        parentGrid.invalidate();
        //return (this.labelFunction());
        null;
    } // End of the function
    function get labelFunction()
    {
        return (__labelFunction);
    } // End of the function
    function getStyle(prop)
    {
        var _loc3 = this[prop];
        if (_loc3 == undefined)
        {
            if (styleName != undefined)
            {
                if (styleName instanceof mx.styles.CSSStyleDeclaration)
                {
                    _loc3 = styleName.getStyle(prop);
                }
                else
                {
                    _loc3 = _global.styles[styleName].getStyle(prop);
                } // end if
            } // end else if
            if ((_loc3 == undefined || _loc3 == _global.style[prop] || _loc3 == _global.styles[parentGrid.className][prop]) && prop != "backgroundColor")
            {
                _loc3 = parentGrid.getStyle(prop);
            } // end if
        } // end if
        return (_loc3);
    } // End of the function
    function __getTextFormat(tf, bAll, fieldInst)
    {
        var _loc4;
        if (parentGrid.header_mc[fieldInst._name] != undefined)
        {
            _loc4 = this.getStyle("headerStyle").__getTextFormat(tf, bAll, fieldInst);
            if (_loc4 != false)
            {
                _loc4 = parentGrid.getStyle("headerStyle").__getTextFormat(tf, bAll, fieldInst);
            } // end if
            if (_loc4 == false)
            {
                return (_loc4);
            } // end if
        } // end if
        if (styleName != undefined)
        {
            var _loc8 = typeof(styleName) == "string" ? (_global.styles[styleName]) : (styleName);
            _loc4 = _loc8.__getTextFormat(tf, bAll);
            if (!_loc4)
            {
                return (_loc4);
            } // end if
        } // end if
        _loc4 = super.__getTextFormat(tf, bAll, fieldInst);
        if (_loc4)
        {
            return (parentGrid.__getTextFormat(tf, bAll));
        }
        else
        {
            return (_loc4);
        } // end else if
    } // End of the function
    var editable = true;
    var sortable = true;
    var resizable = true;
    var sortOnHeaderRelease = true;
    var __width = 50;
} // End of Class
