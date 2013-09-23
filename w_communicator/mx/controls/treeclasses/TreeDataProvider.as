class mx.controls.treeclasses.TreeDataProvider extends Object
{
    var childNodes, appendChild, insertBefore, parentNode, removeNode;
    function TreeDataProvider()
    {
        super();
    } // End of the function
    static function Initialize(obj)
    {
        obj = obj.prototype;
        if (obj.addTreeNode != undefined)
        {
            return (false);
        } // end if
        var _loc4 = mx.controls.treeclasses.TreeDataProvider.mixinProps;
        var _loc5 = _loc4.length;
        for (var _loc2 = 0; _loc2 < _loc5; ++_loc2)
        {
            obj[_loc4[_loc2]] = mx.controls.treeclasses.TreeDataProvider.mixins[_loc4[_loc2]];
            _global.ASSetPropFlags(obj, _loc4[_loc2], 1);
        } // end of for
        mx.events.EventDispatcher.initialize(obj);
        _global.ASSetPropFlags(obj, "addEventListener", 1);
        _global.ASSetPropFlags(obj, "removeEventListener", 1);
        _global.ASSetPropFlags(obj, "dispatchEvent", 1);
        _global.ASSetPropFlags(obj, "dispatchQueue", 1);
        _global.ASSetPropFlags(obj, "createEvent", 1);
        return (true);
    } // End of the function
    function createProp(obj, propName, setter)
    {
        var p = propName.charAt(0).toUpperCase() + propName.substr(1);
        var _loc2 = null;
        var _loc3 = function (Void)
        {
            return (this["get" + p]());
        };
        if (setter)
        {
            _loc2 = function (val)
            {
                this["set" + p](val);
            };
        } // end if
        obj.addProperty(propName, _loc3, _loc2);
    } // End of the function
    static function convertToNode(tag, arg, data)
    {
        if (typeof(arg) == "string")
        {
            var _loc2 = mx.controls.treeclasses.TreeDataProvider.blankXML.createElement(tag);
            _loc2.attributes.label = arg;
            if (data != undefined)
            {
                _loc2.attributes.data = data;
            } // end if
            return (_loc2);
        }
        else if (arg instanceof XML)
        {
            return (arg.firstChild.cloneNode(true));
        }
        else if (arg instanceof XMLNode)
        {
            return (arg);
        }
        else if (typeof(arg) == "object")
        {
            _loc2 = mx.controls.treeclasses.TreeDataProvider.blankXML.createElement(tag);
            for (var _loc3 in arg)
            {
                _loc2.attributes[_loc3] = arg[_loc3];
            } // end of for...in
            if (data != undefined)
            {
                _loc2.attributes.data = data;
            } // end if
            return (_loc2);
        } // end else if
    } // End of the function
    function addTreeNode(arg, data)
    {
        return (this.addTreeNodeAt(childNodes.length, arg, data));
    } // End of the function
    function addTreeNodeAt(index, arg, data)
    {
        if (index > childNodes.length)
        {
            return;
        } // end if
        var _loc2;
        if (arg instanceof XMLNode)
        {
            _loc2 = arg.removeTreeNode();
        }
        else
        {
            _loc2 = mx.controls.treeclasses.TreeDataProvider.convertToNode("node", arg, data);
        } // end else if
        if (index >= childNodes.length)
        {
            this.appendChild(_loc2);
        }
        else
        {
            this.insertBefore(_loc2, childNodes[index]);
        } // end else if
        this.updateViews({eventName: "addNode", node: _loc2, parentNode: this, index: index});
        return (_loc2);
    } // End of the function
    function getTreeNodeAt(index)
    {
        return (childNodes[index]);
    } // End of the function
    function removeTreeNodeAt(index)
    {
        var _loc2 = childNodes[index];
        _loc2.removeNode();
        this.updateViews({eventName: "removeNode", node: _loc2, parentNode: this, index: index});
        return (_loc2);
    } // End of the function
    function removeTreeNode()
    {
        var _loc4 = parentNode;
        var _loc7;
        var _loc3 = 0;
        for (var _loc2 = parentNode.firstChild; _loc2 != undefined; _loc2 = _loc2.nextSibling)
        {
            if (_loc2 == this)
            {
                _loc7 = _loc3;
                break;
            } // end if
            ++_loc3;
        } // end of for
        if (_loc7 != undefined)
        {
            var _loc5 = this.getRootNode();
            this.removeNode();
            _loc4.updateViews({eventName: "removeNode", node: this, parentNode: _loc4, index: _loc7});
        } // end if
        return (this);
    } // End of the function
    function removeAll()
    {
        while (childNodes.length > 0)
        {
            this.removeTreeNodeAt(childNodes.length - 1);
        } // end while
        var _loc2 = this.getRootNode();
        this.updateViews({eventName: "updateTree"});
    } // End of the function
    function getRootNode()
    {
        for (var _loc2 = this; _loc2.parentNode != undefined && _loc2.isTreeRoot == undefined; _loc2 = _loc2.parentNode)
        {
        } // end of for
        return (_loc2);
    } // End of the function
    function updateViews(eventObj)
    {
        var _loc2 = this;
        eventObj.target = this;
        eventObj.type = "modelChanged";
        while (_loc2 != undefined)
        {
            if (_loc2.isTreeRoot || _loc2.parentNode == undefined)
            {
                _loc2.dispatchEvent(eventObj);
            } // end if
            _loc2 = _loc2.parentNode;
        } // end while
    } // End of the function
    static var mixinProps = ["addTreeNode", "addTreeNodeAt", "getTreeNodeAt", "removeTreeNodeAt", "getRootNode", "getDepth", "removeAll", "removeTreeNode", "updateViews"];
    static var evtDipatcher = mx.events.EventDispatcher;
    static var mixins = new mx.controls.treeclasses.TreeDataProvider();
    static var blankXML = new XML();
    static var largestID = 0;
} // End of Class
