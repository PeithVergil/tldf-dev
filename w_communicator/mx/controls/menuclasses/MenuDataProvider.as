class mx.controls.menuclasses.MenuDataProvider extends Object
{
    var addTreeNode, addTreeNodeAt, removeTreeNode, getTreeNodeAt, childNodes;
    function MenuDataProvider()
    {
        super();
    } // End of the function
    static function Initialize(obj)
    {
        obj = obj.prototype;
        var _loc3 = mx.controls.menuclasses.MenuDataProvider.mixinProps;
        var _loc5 = _loc3.length;
        for (var _loc2 = 0; _loc2 < _loc5; ++_loc2)
        {
            obj[_loc3[_loc2]] = mx.controls.menuclasses.MenuDataProvider.mixins[_loc3[_loc2]];
            _global.ASSetPropFlags(obj, _loc3[_loc2], 1);
        } // end of for
        return (true);
    } // End of the function
    function addMenuItem(arg)
    {
        return (this.addTreeNode(mx.controls.treeclasses.TreeDataProvider.convertToNode("menuitem", arg)));
    } // End of the function
    function addMenuItemAt(index, arg)
    {
        return (this.addTreeNodeAt(index, mx.controls.treeclasses.TreeDataProvider.convertToNode("menuitem", arg)));
    } // End of the function
    function removeMenuItem(Void)
    {
        return (this.removeTreeNode());
    } // End of the function
    function removeMenuItemAt(index)
    {
        return (this.getTreeNodeAt(index).removeTreeNode());
    } // End of the function
    function getMenuItemAt(index)
    {
        return (this.getTreeNodeAt(index));
    } // End of the function
    function indexOf(item)
    {
        for (var _loc2 = 0; _loc2 < childNodes.length; ++_loc2)
        {
            if (childNodes[_loc2] == item)
            {
                return (_loc2);
            } // end if
        } // end of for
        return;
    } // End of the function
    static var mixinProps = ["addMenuItem", "addMenuItemAt", "getMenuItemAt", "removeMenuItem", "removeMenuItemAt", "normalize", "indexOf"];
    static var mixins = new mx.controls.menuclasses.MenuDataProvider();
} // End of Class
