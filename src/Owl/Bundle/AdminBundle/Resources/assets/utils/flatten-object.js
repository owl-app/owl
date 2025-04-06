function flattenObject (obj, separator = '.', parent = null, res = {}) {
    for (let key in obj) {
        if (typeof obj[key] == 'object') {
            let propName = parent ? parent + separator + key : key;
            flattenObject(obj[key], separator, propName, res);
        } else {
            res[parent] = obj[key];
        }
    }

    return res;
}

export default flattenObject;