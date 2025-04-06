function changeData(data) {
    function filter(array, condition) {
        let result = [];
        for (let i = 0; i < array.length; i++) {
            if (condition(array[i])) {
                result.push(array[i]);
            }
        }
        return result;
    }

    function getChilds(parentKey, items, currentLevel) {
        currentLevel = currentLevel || 1;
        let subItems = filter(items, function (n) {
            return n.ID_PARENT_BLOCK === parentKey;
        });
        if(subItems.length > 1) {
            subItems.sort(function (a, b) {
                let sortA = a.hasOwnProperty('SORT') ? parseInt(a.SORT) : 0;
                let sortB = b.hasOwnProperty('SORT') ? parseInt(b.SORT) : 0;
                return sortA <= sortB ? -1 : 1;
            });
        }
        let result = [];
        for (let i = 0; i < subItems.length; i++) {
            let subItem = subItems[i];
            subItem.level = currentLevel;

            let kids = getChilds(subItem.ID, items, (currentLevel + 1));
            if (kids.length) {
                if(currentLevel === 5) {
                    subItem.children = [{
                        NAME: '',
                        NUMBER: '',
                        parentItem: subItem,
                        childs: kids,
                        level: 6
                    }]
                } else {
                    subItem.children = kids;
                }
            } else {
                if(currentLevel === 5) {
                    subItem.children = [{
                        NAME: '',
                        NUMBER: '',
                        parentItem: subItem,
                        childs: [],
                        level: 6
                    }]
                }
            }
            result.push(subItem);
        }
        if (parentKey.length || parentKey > 0) {
            return result;
        } else {
            return result.length ? result[0] : [];
        }
    }

    let richMediaData = getChilds("", data);

    return richMediaData;
}

export default changeData;
