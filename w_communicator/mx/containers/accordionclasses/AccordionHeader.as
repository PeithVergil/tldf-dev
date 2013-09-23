class mx.containers.accordionclasses.AccordionHeader extends mx.controls.Button
{
    var swapDepths, focus_mc, createObject, getStyle, __get__height, __get__width;
    function AccordionHeader()
    {
        super();
    } // End of the function
    function onRollOver()
    {
        this.swapDepths(maxDepth);
        super.onRollOver();
    } // End of the function
    function drawFocus(isFocused)
    {
        if (isFocused)
        {
            if (focus_mc == undefined)
            {
                focus_mc = this.createObject("FocusRect", "focus_mc", 10);
            } // end if
            focus_mc.move(1, 1);
            focus_mc.setSize(this.__get__width() - 2, this.__get__height() - 2, 0, 100, this.getStyle("themeColor"));
            focus_mc._visible = true;
        }
        else
        {
            focus_mc._visible = false;
        } // end else if
    } // End of the function
    static var symbolName = "AccordionHeader";
    var ignoreClassStyleDeclaration = {Button: 1};
    static var symbolOwner = mx.containers.accordionclasses.AccordionHeader;
    var className = "AccordionHeader";
    var falseUpSkin = "AccordionHeaderSkin";
    var falseDownSkin = "AccordionHeaderSkin";
    var falseOverSkin = "AccordionHeaderSkin";
    var falseDisabledSkin = "AccordionHeaderSkin";
    var trueUpSkin = "AccordionHeaderSkin";
    var trueDownSkin = "AccordionHeaderSkin";
    var trueOverSkin = "AccordionHeaderSkin";
    var trueDisabledSkin = "AccordionHeaderSkin";
    var centerContent = false;
    var btnOffset = 0;
    var maxDepth = 1999;
    static var version = "2.0.2.126";
} // End of Class
