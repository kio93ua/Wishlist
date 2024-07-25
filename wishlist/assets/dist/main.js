(function (factory) {
  typeof define === 'function' && define.amd ? define(factory) :
  factory();
})((function () { 'use strict';

  function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);

    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(object);

      if (enumerableOnly) {
        symbols = symbols.filter(function (sym) {
          return Object.getOwnPropertyDescriptor(object, sym).enumerable;
        });
      }

      keys.push.apply(keys, symbols);
    }

    return keys;
  }

  function _objectSpread2(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};

      if (i % 2) {
        ownKeys(Object(source), true).forEach(function (key) {
          _defineProperty(target, key, source[key]);
        });
      } else if (Object.getOwnPropertyDescriptors) {
        Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
      } else {
        ownKeys(Object(source)).forEach(function (key) {
          Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
        });
      }
    }

    return target;
  }

  function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
    try {
      var info = gen[key](arg);
      var value = info.value;
    } catch (error) {
      reject(error);
      return;
    }

    if (info.done) {
      resolve(value);
    } else {
      Promise.resolve(value).then(_next, _throw);
    }
  }

  function _asyncToGenerator(fn) {
    return function () {
      var self = this,
          args = arguments;
      return new Promise(function (resolve, reject) {
        var gen = fn.apply(self, args);

        function _next(value) {
          asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
        }

        function _throw(err) {
          asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
        }

        _next(undefined);
      });
    };
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  function _classPrivateFieldGet(receiver, privateMap) {
    var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "get");

    return _classApplyDescriptorGet(receiver, descriptor);
  }

  function _classPrivateFieldSet(receiver, privateMap, value) {
    var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "set");

    _classApplyDescriptorSet(receiver, descriptor, value);

    return value;
  }

  function _classExtractFieldDescriptor(receiver, privateMap, action) {
    if (!privateMap.has(receiver)) {
      throw new TypeError("attempted to " + action + " private field on non-instance");
    }

    return privateMap.get(receiver);
  }

  function _classApplyDescriptorGet(receiver, descriptor) {
    if (descriptor.get) {
      return descriptor.get.call(receiver);
    }

    return descriptor.value;
  }

  function _classApplyDescriptorSet(receiver, descriptor, value) {
    if (descriptor.set) {
      descriptor.set.call(receiver, value);
    } else {
      if (!descriptor.writable) {
        throw new TypeError("attempted to set read only private field");
      }

      descriptor.value = value;
    }
  }

  function _checkPrivateRedeclaration(obj, privateCollection) {
    if (privateCollection.has(obj)) {
      throw new TypeError("Cannot initialize the same private elements twice on an object");
    }
  }

  function _classPrivateFieldInitSpec(obj, privateMap, value) {
    _checkPrivateRedeclaration(obj, privateMap);

    privateMap.set(obj, value);
  }

  var eventBus = function eventBus() {
    var data = {};
    return {
      on: (events, callback) => {
        events.split(' ').forEach(event => {
          (data[event] = data[event] || []).push(callback);
        });
      },
      off: (events, callback) => {
        events.split(' ').forEach(event => {
          data[event] = (data[event] || []).filter(i => i !== callback);
        });
      },
      emit: function emit(event) {
        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        (data[event] || []).forEach(callback => callback(...args));
      },
      destroy: () => data = {}
    };
  };

  var restApi = function restApi(url) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    var restUrl = url;
    var config = options;

    var request = /*#__PURE__*/function () {
      var _ref = _asyncToGenerator(function* (method, url, route) {
        var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
        var body = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;

        try {
          url = route ? "".concat(url, "/").concat(route) : url;
          var init = {
            method: method,
            headers: new Headers({
              'Content-Type': 'application/json',
              'X-WP-Nonce': options.nonce
            }),
            body
          };
          var response = yield fetch(url, init);
          return yield response.json();
        } catch (e) {
          throw new Error(e);
        }
      });

      return function request(_x, _x2, _x3) {
        return _ref.apply(this, arguments);
      };
    }();

    return {
      get: function () {
        var _get = _asyncToGenerator(function* () {
          var route = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
          return yield request('GET', restUrl, route, config);
        });

        function get() {
          return _get.apply(this, arguments);
        }

        return get;
      }(),
      post: function () {
        var _post = _asyncToGenerator(function* () {
          var route = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
          var body = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
          return yield request('POST', restUrl, route, config, body);
        });

        function post() {
          return _post.apply(this, arguments);
        }

        return post;
      }(),
      delete: function () {
        var _delete2 = _asyncToGenerator(function* () {
          var route = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
          var body = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
          return yield request('DELETE', restUrl, route, config, body);
        });

        function _delete() {
          return _delete2.apply(this, arguments);
        }

        return _delete;
      }(),
      patch: function () {
        var _patch = _asyncToGenerator(function* () {
          var route = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
          var body = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
          return yield request('PATCH', restUrl, route, config, body);
        });

        function patch() {
          return _patch.apply(this, arguments);
        }

        return patch;
      }()
    };
  };

  var defaults = {
    classes: {
      message: 'dcaw-message',
      box: 'dcaw-messages-wrapper'
    },
    timeout: 4000,
    max: 5
  };

  var messageHub = function messageHub() {
    var messageBox = document.querySelector(".".concat(defaults.classes.box));
    var data = [];

    function findElement(element) {
      return data.find(i => i.element === element);
    }

    function buildElement(str) {
      var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'success';
      var element = document.createElement('div');
      element.classList.add(defaults.classes.message, "is-".concat(type));
      element.textContent = str;
      return element;
    }

    function removeElement(element) {
      var message = findElement(element);
      clearTimeout(message.timeout);
      messageBox.removeChild(message.element);
      data = data.filter(i => i !== message);
    }

    function insertElement(element) {
      var {
        timeout
      } = findElement(element);
      messageBox.appendChild(element);
      element.addEventListener('click', event => {
        event.stopPropagation();
        clearTimeout(timeout);
        removeElement(element);
      });
      element.addEventListener('mouseover', () => {
        clearTimeout(timeout);
      });
      element.addEventListener('mouseleave', () => {
        timeout = elementTimeout(element);
      });
    }

    function elementTimeout(element) {
      return setTimeout(() => {
        removeElement(element);
      }, defaults.timeout);
    }

    function buildBox() {
      messageBox = document.createElement('div');
      messageBox.classList.add(defaults.classes.box);
      document.body.appendChild(messageBox);
    }

    return {
      show: (str, type) => {
        if (!messageBox) buildBox();
        var element = buildElement(str, type);
        var timeout = elementTimeout(element);

        if (data.length >= defaults.max) {
          removeElement(data[0].element);
        }

        data.push({
          element,
          timeout
        });
        insertElement(element);
      }
    };
  };

  var _Event = /*#__PURE__*/new WeakMap();

  var _Message = /*#__PURE__*/new WeakMap();

  var _Options = /*#__PURE__*/new WeakMap();

  var _Rest = /*#__PURE__*/new WeakMap();

  var _Store = /*#__PURE__*/new WeakMap();

  var _Counters = /*#__PURE__*/new WeakMap();

  var _Tables = /*#__PURE__*/new WeakMap();

  var _Name = /*#__PURE__*/new WeakMap();

  class Core {
    constructor(name) {
      var _this = this;

      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      _classPrivateFieldInitSpec(this, _Event, {
        writable: true,
        value: eventBus()
      });

      _classPrivateFieldInitSpec(this, _Message, {
        writable: true,
        value: messageHub()
      });

      _classPrivateFieldInitSpec(this, _Options, {
        writable: true,
        value: {}
      });

      _classPrivateFieldInitSpec(this, _Rest, {
        writable: true,
        value: void 0
      });

      _classPrivateFieldInitSpec(this, _Store, {
        writable: true,
        value: []
      });

      _classPrivateFieldInitSpec(this, _Counters, {
        writable: true,
        value: []
      });

      _classPrivateFieldInitSpec(this, _Tables, {
        writable: true,
        value: []
      });

      _classPrivateFieldInitSpec(this, _Name, {
        writable: true,
        value: void 0
      });

      _defineProperty(this, "mount", () => {
        _classPrivateFieldGet(this, _Rest).get().then(result => {
          if (result.ok) {
            _classPrivateFieldSet(this, _Store, result.data);

            this.initCounters();
            this.initTables();
            this.initEvents();
            this.emit('mount');
          }
        });
      });

      _defineProperty(this, "onMaximum", result => {
        this.show(result.message, 'info');
      });

      _defineProperty(this, "onDeleteProduct", result => {
        this.show(result.message, 'info');
      });

      _defineProperty(this, "onPostProduct", result => {
        this.show(result.message);
      });

      _defineProperty(this, "initCounters", () => {
        var {
          selectors,
          classes
        } = _classPrivateFieldGet(this, _Options);

        document.querySelectorAll(selectors.counters).forEach(parent => {
          var span = document.createElement('span');
          span.classList.add(classes.counter, classes.empty);

          _classPrivateFieldGet(this, _Counters).push(span);

          parent.appendChild(span);
        });
      });

      _defineProperty(this, "updateCounters", () => {
        var length = _classPrivateFieldGet(this, _Store).reduce((acc, data) => {
          if (data.products) {
            return acc + data.products.length;
          }
        }, 0);

        var {
          classes: {
            empty
          }
        } = _classPrivateFieldGet(this, _Options);

        _classPrivateFieldGet(this, _Counters).forEach(counter => {
          if (length) {
            counter.textContent = length.toString();
            counter.classList.remove(empty);
          } else {
            counter.classList.add(empty);
            counter.textContent = '';
          }
        });
      });

      _defineProperty(this, "initTables", () => {
        document.querySelectorAll(_classPrivateFieldGet(this, _Options).selectors.tables).forEach(table => {
          _classPrivateFieldGet(this, _Tables).push(table);
        });
      });

      _defineProperty(this, "updateTables", () => {
        _classPrivateFieldGet(this, _Tables).forEach( /*#__PURE__*/function () {
          var _ref = _asyncToGenerator(function* (table) {
            _this.emit('loading', true, table);

            var result = yield _this.rest.get('table');
            table.innerHTML = result.data;

            _this.emit('loading', false, table);
          });

          return function (_x) {
            return _ref.apply(this, arguments);
          };
        }());
      });

      _defineProperty(this, "onLoading", function () {
        var loading = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
        var elem = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

        if (elem instanceof Element) {
          var method = loading ? 'add' : 'remove';
          elem.classList[method](_classPrivateFieldGet(_this, _Options).classes.loading);
        }
      });

      _defineProperty(this, "documentClickHandler", /*#__PURE__*/function () {
        var _ref2 = _asyncToGenerator(function* (event) {
          var {
            selectors: {
              button
            }
          } = _classPrivateFieldGet(_this, _Options);

          if (event.target.closest(button)) {
            event.preventDefault();

            var {
              selectors: {
                button: _button
              },
              classes: {
                active
              }
            } = _classPrivateFieldGet(_this, _Options);

            var $button = event.target.closest(_button);
            var productID = parseInt($button.dataset.id);

            _this.emit('loading', true, $button);

            try {
              var inStore = _classPrivateFieldGet(_this, _Store).some(data => {
                if (data.products && data.products.includes(productID)) {
                  return true;
                }
              });

              if (inStore) {
                // stored product
                $button.classList.remove(active);
                yield _this.deleteProduct(productID);
              } else {
                // new product
                yield _this.postProduct(productID);
                $button.classList.add(active);
              }
            } catch (e) {
              _this.onError(e);
            } finally {
              _this.emit('loading', false, $button);

              _this.emit('updated');
            }
          }
        });

        return function (_x2) {
          return _ref2.apply(this, arguments);
        };
      }());

      _defineProperty(this, "postProduct", /*#__PURE__*/function () {
        var _ref3 = _asyncToGenerator(function* (productID, body) {
          var result = yield _classPrivateFieldGet(_this, _Rest).post("product/".concat(productID), body);

          if (result.ok) {
            _classPrivateFieldSet(_this, _Store, result.data);

            _this.emit('post-product', result);
          } else {
            throw result;
          }

          return result;
        });

        return function (_x3, _x4) {
          return _ref3.apply(this, arguments);
        };
      }());

      _defineProperty(this, "deleteProduct", /*#__PURE__*/function () {
        var _ref4 = _asyncToGenerator(function* (productID, body) {
          var result = yield _classPrivateFieldGet(_this, _Rest).delete("product/".concat(productID), body);

          if (result.ok) {
            _classPrivateFieldSet(_this, _Store, result.data);

            _this.emit('delete-product', result);
          } else {
            throw result;
          }

          return result;
        });

        return function (_x5, _x6) {
          return _ref4.apply(this, arguments);
        };
      }());

      _defineProperty(this, "on", (event, callback) => {
        _classPrivateFieldGet(this, _Event).on("".concat(_classPrivateFieldGet(this, _Name), ":").concat(event), callback);

        return this;
      });

      _defineProperty(this, "off", (event, callback) => {
        _classPrivateFieldGet(this, _Event).off("".concat(_classPrivateFieldGet(this, _Name), ":").concat(event), callback);

        return this;
      });

      _defineProperty(this, "emit", function (event) {
        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        _classPrivateFieldGet(_this, _Event).emit("".concat(_classPrivateFieldGet(_this, _Name), ":").concat(event), ...args);

        return _this;
      });

      _defineProperty(this, "destroy", function () {
        var completely = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

        _this.emit("".concat(_classPrivateFieldGet(_this, _Name), ":destroy"), completely);

        _classPrivateFieldGet(_this, _Event).destroy();

        return _this;
      });

      _defineProperty(this, "show", function () {
        _classPrivateFieldGet(_this, _Message).show(...arguments);
      });

      _classPrivateFieldSet(this, _Options, options);

      _classPrivateFieldSet(this, _Name, name);

      var {
        nonce,
        rest
      } = _classPrivateFieldGet(this, _Options);

      _classPrivateFieldSet(this, _Rest, restApi(rest + '/' + name, {
        nonce
      }));
    }

    initEvents() {
      this.on('loading', this.onLoading);
      this.on('max', this.onMaximum);
      this.on('post-product', this.onPostProduct);
      this.on('delete-product', this.onDeleteProduct);
      this.on('mount', this.updateCounters);
      this.on('updated', this.updateCounters);
      this.on('updated', this.updateTables);
      document.addEventListener('click', this.documentClickHandler);
    }

    onError(error) {
      if (error.code) {
        this.emit(error.code, error);
      }
    }

    get options() {
      return _classPrivateFieldGet(this, _Options);
    }

    get rest() {
      return _classPrivateFieldGet(this, _Rest);
    }

    get store() {
      return _classPrivateFieldGet(this, _Store);
    }

    set store(value) {
      _classPrivateFieldSet(this, _Store, value);
    }

    get tables() {
      return _classPrivateFieldGet(this, _Tables);
    }

  }

  var copyToClipboard = str => {
    var el = document.createElement('textarea');
    el.value = str;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
  };

  var _moveProducts = /*#__PURE__*/new WeakMap();

  class Wishlist extends Core {
    constructor() {
      var _this,
          _superprop_getOnError = () => super.onError;

      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      super('wishlist', options);
      _this = this;

      _classPrivateFieldInitSpec(this, _moveProducts, {
        writable: true,
        value: []
      });

      _defineProperty(this, "updateMovedProducts", () => {
        this.tables.forEach(table => {
          var checkboxs = table.querySelectorAll('input[name="move_products"]');

          _classPrivateFieldSet(this, _moveProducts, Array.from(checkboxs).filter(checkbox => checkbox.checked).map(checkbox => parseInt(checkbox.value)));
        });
      });

      _defineProperty(this, "tableSubmitHandler", /*#__PURE__*/function () {
        var _ref = _asyncToGenerator(function* (e) {
          e.preventDefault();
          var formData = new FormData(e.target);

          switch (e.target.dataset.name) {
            case 'edit_list':
              yield _this.patchList(formData.get('id'), JSON.stringify({
                id: formData.get('id'),
                title: formData.get('title')
              }));
              break;

            case 'move_products':
              if (_classPrivateFieldGet(_this, _moveProducts).length) {
                yield _this.patchList(formData.get('prev_list'), JSON.stringify({
                  // eslint-disable-next-line camelcase
                  prev_list: formData.get('prev_list'),
                  // eslint-disable-next-line camelcase
                  next_list: formData.get('next_list'),
                  products: _classPrivateFieldGet(_this, _moveProducts)
                }));
              } else {
                return;
              }

              break;
          }

          _this.emit('updated');
        });

        return function (_x) {
          return _ref.apply(this, arguments);
        };
      }());

      _defineProperty(this, "tableClickHandler", /*#__PURE__*/function () {
        var _ref2 = _asyncToGenerator(function* (e) {
          if (e.target.closest('[data-action]')) {
            e.preventDefault();
            yield _this.buttonActions(e.target.closest('[data-action]'));
          }

          if (e.target.closest(_this.options.selectors.remove)) {
            e.preventDefault();
            yield _this.removeProductBtn(e.target.closest(_this.options.selectors.remove));
          }
        });

        return function (_x2) {
          return _ref2.apply(this, arguments);
        };
      }());

      _defineProperty(this, "buttonActions", /*#__PURE__*/function () {
        var _ref3 = _asyncToGenerator(function* (button) {
          var id = button.dataset.id;
          var action = button.dataset.action;

          _this.emit('loading', true, button);

          try {
            switch (action) {
              case 'delete':
                yield _this.deleteList(id);
                break;

              case 'default':
                yield _this.patchList(id, JSON.stringify({
                  default: true
                }));
                break;

              case 'share':
                yield _this.shareList(id);
                break;
            }
          } catch (e) {
            _superprop_getOnError().call(_this, e);
          } finally {
            _this.emit('loading', false, button);

            _this.emit('updated');
          }
        });

        return function (_x3) {
          return _ref3.apply(this, arguments);
        };
      }());

      _defineProperty(this, "removeProductBtn", /*#__PURE__*/function () {
        var _ref4 = _asyncToGenerator(function* (button) {
          var productID = parseInt(button.dataset.id);
          var listID = button.dataset.listId;

          _this.emit('loading', true, button);

          try {
            yield _this.deleteProduct(productID, JSON.stringify({
              // eslint-disable-next-line camelcase
              list_id: listID
            }));
          } catch (e) {
            _this.onError(e);
          } finally {
            _this.emit('loading', false, button);

            _this.emit('updated');
          }
        });

        return function (_x4) {
          return _ref4.apply(this, arguments);
        };
      }());

      _defineProperty(this, "createWishlistBtnHandler", /*#__PURE__*/function () {
        var _ref5 = _asyncToGenerator(function* (e) {
          e.preventDefault();
          var button = e.currentTarget;

          _this.emit('loading', true, button);

          try {
            yield _this.createList();
          } catch (e) {
            _superprop_getOnError().call(_this, e);
          } finally {
            _this.emit('loading', false, button);

            _this.emit('updated');
          }
        });

        return function (_x5) {
          return _ref5.apply(this, arguments);
        };
      }());

      _defineProperty(this, "shareList", /*#__PURE__*/function () {
        var _ref6 = _asyncToGenerator(function* (id) {
          var result = yield _this.rest.get("list/share/".concat(id));

          if (result.ok) {
            _this.emit('shared-list', result);
          } else {
            throw result;
          }

          return result;
        });

        return function (_x6) {
          return _ref6.apply(this, arguments);
        };
      }());

      _defineProperty(this, "createList", /*#__PURE__*/_asyncToGenerator(function* () {
        var result = yield _this.rest.post('list');

        if (result.ok) {
          _this.store = result.data;

          _this.emit('post-list', result);
        } else {
          throw result;
        }

        return result;
      }));

      _defineProperty(this, "patchList", /*#__PURE__*/function () {
        var _ref8 = _asyncToGenerator(function* (id, body) {
          var result = yield _this.rest.patch("list/".concat(id), body);

          if (result.ok) {
            _this.store = result.data;

            _this.emit('patch-list', result);
          } else {
            throw result;
          }

          return result;
        });

        return function (_x7, _x8) {
          return _ref8.apply(this, arguments);
        };
      }());

      _defineProperty(this, "deleteList", /*#__PURE__*/function () {
        var _ref9 = _asyncToGenerator(function* (id) {
          var result = yield _this.rest.delete("list/".concat(id));

          if (result.ok) {
            _this.store = result.data;

            _this.emit('delete-list', result);
          } else {
            throw result;
          }

          return result;
        });

        return function (_x9) {
          return _ref9.apply(this, arguments);
        };
      }());

      _defineProperty(this, "onGuestClick", result => {
        this.show(result.message);
        window.location.href = this.options.accountLink;
      });

      _defineProperty(this, "onShareList", result => {
        copyToClipboard(result.data);
        this.show(result.message);
      });

      _defineProperty(this, "onPostList", result => {
        this.show(result.message);
      });

      _defineProperty(this, "onDeleteList", result => {
        this.show(result.message, 'warning');
      });

      _defineProperty(this, "onPatchList", result => {
        this.show(result.message, 'info');
      });
    }

    initEvents() {
      super.initEvents();
      this.on('guest', this.onGuestClick);
      this.on('shared-list', this.onShareList);
      this.on('post-list', this.onPostList);
      this.on('delete-list', this.onDeleteList);
      this.on('patch-list', this.onPatchList);
      this.on('loading', this.updateMovedProducts);
      document.querySelectorAll(this.options.selectors.createWishlist).forEach(item => {
        item.addEventListener('click', this.createWishlistBtnHandler);
      });
      this.tables.forEach(table => {
        table.addEventListener('click', this.tableClickHandler);
        table.addEventListener('submit', this.tableSubmitHandler);
        table.addEventListener('change', this.updateMovedProducts);
      });
    }

  }

  class Compare extends Core {
    constructor() {
      var _superprop_getOnError = () => super.onError,
          _this;

      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      super('compare', options);
      _this = this;

      _defineProperty(this, "tableClickHandler", /*#__PURE__*/function () {
        var _ref = _asyncToGenerator(function* (e) {
          if (e.target.closest('[data-action="clear"]')) {
            e.preventDefault();
            var button = e.target.closest('[data-action="clear"]');

            _this.emit('loading', true, button);

            try {
              yield _this.clearCompare();
            } catch (e) {
              _superprop_getOnError().call(_this, e);
            } finally {
              _this.emit('loading', false, button);

              _this.emit('updated');
            }
          }

          if (e.target.closest(_this.options.selectors.remove)) {
            e.preventDefault();

            var _button = e.target.closest(_this.options.selectors.remove);

            var productID = parseInt(_button.dataset.id);

            _this.emit('loading', true, _button);

            try {
              yield _this.deleteProduct(productID);
            } catch (e) {
              _this.onError(e);
            } finally {
              _this.emit('loading', false, _button);

              _this.emit('updated');
            }
          }

          if (e.target.closest(_this.options.selectors.collapse)) {
            e.preventDefault();
            e.target.closest(_this.options.selectors.collapse).parentNode.classList.toggle(_this.options.classes.collapse);
          }
        });

        return function (_x) {
          return _ref.apply(this, arguments);
        };
      }());

      _defineProperty(this, "clearCompare", /*#__PURE__*/_asyncToGenerator(function* () {
        var result = yield _this.rest.delete();

        if (result.ok) {
          _this.store = result.data;

          _this.emit('cleared', result);
        } else {
          throw result;
        }

        return result;
      }));
    }

    initEvents() {
      super.initEvents();
      this.tables.forEach(table => {
        table.addEventListener('click', this.tableClickHandler);
      });
    }

  }

  var config = {
    active: 'is-active',
    target: '.js-dcaw-dropdown'
  };

  var dropdown = function () {
    var isClose = true;
    var toggle;
    var parent;

    var closeHandler = e => {
      if (!isClose && !parent.contains(e.target)) {
        parent.classList.remove(config.active);
        isClose = true;
        document.removeEventListener('click', closeHandler, true);
      }
    };

    var clickHandler = e => {
      toggle = e.target.closest(config.target);

      if (toggle) {
        e.preventDefault();
        e.stopPropagation();
        parent = toggle.parentNode;

        if (parent.classList.contains(config.active)) {
          parent.classList.remove(config.active);
          isClose = true;
        } else {
          parent.classList.add(config.active);
          isClose = false;
          document.addEventListener('click', closeHandler, true);
        }
      }
    };

    return {
      mount: () => {
        document.addEventListener('click', clickHandler);
      }
    };
  }();

  document.addEventListener('DOMContentLoaded', () => {
    dropdown.mount();

    if (typeof dcawCompare === 'object' && typeof dcawGeneral === 'object') {
      var compare = new Compare(_objectSpread2(_objectSpread2({}, dcawGeneral), dcawCompare));
      compare.mount();
    }

    if (typeof dcawWishlist === 'object' && typeof dcawGeneral === 'object') {
      var wishlist = new Wishlist(_objectSpread2(_objectSpread2({}, dcawGeneral), dcawWishlist));
      wishlist.mount();
    }
  });

}));
//# sourceMappingURL=main.js.map
