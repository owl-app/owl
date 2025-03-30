function flattenObject (obj, parent, res = {}) {
    for (let key in obj) {
        if (typeof obj[key] == 'object') {
            let propName = parent ? parent + '.' + key : key;
            flattenObject(obj[key], propName, res);
        } else {
            res[parent] = obj[key];
        }
    }

    return res;
}

export default flattenObject;