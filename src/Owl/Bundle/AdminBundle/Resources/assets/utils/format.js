export const objectToFormData = (obj, formData = new FormData(), parentKey = '') => {
    for (const key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
            const fullKey = parentKey ? `${parentKey}[${key}]` : key;
            const value = obj[key];

            if (value instanceof File) {
                formData.append(fullKey, value);
            } else if (Array.isArray(value)) {
                value.forEach((val, index) => {
                    formData.append(`${fullKey}[${index}]`, val);
                });
            } else if (typeof value === 'object' && value !== null) {
                this.objectToFormData(value, formData, fullKey);
            } else {
                formData.append(fullKey, value);
            }
        }
    }
    return formData;
};