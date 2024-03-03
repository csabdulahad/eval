(() => {

    class JQConnect {

        #isAsync = false
        #resolve = null

        #url;
        #timeout; // number of second for the request to timeout
        #state;
        #status;
        #statusText;

        // Data buffers
        #headers = {};
        #queryParam = {};
        #dataSource = {_raw_data: ''};

        // Buffers the server response.
        // If talking hatish, it will have "response" key trimmed from the
        // json encoded hati server response
        #response;

        // Indicates whether to log the response to the console
        #logResponse = false;

        // Indicates whether it tries to log in json format then falls back to raw text output
        #logAsJson = true;

        // Various callbacks
        #callbackAny;
        #callbackOk;
        #callbackErr;

        #timeoutCallback;
        #unresolvedHost;
        #unknownError;

        #preRedirect;
        #postRun;
        #preRun;

        // Redirection paths and theirs flags
        #anyPath;
        #successPath;
        #errorPath;

        #insDirAny = false;
        #insDirOk = false;
        #insDirErr = false;

        // Variables for toasting
        #noToast = false;
        #toastOnAny = true;
        #autoHideToast = true;
        #toastOnSuccess = false;
        #toastOnError = false;
        #delay = 2;

        // Hati response variables
        #hati = false;
        #hatiMsg;
        #hatiStatus;
        #hatiLevel;

        static HATI_STATUS_SUCCESS = 1;
        static HATI_STATUS_INFO = 2;
        static HATI_STATUS_WARNING = 0;
        static HATI_STATUS_ERROR = -1;
        static HATI_STATUS_UNKNOWN = -2;

        static HATI_LVL_SYSTEM = 0;
        static HATI_LVL_USER = 1;
        static HATI_LVL_UNKNOWN = -2;

        // XHttp state flags
        STATE_REQ_NOT_INITIALIZED = 0;
        STATE_SERVER_CONNECTION_ESTABLISHED = 1;
        STATE_REQ_RECEIVED = 2;
        STATE_REQ_PROCESSING = 3;
        STATE_REQ_FINISH_AND_READY = 4;

        // XHttp status flags
        STATUS_OK = 200;
        STATUS_FORBIDDEN = 403;
        STATUS_PAGE_NOT_FOUND = 404;

        static #contentType = {
            form: 'application/x-www-form-urlencoded',
            json: 'application/json',
            raw: 'text/plain'
        }

        constructor() {
            this.#timeout = 30000;

            this.#timeoutCallback = () => {
                console.warn(`Connection timed out`);
            };
        }

        /**
         * Sets a path to be redirected to after the server response regardless of status/response
         *
         * @param {string} path The path to redirect to
         * @param {boolean} instant Indicates whether to redirect instantly after receiving the response
         * @return {JQConnect}
         * */
        direct(path, instant = false) {
            this.#anyPath = path;
            this.#successPath = null;
            this.#errorPath = null;
            this.#insDirAny = instant;
            return this;
        }

        /**
         * Flags to redirects to the specified path on successful Hati response
         *
         * @param {string} path The path to be redirected to
         * @param {boolean} instant Indicates whether to redirect instantly after receiving the response
         * @return {JQConnect}
         * */
        directSuccess(path, instant = false) {
            this.#anyPath = null;
            this.#successPath = path;
            this.#insDirOk = instant;
            return this;
        }

        /**
         * Flags to redirects to the specified path on error Hati response
         *
         * @param {string} path The path to be redirected to
         * @param {boolean} instant Indicates whether to redirect instantly after receiving the response
         * @return {JQConnect}
         * */
        directError(path, instant = false) {
            this.#anyPath = null;
            this.#errorPath = path;
            this.#insDirErr = instant;
            return this;
        }

        /**
         * Set the callback to be invoked on server response regardless any server
         * response status. This callback can be used as a primary callback for
         * connecting to server which  doesn't talk Hatish.
         * <br>For connection error corresponding callbacks are invoked.
         *
         * @param {function({txt:string, json:object})} callback receives connection
         * result in both raw text format and json format. For json object, it tries
         * to parse the response. If fails then returns null as json value.
         * @return {JQConnect}
         * */
        onAny(callback) {
            this.#callbackAny = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked when the Hati server replied ok response.
         * The callback is invoked for non-hati server, if there is no onAny
         * callback set.
         *
         * @param {function({txt:string, json:object})} callback receives connection
         * result in both raw text format and json format. For json object, it tries
         * to parse the response. If fails then returns null as json value.
         * @return {JQConnect}
         * */
        onOk(callback) {
            this.#callbackOk = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked when the <b>Hati</b> server replied error response
         * <br>
         * This callback is never invoked when JQConnect is decorated with withHati() call. For
         * catching error, use other "on" callback functions such as <b>onTimeout(),
         * onUnresolvedHost()</b> etc.
         *
         * @param {function({txt:string, json:object})} callback receives connection
         * result in both raw text format and json format. For json object, it tries
         * to parse the response. If fails then returns null as json value.
         * @return {JQConnect}
         * */
        onErr(callback) {
            this.#callbackErr = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked before connecting to the server
         *
         * @param {function ()} callback
         * @return {JQConnect}
         * */
        preRun(callback) {
            this.#preRun = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked after receiving server response
         *
         * @param {function ()} callback
         * @return {JQConnect}
         * */
        postRun(callback) {
            this.#postRun = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked before redirection after getting any server response
         *
         * @param {function ()} callback
         * */
        preRedirect(callback) {
            this.#preRedirect = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked on timeout connecting to the server on specified url.
         * Default is 30 seconds.
         *
         * @param {function ()} callback
         * @return {JQConnect}
         * */
        onTimeout(callback) {
            this.#timeoutCallback = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked on encountering unresolved host error
         *
         * @param {function ()} callback
         * @return {JQConnect}
         * */
        onUnresolvedHost(callback) {
            this.#unresolvedHost = callback;
            return this;
        }

        /**
         * Sets the callback to be invoked when any unknown error happened
         *
         * @param {function ()} callback
         * @return {JQConnect}
         * */
        onUnknownError(callback) {
            this.#unknownError = callback;
            return this;
        }

        /**
         * Does further the processing after server replied, and extract Hati API related information accordingly.
         * Also display any Toast was sent by Hati.
         *
         * @return {JQConnect}
         * */
        withHati() {
            this.#hati = true;
            return this;
        }

        #callbackMediator(response) {
            this.#response = response;

            if (!this.#hati) {
                this.#noToast = true;

                /*
                 * Check if http response code starts with 4 or 5 to consider it error!
                 * */
                let firstDigit = this.#status.toString()[0]
                let result = !['4', '5'].includes(firstDigit)

                this.#invokeRedirect(result);
                this.#invokeCallback(result);
                return;
            }

            // validate response for hati & store the response
            try {
                this.#response = JSON.parse(response);
                this.#hatiMsg = this.#response.response['msg'];
                this.#hatiStatus = this.#response.response['status'];

                if (this.#response.response['delay_time'] !== undefined)
                    console.warn('Hati is running in development mode');
            } catch (error) {
                this.#resetHati();
                console.error(`${this.#hatiMsg} ${error.message}.\nResponse: ${this.#response}`);
                this.#invokeCallback(false);
                return;
            }

            let success = this.#hatiStatus === JQConnect.HATI_STATUS_SUCCESS;

            this.#invokeRedirect(success);

            // after any redirection if we are still here yet, then invoke callbacks accordingly
            this.#invokeCallback(success);

            // if no toast then we don't go any further down here
            if (this.#noToast) return;

            // handle sticky toast
            if (!this.#autoHideToast) {
                Toast.show(this.#hatiStatus, this.#hatiMsg, false);
                this.#directAfterToast(success);
                return;
            }

            if (this.#toastOnAny) {
                // here we know it is toast for all types of flags.
                this.#showToast(success);
            } else {
                // show toast only it is either success or error
                if (this.#toastOnSuccess && success) this.#showToast(true);
                else if (this.#toastOnError && !success) this.#showToast(false);
            }
        }

        #invokeRedirect(success) {
            // firstly process any redirection based on no-toast or instant redirection
            if ((this.#insDirAny || this.#noToast) && this.#anyPath) JQConnect.redirect(this.#anyPath);
            if ((this.#insDirOk || this.#noToast) && success) JQConnect.redirect(this.#successPath);
            if ((this.#insDirErr || this.#noToast) && !success) JQConnect.redirect(this.#errorPath);
        }

        #invokeCallback(success) {
            if (this.#callbackAny != null) {
                this.#callbackAny(this.#decorateRes());
            }
            else {
                if (success && this.#callbackOk != null) this.#callbackOk(this.#decorateRes());
                if (!success && this.#callbackErr != null) this.#callbackErr(this.#decorateRes());
            }

            if (this.#postRun) this.#postRun();
        }

        #decorateRes() {
            return {
                txt: this.responseRaw(),
                json: (() => {
                    let x = this.response();
                    return typeof x === 'object' ? x : null
                })()
            }
        }

        #resetHati() {
            this.#hatiStatus = JQConnect.HATI_STATUS_UNKNOWN;
            this.#hatiLevel = JQConnect.HATI_LVL_UNKNOWN;
            this.#hatiMsg = `Server didn't talk Hatish.`;
        }

        #directAfterToast(success) {
            if (this.#anyPath) this.#direct(this.#anyPath);
            else {
                if (success) this.#direct(this.#successPath);
                else this.#direct(this.#errorPath);
            }
        }

        #direct(path) {
            if (this.#preRedirect) this.#preRedirect();
            JQConnect.redirect(path);
        }

        #showToast(success) {
            Toast.show(this.#hatiStatus, this.#hatiMsg, true, () => {
                this.#directAfterToast(success);
            }, this.#delay);
        }

        #hit(as, method) {
            as = as.toLowerCase();
            if (!JQConnect.#contentType.owns(as))
                throw new Error(`The argument 'as' must be one of these: form, json, raw`);

            let url = this.#prepareUrl();
            url = JQConnect.#removeExtraSign(url);

            this.header('Content-Type', JQConnect.#contentType[as]);


            if (['json', 'form'].owns(as)) {
                delete this.#dataSource['_raw_data'];
            }

            let data;
            if (as === 'json') {
                data = JSON.stringify(this.#dataSource);
            } else if (as === 'form') {
                data = JQConnect.parameterize(this.#dataSource);
            } else {
                data = JSON.stringify(this.#dataSource['_raw_data']);
            }

            if (this.#preRun) this.#preRun();

            let jqxhr = $.ajax({
                url: url,
                method: method,
                crossDomain: true,
                timeout: this.#timeout,
                contentType: false,
                headers: this.#headers,
                data: data,
                xhrFields: {
                    withCredentials: true
                },
                processData: false
            });

            jqxhr.done((data, textStatus, jqXHR) => {
                this.#handle(jqXHR, textStatus);
            });

            jqxhr.fail((jqXHR, textStatus) => {
                this.#handle(jqXHR, textStatus)
            });
        }

        #handle(jqXHR, textStatus) {
            this.#state = jqXHR.readyState;
            this.#status = jqXHR.status;
            this.#statusText = textStatus;

            // log the response
            if (this.#logResponse) {
                let data = jqXHR.responseText ?? '';
                if(data.length === 0) {
                    console.info(`Nothing to log`);
                } else if (!this.#logAsJson) {
                    console.log(data);
                } else {
                    try {
                        console.log(JSON.parse(data));
                    } catch {
                        console.log(data);
                    }
                }
            }

            if (this.#status === this.STATUS_PAGE_NOT_FOUND) {
                if (this.#unresolvedHost != null) this.#unresolvedHost();
            } else if (this.#statusText === 'timeout') {
                if (this.#timeoutCallback != null) this.#timeoutCallback();
            } else {
                this.#callbackMediator(jqXHR.responseText);
            }

            if (this.#isAsync && this.#resolve) {
                this.#resolve()

                this.#isAsync = false
                this.#resolve = null
            }
        }

        /**
         * Removes the & and ? marks if it is happened to be
         * */
        static #removeExtraSign(url) {
            if (url.endsWith('&')) url = url.substring(0, url.length - 1);
            if (url.endsWith('?')) url = url.substring(0, url.length - 1);
            return url;
        }

        #prepareUrl() {
            let url = this.#url + '?';
            Object.entries(this.#queryParam).forEach(([k, v]) =>
                url += `${k}=${v}&`
            )
            return url;
        }

        /**
         * Sets the Toast to be sticky when Hati send in a toast
         *
         * @return {JQConnect}
         * */
        stickyToast() {
            this.#autoHideToast = false;
            return this;
        }

        /**
         * Sets flag to not show any toast sent by Hati
         *
         * @return {JQConnect}
         * */
        noToast() {
            this.#noToast = true;
            return this;
        }

        /**
         * Flags to show only Success response by Hati
         *
         * @return {JQConnect}
         * */
        toastSuccess() {
            this.#toastOnAny = false;
            this.#toastOnSuccess = true;
            return this;
        }

        /**
         * Flags to show only Error response by Hati
         *
         * @return {JQConnect}
         * */
        toastError() {
            this.#toastOnAny = false;
            this.#toastOnError = true;
            return this;
        }

        /**
         * Sets the duration for toast to be shown
         *
         * @return {JQConnect}
         * */
        toastTime(time) {
            this.#delay = time;
            return this;
        }

        /**
         * Logs the response for the connection to the console.
         * @param {boolean} asJson When true, it tries to log response as JSON object. If fails then falls back
         * to text output.
         * @return {JQConnect}
         * */
        logResponse(asJson = true) {
            this.#logResponse = true;
            this.#logAsJson = asJson;
            return this;
        }

        /**
         * Sets timeout for connection
         *
         * @param {int} ms Number of milliseconds
         * @return {JQConnect}
         * */
        timeout(ms) {
            this.#timeout = ms;
            return this;
        }

        /**
         * Makes a GET request to the specified url. It ignores the raw data.
         *
         * @param {'form'|'json'} as The Content-Type header is set accordingly when data is sent
         * @throws {Error} When as argument is set as raw data
         * */
        get(as = 'form') {
            as = as.toLowerCase();
            if (!['form', 'json'].owns(as))
                throw new Error('The argument as must be one of these: form, json');

            delete this.#dataSource._raw_data;

            // serialize the data source
            let url = this.#prepareUrl();
            Object.entries(this.#dataSource).forEach(([k, v]) =>
                url += `${k}=${v}&`
            );
            url = JQConnect.#removeExtraSign(url);

            this.header('Content-Type', JQConnect.#contentType[as]);

            if (this.#preRun) this.#preRun();

            let jqxhr = $.ajax({
                url: url,
                method: 'GET',
                crossDomain: true,
                timeout: this.#timeout,
                contentType: false,
                headers: this.#headers,
                xhrFields: {
                    withCredentials: true
                },
                processData: false
            });

            jqxhr.done((data, textStatus, jqXHR) => {
                this.#handle(jqXHR, textStatus)
            });

            jqxhr.fail((jqXHR, textStatus) => {
                this.#handle(jqXHR, textStatus)
            });
        }

        /**
         * Async version of {@link JQConnect.get()}. Callbacks which have been added
         * using on** methods will always run in order first, before the promise is
         * considered fulfilled.
         *
         * @param {'form'|'json'} as The Content-Type header is set accordingly when data is sent
         * @throws {Error} When as argument is set as raw data
         *
         * */
        async getAsync(as = 'form') {
            return new Promise((resolve) => {
                this.#isAsync = true
                this.#resolve = resolve

                this.get(as)
            })
        }

        /**
         * Makes a POST request to the specified url.
         * <br>
         * For as argument data will be sent as:
         * <br>json -- JSON object as part of request body
         * <br>form -- x-www-form-urlencoded as part of the request body
         * <br>raw  -- raw as part of the request body. Use raw() function to add data.
         *
         * @param {'form'|'json'|'raw'=} as The Content-Type header is set accordingly when data is sent
         * */
        post(as = 'form') {
            this.#hit(as, 'POST');
        }

        /**
         * Async version of {@link JQConnect.post()}. Callbacks which have been added
         * using on** methods will always run in order first, before the promise is
         * considered fulfilled. Promise is always resolved, not rejected!<br>
         *
         * For as argument data will be sent as:
         * <br>json -- JSON object as part of request body
         * <br>form -- x-www-form-urlencoded as part of the request body
         * <br>raw  -- raw as part of the request body. Use raw() function to add data.
         *
         * @param {'form'|'json'|'raw'=} as The Content-Type header is set accordingly when data is sent
         * */
        postAsync(as = 'form') {
            return new Promise((resolve) => {
                this.#isAsync = true
                this.#resolve = resolve

                this.#hit(as, 'POST');
            })
        }

        /**
         * Makes a PUT request to the specified url.
         * <br>
         * For as argument data will be sent as:
         * <br>json -- JSON object as part of request body
         * <br>form -- x-www-form-urlencoded as part of the request body
         * <br>raw  -- raw as part of the request body. Use raw() function to add data.
         *
         * @param {'form'|'json'|'raw'=} as The Content-Type header is set accordingly when data is sent
         * */
        put(as = 'json') {
            this.#hit(as, 'PUT');
        }

        /**
         * Async version of {@link JQConnect.put()}. Callbacks which have been added
         * using on** methods will always run in order first, before the promise is
         * considered fulfilled. Promise is always resolved, not rejected!<br>
         *
         * For as argument data will be sent as:
         * <br>json -- JSON object as part of request body
         * <br>form -- x-www-form-urlencoded as part of the request body
         * <br>raw  -- raw as part of the request body. Use raw() function to add data.
         *
         * @param {'form'|'json'|'raw'=} as The Content-Type header is set accordingly when data is sent
         *
         * */
        putAsync(as = 'json') {
            return new Promise((resolve) => {
                this.#isAsync = true
                this.#resolve = resolve

                this.#hit(as, 'PUT');
            })
        }

        /**
         * Makes a DELETE request to the specified url.
         * <br>
         * For as argument data will be sent as:
         * <br>json -- JSON object as part of request body
         * <br>form -- x-www-form-urlencoded as part of the request body
         * <br>raw  -- raw as part of the request body. Use raw() function to add data.
         *
         * @param {'form'|'json'|'raw'=} as The Content-Type header is set accordingly when data is sent
         * */
        delete(as = 'json') {
            this.#hit(as, 'DELETE');
        }

        /**
         * Async version of {@link JQConnect.delete()}. Callbacks which have been added
         * using on** methods will always run in order first, before the promise is
         * considered fulfilled. Promise is always resolved, not rejected!<br>
         *
         * For as argument data will be sent as:
         * <br>json -- JSON object as part of request body
         * <br>form -- x-www-form-urlencoded as part of the request body
         * <br>raw  -- raw as part of the request body. Use raw() function to add data.
         *
         * @param {'form'|'json'|'raw'=} as The Content-Type header is set accordingly when data is sent
         *
         * */
        deleteAsync(as = 'json') {
            return new Promise((resolve) => {
                this.#isAsync = true
                this.#resolve = resolve

                this.#hit(as, 'DELETE');
            })
        }

        /**
         * Sets the url the connection is going to be made to
         *
         * @param {string} url API url
         * @return {JQConnect}
         * */
        to(url) {
            this.#url = url;
            return this;
        }

        /**
         * Any header can be added to the request.
         *
         * @param {string} key Header key
         * @param {string} value Header value
         * @return {JQConnect}
         * */
        header(key, value) {
            this.#headers[key] = value;
            return this;
        }

        /**
         * Sends as raw data as part of request body
         *
         * @param {string|number} data Any value to be sent as a raw data
         * @return {JQConnect}
         * */
        raw(data) {
            if (typeof data === 'object')
                throw new Error(`Data of type Object can't be processed as raw data`);

            this.#dataSource['_raw_data'] += data;

            return this;
        }

        /**
         * Any html form data can be sent using this method. The form argument
         * can either be an id(with/without # sign) or the form object.
         *
         * @param {string|HTMLFormElement} form The form to be sent as body of the request
         * @return {JQConnect}
         * */
        form(form) {
            if (typeof form == 'string') {
                let id = form.startsWith('#') ? form : `#${form}`;
                form = document.getElementById(id);
            }

            if (typeof form != 'object' && !$(form).is('form'))
                throw new Error('Argument must be a form or an id to form');

            // collect the form data as json
            let data = $(form).serializeArray();
            data.forEach((obj) => this.#dataSource[obj.name] = obj.value);

            return this;
        }

        /**
         * Any json object can be sent as part get or post data. For get request
         * the object's key-value pair translates into encoded url query param.
         *
         * @param {string|object} data The JSON data either in object or string form
         * @return {JQConnect}
         * */
        json(data) {
            if (typeof data === 'string') data = JSON.parse(data);

            Object.keys(data).forEach((key) =>
                this.#dataSource[key] = data[key]
            );

            return this;
        }

        /**
         * Sets query parameter to the url
         *
         * @param {string} key The parameter name
         * @param {string|number} value The parameter value
         * @return {JQConnect}
         * */
        queryParam(key, value) {
            this.#queryParam[key] = value;
            return this;
        }

        /**
         * Returns the ajax connection status
         *
         * @return {number}
         * */
        get conStatus() {
            return this.#status;
        }

        /**
         * Returns the ajax connection message
         *
         * @return {string}
         * */
        get conStatusTxt() {
            return this.#statusText;
        }

        /**
         * Returns the message sent by Hati server
         *
         * @return {string} Message sent by Hati.
         * */
        get msg() {
            return this.#hatiMsg;
        }

        /**
         * Returns the status sent by Hati server
         *
         * @return {number} Status sent by Hati.
         */
        get status() {
            return this.#hatiStatus;
        }

        /**
         * Returns the level sent by Hati server
         *
         * @return {number} Level sent by Hati.
         */
        get level() {
            return this.#hatiLevel;
        }

        /**
         * Returns the response in JSON format
         *
         * @return {?object} JSON decoded response
         * */
        response() {
            if (this.#response === 'null') return null;

            if (typeof this.#response === 'object') return this.#response;

            try {
                return JSON.parse(this.#response);
            } catch {
                return null;
            }
        }

        /**
         * Returns the response in raw format
         *
         * @return {string} Response in raw textual format as replied by the server
         * */
        responseRaw() {
            return typeof this.#response === 'object' ? JSON.stringify(this.#response) : this.#response;
        }

        /**
         * Returns whether the connection was made successfully and the server has replied OK
         *
         * @return {boolean} True if the connection state is FINISH_AND_READY and status is OK, false otherwise
         * */
        serverReplied() {
            return this.#state === this.STATE_REQ_FINISH_AND_READY && this.#status === this.STATUS_OK;
        }

        /**
         * Returns whether Hati server has responded
         *
         * @return {boolean} True if hati functioned & responded correctly, false otherwise.
         * */
        hatiResponse() {
            return this.serverReplied() && this.#hatiLevel !== JQConnect.HATI_LVL_UNKNOWN;
        }

        /**
         * Returns whether the outcome of this connection was successful, reported by server
         * header response with either 200 or 204 code. If it is a hati server response, then
         * it will do further check for the status value found in the response object.
         *
         * @param {number} code Hati status code to match found in the response object. 0, 1 & 2 are
         * considered a successful hati response by default.
         * @return {boolean} True if the connection meets the conditions above, false otherwise.
         * */
        isOk (...code) {
            let firstDigit = this.#status.toString()[0]
            let failed = ['4', '5'].includes(firstDigit)

            if (failed) return false

            if (!this.#hati) return true

            if (code.length === 0) code = [0, 1, 2]

            return code.includes[this.status]
        }

        /**
         * On getting an HTTP response code starting with either 4 or 5 is considered an error.
         * If it passes that condition, it is then checked if the connection was made to a hati
         * server. If so, then hati status code is also evaluated to see it was -1.
         *
         * @return {boolean} True if the connection meets the conditions above, false otherwise.
         * */
        isErr () {
            let firstDigit = this.#status.toString()[0]
            let notErr = !['4', '5'].includes(firstDigit)

            if (notErr) return false

            if (!this.#hati) return true

            return this.status === -1
        }

        /**
         * Runs a callback if the connection returned a successful response which particularly doesn't start
         * with 4, 5 and in case of a response from hati server then also it is not status -1.
         *
         * @param {function({txt:string, json:object})} callback receives connection
         * result in both raw text format and json format. For json object, it tries
         * to parse the response. If fails then returns null as json value.
         * */
        ifOk (callback) {
            if (!this.isOk()) return
            callback(this.#decorateRes())
        }

        /**
         * Runs a callback if the connection was unsuccessful meaning the HTTP response code started
         * with 4, 5 or in case of a response from hati server then also it is status -1.
         *
         * @param {function({txt:string, json:object})} callback receives connection
         * result in both raw text format and json format. For json object, it tries
         * to parse the response. If fails then returns null as json value.
         * */
        ifErr (callback) {
            if (!this.isErr()) return
            callback(this.#decorateRes())
        }

        static parameterize(obj) {
            let string = '';
            for (const key in obj) {
                string += `${key}=${obj[key]}&`;
            }
            return string.substring(0, string.length - 1);
        }

        static redirect(path) {
            if (!path) return;
            window.location = path;
        }

    }

    window.JQConnect = () =>  new JQConnect();

    /**
     * Helper function, transfers key-value pair data into query parameters format
     *
     * @param {object} obj JSON object key-value pair to convert to query parameters
     * @returns {string} Query parameterized string
     * */
    window.JQConnect.parameterize = (obj) => JQConnect.parameterize(obj);

    /**
     * Redirects to specified path. Performs checks if the path is defined.
     *
     * @param {string} path
     * */
    window.JQConnect.redirect = (path) => JQConnect.redirect(path);

})();