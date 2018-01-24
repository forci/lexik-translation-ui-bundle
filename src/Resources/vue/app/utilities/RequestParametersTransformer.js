class RequestParametersTransformer {

    static serialize(obj, prefix) {
        let str = [], p;
        for (p in obj) {
            if (obj.hasOwnProperty(p)) {
                let k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
                str.push((v !== null && typeof v === "object") ?
                    this.serialize(v, k) :
                    encodeURIComponent(k) + "=" + encodeURIComponent(v));
            }
        }

        return str.join("&");
    }

    // not really needed anymore, delete
    static objectToFormData(obj, form, namespace) {
        let fd = form || new FormData();
        let formKey;

        for (let property in obj) {
            if (obj.hasOwnProperty(property)) {

                if (namespace) {
                    formKey = namespace + '[' + property + ']';
                } else {
                    formKey = property;
                }

                if (typeof obj[property] === 'object' && !(obj[property] instanceof File)) {

                    this.objectToFormData(obj[property], fd, property);

                } else {
                    fd.append(formKey, obj[property]);
                }
            }
        }

        return fd;
    };
}

export default RequestParametersTransformer;