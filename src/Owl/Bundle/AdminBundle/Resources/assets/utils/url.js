function updateSingleQueryParam(rootKey, nestedObjectOrValue, $prefix) {
    const url = new URL(window.location.href);
    const searchParams = new URLSearchParams(url.search);

    const deletePrefix = `${$prefix}[${rootKey}]`;
    for (const key of Array.from(searchParams.keys())) {
        if (key === deletePrefix || key.startsWith(deletePrefix + '[')) {
            searchParams.delete(key);
        }
    }

    function flatten(obj, path = []) {
        const result = [];
        for (const key in obj) {
            const val = obj[key];
            const currentPath = path.concat(key);

            if (typeof val === 'object' && val !== null && !Array.isArray(val)) {
                result.push(...flatten(val, currentPath));
            } else if (Array.isArray(val)) {
                val.forEach((v, i) => {
                    result.push([currentPath.concat(i), v]);
                });
            } else {
                result.push([currentPath, val]);
            }
        }
        return result;
    }

    if (typeof nestedObjectOrValue === 'object' && nestedObjectOrValue !== null) {
        const entries = flatten(nestedObjectOrValue);
        for (const [subPath, value] of entries) {
            const fullKey = `${$prefix}[${rootKey}]` + subPath.map(k => `[${k}]`).join('');
            searchParams.set(fullKey, value);
        }
    } else {
        searchParams.set(`${$prefix}[${rootKey}]`, nestedObjectOrValue);
    }

    url.search = searchParams.toString();

    return url;
}

export default updateSingleQueryParam;