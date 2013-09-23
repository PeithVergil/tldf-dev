class mx.skins.halo.AccordionHeaderSkin extends mx.skins.RectBorder
{
    var __get__height, __get__width, getStyle, clear, drawRoundRect, beginGradientFill, drawRect, endFill;
    function AccordionHeaderSkin()
    {
        super();
    } // End of the function
    function init()
    {
        super.init();
    } // End of the function
    function size()
    {
        this.drawHaloRect(this.__get__width(), this.__get__height());
    } // End of the function
    function drawHaloRect(w, h)
    {
        var _loc6 = this.getStyle("borderStyle");
        var _loc4 = this.getStyle("themeColor");
        var _loc5 = this.getStyle("textSelectedColor");
        this.clear();
        switch (_loc6)
        {
            case "falseup":
            {
                this.drawRoundRect(0, 0, w, h, 0, 9081738, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.gradientFill(2, 2, w - 2, h - 2, [14342874, 16777215]);
                break;
            } 
            case "falsedown":
            {
                this.drawRoundRect(0, 0, w, h, 0, _loc5, 50);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, _loc4, 100);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, 16777215, 100);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, _loc4, 20);
                break;
            } 
            case "falserollover":
            {
                this.drawRoundRect(0, 0, w, h, 0, _loc5, 50);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, _loc4, 50);
                this.gradientFill(2, 2, w - 2, h - 2, [14342874, 16777215]);
                break;
            } 
            case "falsedisabled":
            {
                this.drawRoundRect(0, 0, w, h, 0, 9081738, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 13159628, 60);
                this.gradientFill(2, 2, w - 2, h - 2, [14342874, 16777215]);
                break;
            } 
            case "trueup":
            {
                this.drawRoundRect(0, 0, w, h, 0, _loc5, 50);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, _loc4, 50);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, 16777215, 100);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, _loc4, 20);
                break;
            } 
            case "truedown":
            {
                this.drawRoundRect(0, 0, w, h, 0, _loc5, 50);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, _loc4, 50);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, 16777215, 100);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, _loc4, 20);
                break;
            } 
            case "truerollover":
            {
                this.drawRoundRect(0, 0, w, h, 0, _loc5, 50);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, _loc4, 50);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, 16777215, 100);
                this.drawRoundRect(2, 2, w - 4, h - 4, 0, _loc4, 20);
                break;
            } 
            case "truedisabled":
            {
                this.drawRoundRect(0, 0, w, h, 0, 9081738, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 16777215, 100);
                this.drawRoundRect(1, 1, w - 2, h - 2, 0, 13159628, 60);
                this.gradientFill(2, 2, w - 2, h - 2, [14342874, 16777215]);
                break;
            } 
        } // End of switch
    } // End of the function
    function gradientFill(x, y, w, h, c)
    {
        var _loc2 = [100, 100];
        var _loc8 = [0, 255];
        var _loc3 = {matrixType: "box", x: x, y: y, w: w, h: h, r: 1.570796E+000};
        this.beginGradientFill("linear", c, _loc2, _loc8, _loc3);
        this.drawRect(x, y, w, h);
        this.endFill();
    } // End of the function
    static function classConstruct()
    {
        mx.core.ext.UIObjectExtensions.Extensions();
        _global.skinRegistry.AccordionHeaderSkin = true;
        return (true);
    } // End of the function
    static var symbolName = "AccordionHeaderSkin";
    static var symbolOwner = mx.skins.halo.AccordionHeaderSkin;
    var className = "AccordionHeaderSkin";
    static var classConstructed = mx.skins.halo.AccordionHeaderSkin.classConstruct();
    static var UIObjectExtensionsDependency = mx.core.ext.UIObjectExtensions;
} // End of Class
