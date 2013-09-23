class mx.containers.Accordion extends mx.core.View
{
    var tabEnabled, boundingBox_mc, __get__numChildren, invalidate, destroyObject, childNames, childSymbols, childLabels, childIcons, getChildAt, border_mc, getStyle, __get__selectedIndex, getFocusManager, tween, __set__selectedIndex, __get__selectedChild, falseUpSkin, falseDownSkin, falseOverSkin, falseDisabledSkin, trueUpSkin, trueDownSkin, trueOverSkin, trueDisabledSkin, createClassObject, _parent, createObject, __get__width, __get__height, __set__selectedChild, tweenBorderMetrics, tweenMargins, tweenContentWidth, tweenContentHeight, tweenOldSelectedIndex, tweenNewSelectedIndex, dispatchEvent, dispatchValueChangedEvent;
    function Accordion()
    {
        super();
    } // End of the function
    function init()
    {
        super.init();
        tabEnabled = true;
        boundingBox_mc._visible = false;
        boundingBox_mc._width = boundingBox_mc._height = 0;
    } // End of the function
    function createChild(symbolName, instanceName, props)
    {
        var _loc3 = super.createChild(symbolName, instanceName, props);
        _loc3._visible = false;
        var _loc4 = this.__get__numChildren() - 1;
        this.createHeaderAndMask(_loc3, _loc4);
        this.invalidate();
        return (_loc3);
    } // End of the function
    function createSegment(symbolName, instanceName, labelStr, iconStr)
    {
        return (this.createChild(symbolName, instanceName, {label: labelStr, icon: iconStr}));
    } // End of the function
    function destroyChildAt(index)
    {
        if (this.__get__numChildren() == 0)
        {
            return;
        } // end if
        super.destroyChildAt(index);
        this.destroyObject(kHeaderNameBase + index);
        this.destroyObject(kMaskNameBase + index);
        var _loc4 = this.__get__numChildren();
        for (var _loc3 = Number(index); _loc3 < _loc4; ++_loc3)
        {
            set(kHeaderNameBase + _loc3, this[kHeaderNameBase + (_loc3 + 1)]);
            this[kHeaderNameBase + _loc3]._name = kHeaderNameBase + _loc3;
            set(kMaskNameBase + _loc3, this[kMaskNameBase + (_loc3 + 1)]);
            this[kMaskNameBase + _loc3]._name = kMaskNameBase + _loc3;
            this[kHeaderNameBase + _loc3].setStateVar(this[kHeaderNameBase + _loc3].getState());
        } // end of for
        delete this[kHeaderNameBase + _loc4];
        delete this[kMaskNameBase + _loc4];
        for (var _loc3 = 0; _loc3 < _loc4; ++_loc3)
        {
            this[kHeaderNameBase + _loc3].swapDepths(kBaseHeaderDepth + _loc3);
            this[kMaskNameBase + _loc3].swapDepths(kBaseMaskDepth + _loc3);
            this[mx.core.View.childNameBase + _loc3].swapDepths(_loc3 + 1);
        } // end of for
        if (_loc4 == 0)
        {
            __selectedIndex = undefined;
        }
        else if (index < __selectedIndex)
        {
            --__selectedIndex;
        }
        else if (index == __selectedIndex)
        {
            if (index == _loc4)
            {
                --__selectedIndex;
            } // end if
            var _loc6 = this[kHeaderNameBase + __selectedIndex];
            _loc6.setState(true);
        } // end else if
        this.invalidate();
    } // End of the function
    function createChildren()
    {
        var _loc7 = childNames.length;
        for (var _loc3 = 0; _loc3 < _loc7; ++_loc3)
        {
            var _loc4 = childSymbols[_loc3];
            if (_loc4 == undefined)
            {
                _loc4 = "View";
            } // end if
            this.createChild(_loc4, childNames[_loc3], {label: childLabels[_loc3], icon: childIcons[_loc3]});
        } // end of for
        super.createChildren();
    } // End of the function
    function initLayout()
    {
        var _loc5 = this.__get__numChildren();
        for (var _loc3 = 0; _loc3 < _loc5; ++_loc3)
        {
            var _loc4 = this.getChildAt(_loc3);
            _loc4.swapDepths(_loc3 + 1);
            this.createHeaderAndMask(_loc4, _loc3);
        } // end of for
        super.initLayout();
    } // End of the function
    function doLayout()
    {
        var _loc17 = border_mc.__get__borderMetrics();
        var _loc15 = -1;
        var _loc16 = -1;
        var _loc18 = this.getStyle("marginTop");
        var _loc9 = this.getStyle("verticalGap");
        var _loc12 = this.calcContentWidth();
        var _loc10 = this.calcContentHeight();
        var _loc13 = _loc17.left + _loc15;
        var _loc3 = _loc17.top + _loc18;
        var _loc7 = _loc13;
        var _loc11 = _loc12;
        var _loc8 = this.getStyle("headerHeight");
        if (_loc15 < 0)
        {
            _loc7 = _loc7 - _loc15;
            _loc11 = _loc11 + _loc15;
        } // end if
        if (_loc16 < 0)
        {
            _loc11 = _loc11 + _loc16;
        } // end if
        var _loc14 = this.__get__numChildren();
        for (var _loc2 = 0; _loc2 < _loc14; ++_loc2)
        {
            var _loc6 = this[kHeaderNameBase + _loc2];
            var _loc5 = this.getChildAt(_loc2);
            var _loc4 = this[kMaskNameBase + _loc2];
            _loc6.move(_loc13, _loc3);
            _loc6.setSize(_loc12, _loc8);
            _loc6.__set__visible(true);
            _loc3 = _loc3 + _loc8;
            _loc4._x = _loc7;
            _loc4._y = _loc3;
            _loc4._width = _loc11;
            _loc4._height = _loc10 + _loc9;
            _loc5._x = _loc7;
            _loc5._y = _loc3;
            _loc5._visible = _loc2 == this.__get__selectedIndex();
            if (_loc2 == this.__get__selectedIndex())
            {
                _loc3 = _loc3 + _loc10;
            } // end if
            _loc3 = _loc3 + _loc9;
        } // end of for
    } // End of the function
    function onSetFocus()
    {
        super.onSetFocus();
        this.getFocusManager().defaultPushButtonEnabled = false;
    } // End of the function
    function onKillFocus()
    {
        super.onKillFocus();
        this.getFocusManager().defaultPushButtonEnabled = true;
    } // End of the function
    function keyDownHandler(evt)
    {
        if (tween != undefined)
        {
            return;
        } // end if
        var _loc2 = this.__get__selectedIndex();
        switch (evt.code)
        {
            case 34:
            {
                if (this.__get__selectedIndex() < this.__get__numChildren() - 1)
                {
                    this.__set__selectedIndex(this.__get__selectedIndex() + 1);
                }
                else
                {
                    this.__set__selectedIndex(0);
                } // end else if
                this.dispatchChangeEvent(_loc2, this.__get__selectedIndex());
                break;
            } 
            case 33:
            {
                if (this.__get__selectedIndex() > 0)
                {
                    this.__set__selectedIndex(this.__get__selectedIndex() - 1);
                }
                else
                {
                    this.__set__selectedIndex(this.__get__numChildren() - 1);
                } // end else if
                this.dispatchChangeEvent(_loc2, this.__get__selectedIndex());
                break;
            } 
            case 36:
            {
                this.__set__selectedIndex(0);
                this.dispatchChangeEvent(_loc2, this.__get__selectedIndex());
                break;
            } 
            case 35:
            {
                this.__set__selectedIndex(this.__get__numChildren() - 1);
                this.dispatchChangeEvent(_loc2, this.__get__selectedIndex());
                break;
            } 
            case 40:
            case 39:
            {
                this.drawHeaderFocus(__focusedIndex, false);
                if (__focusedIndex < this.__get__numChildren() - 1)
                {
                    ++__focusedIndex;
                }
                else
                {
                    __focusedIndex = 0;
                } // end else if
                this.drawHeaderFocus(__focusedIndex, true);
                break;
            } 
            case 38:
            case 37:
            {
                this.drawHeaderFocus(__focusedIndex, false);
                if (__focusedIndex > 0)
                {
                    --__focusedIndex;
                }
                else
                {
                    __focusedIndex = this.__get__numChildren() - 1;
                } // end else if
                this.drawHeaderFocus(__focusedIndex, true);
                break;
            } 
            case 32:
            case 13:
            {
                if (__focusedIndex != this.__get__selectedIndex())
                {
                    this.__set__selectedIndex(__focusedIndex);
                    this.dispatchChangeEvent(_loc2, this.__get__selectedIndex());
                } // end if
            } 
        } // End of switch
    } // End of the function
    function drawFocus(isFocused)
    {
        __bDrawFocus = isFocused;
        this.drawHeaderFocus(__focusedIndex, isFocused);
    } // End of the function
    function getSelectedChild()
    {
        //return (this.getChildAt(this.selectedIndex()));
    } // End of the function
    function get selectedChild()
    {
        return (this.getSelectedChild());
    } // End of the function
    function setSelectedChild(v)
    {
        var _loc3 = this.__get__numChildren();
        for (var _loc2 = 0; _loc2 < _loc3; ++_loc2)
        {
            if (this.getChildAt(_loc2) == v)
            {
                this.setSelectedIndex(_loc2);
                return;
            } // end if
        } // end of for
    } // End of the function
    function set selectedChild(v)
    {
        this.setSelectedChild(v);
        //return (this.selectedChild());
        null;
    } // End of the function
    function getSelectedIndex()
    {
        return (__selectedIndex);
    } // End of the function
    function get selectedIndex()
    {
        return (this.getSelectedIndex());
    } // End of the function
    function setSelectedIndex(v)
    {
        var _loc2 = v;
        if (_loc2 == __selectedIndex)
        {
            return;
        } // end if
        var _loc4 = this[kHeaderNameBase + __selectedIndex];
        _loc4.setState(false);
        var _loc5 = __selectedIndex;
        __selectedIndex = _loc2;
        this.startTween(_loc5, _loc2);
        var _loc3 = this[kHeaderNameBase + __selectedIndex];
        _loc3.setState(true);
        this.drawHeaderFocus(__focusedIndex, false);
        __focusedIndex = __selectedIndex;
        this.drawHeaderFocus(__focusedIndex, __bDrawFocus);
    } // End of the function
    function set selectedIndex(v)
    {
        this.setSelectedIndex(v);
        //return (this.selectedIndex());
        null;
    } // End of the function
    function createHeaderAndMask(content_mc, i)
    {
        if (__selectedIndex == undefined)
        {
            __selectedIndex = 0;
        } // end if
        var _loc3 = {};
        if (falseUpSkin != undefined)
        {
            _loc3.falseUpSkin = falseUpSkin;
        } // end if
        if (falseDownSkin != undefined)
        {
            _loc3.falseDownSkin = falseDownSkin;
        } // end if
        if (falseOverSkin != undefined)
        {
            _loc3.falseOverSkin = falseOverSkin;
        } // end if
        if (falseDisabledSkin != undefined)
        {
            _loc3.falseDisabledSkin = falseDisabledSkin;
        } // end if
        if (trueUpSkin != undefined)
        {
            _loc3.trueUpSkin = trueUpSkin;
        } // end if
        if (trueDownSkin != undefined)
        {
            _loc3.trueDownSkin = trueDownSkin;
        } // end if
        if (trueOverSkin != undefined)
        {
            _loc3.trueOverSkin = trueOverSkin;
        } // end if
        if (trueDisabledSkin != undefined)
        {
            _loc3.trueDisabledSkin = trueDisabledSkin;
        } // end if
        var _loc2 = this.createClassObject(headerClass, kHeaderNameBase + i, kBaseHeaderDepth + i, _loc3);
        _loc2.visible = false;
        _loc2.label = content_mc.label;
        _loc2.tabEnabled = false;
        _loc2.clickHandler = function ()
        {
            _parent.headerPress(this);
        };
        _loc2.setSize(_loc2.width, this.getStyle("headerHeight"));
        _loc2.content_mc = content_mc;
        if (content_mc.icon != undefined)
        {
            _loc2.icon = content_mc.icon;
        } // end if
        if (i == __selectedIndex)
        {
            _loc2.setState(true);
        } // end if
        var _loc6 = this.createObject("BoundingBox", kMaskNameBase + i, kBaseMaskDepth + i);
        content_mc.setMask(_loc6);
    } // End of the function
    function getHeaderAt(idx)
    {
        return (this[kHeaderNameBase + idx]);
    } // End of the function
    function calcContentWidth()
    {
        var _loc2 = this.__get__width();
        var _loc3 = border_mc.__get__borderMetrics();
        _loc2 = _loc2 - (_loc3.left + _loc3.right);
        var _loc5 = -1;
        var _loc4 = -1;
        _loc2 = _loc2 - (_loc5 + _loc4);
        return (_loc2);
    } // End of the function
    function calcContentHeight()
    {
        var _loc3 = this.__get__height();
        var _loc6 = border_mc.__get__borderMetrics();
        _loc3 = _loc3 - (_loc6.top + _loc6.bottom);
        var _loc8 = this.getStyle("marginTop");
        var _loc7 = this.getStyle("marginBottom");
        _loc3 = _loc3 - (_loc8 + _loc7);
        var _loc4 = this.__get__numChildren();
        var _loc5 = this.getStyle("verticalGap");
        for (var _loc2 = 0; _loc2 < _loc4; ++_loc2)
        {
            _loc3 = _loc3 - this[kHeaderNameBase + _loc2].height;
            if (_loc2 > 0)
            {
                _loc3 = _loc3 - _loc5;
            } // end if
        } // end of for
        return (_loc3);
    } // End of the function
    function drawHeaderFocus(headerIndex, isFocused)
    {
        this[kHeaderNameBase + headerIndex].drawFocus(isFocused);
    } // End of the function
    function headerPress(header)
    {
        var _loc2 = this.__get__selectedIndex();
        this.__set__selectedChild(header.content_mc);
        this.dispatchChangeEvent(_loc2, this.__get__selectedIndex());
    } // End of the function
    function startTween(oldSelectedIndex, newSelectedIndex)
    {
        tweenBorderMetrics = border_mc.borderMetrics;
        tweenMargins = new Object();
        tweenMargins.left = -1;
        tweenMargins.top = this.getStyle("marginTop");
        tweenMargins.right = -1;
        tweenMargins.bottom = this.getStyle("marginBottom");
        tweenContentWidth = this.calcContentWidth();
        tweenContentHeight = this.calcContentHeight();
        tweenOldSelectedIndex = oldSelectedIndex;
        tweenNewSelectedIndex = newSelectedIndex;
        tween = new mx.effects.Tween(this, 1, tweenContentHeight - 1, this.getStyle("openDuration"));
        var _loc2 = this.getStyle("openEasing");
        if (_loc2 != undefined)
        {
            tween.easingEquation = _loc2;
        } // end if
    } // End of the function
    function onTweenUpdate(value)
    {
        var _loc16 = tweenBorderMetrics;
        var _loc17 = tweenMargins;
        var _loc19 = tweenContentWidth;
        var _loc15 = tweenContentHeight;
        var _loc9 = tweenOldSelectedIndex;
        var _loc10 = tweenNewSelectedIndex;
        var _loc8 = value;
        var _loc7 = _loc15 - value;
        var _loc11 = _loc9 < _loc10 ? (-_loc8) : (0);
        var _loc13 = _loc10 > _loc9 ? (0) : (-_loc7);
        var _loc4 = _loc16.top + _loc17.top;
        var _loc12 = this.__get__numChildren();
        var _loc14 = this.getStyle("verticalGap");
        for (var _loc2 = 0; _loc2 < _loc12; ++_loc2)
        {
            var _loc6 = this[kHeaderNameBase + _loc2];
            var _loc5 = this.getChildAt(_loc2);
            var _loc3 = this[kMaskNameBase + _loc2];
            _loc6._y = _loc4;
            _loc4 = _loc4 + _loc6.height;
            if (_loc2 == _loc9)
            {
                _loc3._y = _loc4;
                _loc3._height = _loc7;
                _loc5._y = _loc3._y + _loc11;
                _loc5._visible = true;
                _loc4 = _loc4 + _loc7;
            }
            else if (_loc2 == _loc10)
            {
                _loc3._y = _loc4;
                _loc3._height = _loc8;
                _loc5._y = _loc3._y + _loc13;
                _loc5._visible = true;
                _loc4 = _loc4 + _loc8;
            } // end else if
            _loc4 = _loc4 + _loc14;
        } // end of for
    } // End of the function
    function onTweenEnd(value)
    {
        delete this.tweenBorderMetrics;
        delete this.tweenMargins;
        delete this.tweenContentWidth;
        delete this.tweenContentHeight;
        delete this.tweenOldSelectedIndex;
        delete this.tweenNewSelectedIndex;
        delete this.tween;
        this.doLayout();
    } // End of the function
    function dispatchChangeEvent(prevValue, newValue)
    {
        this.dispatchEvent({type: "change", prevValue: prevValue, newValue: newValue});
        this.dispatchValueChangedEvent(this.__get__selectedIndex());
    } // End of the function
    static var symbolName = "Accordion";
    static var symbolOwner = mx.containers.Accordion;
    var className = "Accordion";
    static var version = "2.0.2.126";
    var kBaseHeaderDepth = 1000;
    var kBaseMaskDepth = 2000;
    var kHeaderNameBase = "_header";
    var kMaskNameBase = "_mask";
    var headerClass = mx.containers.accordionclasses.AccordionHeader;
    var __selectedIndex = undefined;
    var __focusedIndex = 0;
    var __bDrawFocus = false;
} // End of Class
