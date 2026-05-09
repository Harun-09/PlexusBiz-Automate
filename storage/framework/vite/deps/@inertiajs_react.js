import { r as __toESM, t as __commonJSMin } from "./chunk-CYJPkc-J.js";
import { t as require_react } from "./react.js";
import { v as axios } from "./axios-BGL_sbLC.js";
//#region node_modules/deepmerge/dist/cjs.js
var require_cjs = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var isMergeableObject = function isMergeableObject(value) {
		return isNonNullObject(value) && !isSpecial(value);
	};
	function isNonNullObject(value) {
		return !!value && typeof value === "object";
	}
	function isSpecial(value) {
		var stringValue = Object.prototype.toString.call(value);
		return stringValue === "[object RegExp]" || stringValue === "[object Date]" || isReactElement(value);
	}
	var REACT_ELEMENT_TYPE = typeof Symbol === "function" && Symbol.for ? Symbol.for("react.element") : 60103;
	function isReactElement(value) {
		return value.$$typeof === REACT_ELEMENT_TYPE;
	}
	function emptyTarget(val) {
		return Array.isArray(val) ? [] : {};
	}
	function cloneUnlessOtherwiseSpecified(value, options) {
		return options.clone !== false && options.isMergeableObject(value) ? deepmerge(emptyTarget(value), value, options) : value;
	}
	function defaultArrayMerge(target, source, options) {
		return target.concat(source).map(function(element) {
			return cloneUnlessOtherwiseSpecified(element, options);
		});
	}
	function getMergeFunction(key, options) {
		if (!options.customMerge) return deepmerge;
		var customMerge = options.customMerge(key);
		return typeof customMerge === "function" ? customMerge : deepmerge;
	}
	function getEnumerableOwnPropertySymbols(target) {
		return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(target).filter(function(symbol) {
			return Object.propertyIsEnumerable.call(target, symbol);
		}) : [];
	}
	function getKeys(target) {
		return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target));
	}
	function propertyIsOnObject(object, property) {
		try {
			return property in object;
		} catch (_) {
			return false;
		}
	}
	function propertyIsUnsafe(target, key) {
		return propertyIsOnObject(target, key) && !(Object.hasOwnProperty.call(target, key) && Object.propertyIsEnumerable.call(target, key));
	}
	function mergeObject(target, source, options) {
		var destination = {};
		if (options.isMergeableObject(target)) getKeys(target).forEach(function(key) {
			destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
		});
		getKeys(source).forEach(function(key) {
			if (propertyIsUnsafe(target, key)) return;
			if (propertyIsOnObject(target, key) && options.isMergeableObject(source[key])) destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
			else destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
		});
		return destination;
	}
	function deepmerge(target, source, options) {
		options = options || {};
		options.arrayMerge = options.arrayMerge || defaultArrayMerge;
		options.isMergeableObject = options.isMergeableObject || isMergeableObject;
		options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;
		var sourceIsArray = Array.isArray(source);
		if (!(sourceIsArray === Array.isArray(target))) return cloneUnlessOtherwiseSpecified(source, options);
		else if (sourceIsArray) return options.arrayMerge(target, source, options);
		else return mergeObject(target, source, options);
	}
	deepmerge.all = function deepmergeAll(array, options) {
		if (!Array.isArray(array)) throw new Error("first argument should be an array");
		return array.reduce(function(prev, next) {
			return deepmerge(prev, next, options);
		}, {});
	};
	module.exports = deepmerge;
}));
//#endregion
//#region node_modules/es-errors/type.js
var require_type = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./type')} */
	module.exports = TypeError;
}));
//#endregion
//#region (ignored) node_modules/object-inspect/util.inspect.js
var require_util_inspect = /* @__PURE__ */ __commonJSMin((() => {}));
//#endregion
//#region node_modules/object-inspect/index.js
var require_object_inspect = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var hasMap = typeof Map === "function" && Map.prototype;
	var mapSizeDescriptor = Object.getOwnPropertyDescriptor && hasMap ? Object.getOwnPropertyDescriptor(Map.prototype, "size") : null;
	var mapSize = hasMap && mapSizeDescriptor && typeof mapSizeDescriptor.get === "function" ? mapSizeDescriptor.get : null;
	var mapForEach = hasMap && Map.prototype.forEach;
	var hasSet = typeof Set === "function" && Set.prototype;
	var setSizeDescriptor = Object.getOwnPropertyDescriptor && hasSet ? Object.getOwnPropertyDescriptor(Set.prototype, "size") : null;
	var setSize = hasSet && setSizeDescriptor && typeof setSizeDescriptor.get === "function" ? setSizeDescriptor.get : null;
	var setForEach = hasSet && Set.prototype.forEach;
	var weakMapHas = typeof WeakMap === "function" && WeakMap.prototype ? WeakMap.prototype.has : null;
	var weakSetHas = typeof WeakSet === "function" && WeakSet.prototype ? WeakSet.prototype.has : null;
	var weakRefDeref = typeof WeakRef === "function" && WeakRef.prototype ? WeakRef.prototype.deref : null;
	var booleanValueOf = Boolean.prototype.valueOf;
	var objectToString = Object.prototype.toString;
	var functionToString = Function.prototype.toString;
	var $match = String.prototype.match;
	var $slice = String.prototype.slice;
	var $replace = String.prototype.replace;
	var $toUpperCase = String.prototype.toUpperCase;
	var $toLowerCase = String.prototype.toLowerCase;
	var $test = RegExp.prototype.test;
	var $concat = Array.prototype.concat;
	var $join = Array.prototype.join;
	var $arrSlice = Array.prototype.slice;
	var $floor = Math.floor;
	var bigIntValueOf = typeof BigInt === "function" ? BigInt.prototype.valueOf : null;
	var gOPS = Object.getOwnPropertySymbols;
	var symToString = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? Symbol.prototype.toString : null;
	var hasShammedSymbols = typeof Symbol === "function" && typeof Symbol.iterator === "object";
	var toStringTag = typeof Symbol === "function" && Symbol.toStringTag && (typeof Symbol.toStringTag === hasShammedSymbols ? "object" : "symbol") ? Symbol.toStringTag : null;
	var isEnumerable = Object.prototype.propertyIsEnumerable;
	var gPO = (typeof Reflect === "function" ? Reflect.getPrototypeOf : Object.getPrototypeOf) || ([].__proto__ === Array.prototype ? function(O) {
		return O.__proto__;
	} : null);
	function addNumericSeparator(num, str) {
		if (num === Infinity || num === -Infinity || num !== num || num && num > -1e3 && num < 1e3 || $test.call(/e/, str)) return str;
		var sepRegex = /[0-9](?=(?:[0-9]{3})+(?![0-9]))/g;
		if (typeof num === "number") {
			var int = num < 0 ? -$floor(-num) : $floor(num);
			if (int !== num) {
				var intStr = String(int);
				var dec = $slice.call(str, intStr.length + 1);
				return $replace.call(intStr, sepRegex, "$&_") + "." + $replace.call($replace.call(dec, /([0-9]{3})/g, "$&_"), /_$/, "");
			}
		}
		return $replace.call(str, sepRegex, "$&_");
	}
	var utilInspect = require_util_inspect();
	var inspectCustom = utilInspect.custom;
	var inspectSymbol = isSymbol(inspectCustom) ? inspectCustom : null;
	var quotes = {
		__proto__: null,
		"double": "\"",
		single: "'"
	};
	var quoteREs = {
		__proto__: null,
		"double": /(["\\])/g,
		single: /(['\\])/g
	};
	module.exports = function inspect_(obj, options, depth, seen) {
		var opts = options || {};
		if (has(opts, "quoteStyle") && !has(quotes, opts.quoteStyle)) throw new TypeError("option \"quoteStyle\" must be \"single\" or \"double\"");
		if (has(opts, "maxStringLength") && (typeof opts.maxStringLength === "number" ? opts.maxStringLength < 0 && opts.maxStringLength !== Infinity : opts.maxStringLength !== null)) throw new TypeError("option \"maxStringLength\", if provided, must be a positive integer, Infinity, or `null`");
		var customInspect = has(opts, "customInspect") ? opts.customInspect : true;
		if (typeof customInspect !== "boolean" && customInspect !== "symbol") throw new TypeError("option \"customInspect\", if provided, must be `true`, `false`, or `'symbol'`");
		if (has(opts, "indent") && opts.indent !== null && opts.indent !== "	" && !(parseInt(opts.indent, 10) === opts.indent && opts.indent > 0)) throw new TypeError("option \"indent\" must be \"\\t\", an integer > 0, or `null`");
		if (has(opts, "numericSeparator") && typeof opts.numericSeparator !== "boolean") throw new TypeError("option \"numericSeparator\", if provided, must be `true` or `false`");
		var numericSeparator = opts.numericSeparator;
		if (typeof obj === "undefined") return "undefined";
		if (obj === null) return "null";
		if (typeof obj === "boolean") return obj ? "true" : "false";
		if (typeof obj === "string") return inspectString(obj, opts);
		if (typeof obj === "number") {
			if (obj === 0) return Infinity / obj > 0 ? "0" : "-0";
			var str = String(obj);
			return numericSeparator ? addNumericSeparator(obj, str) : str;
		}
		if (typeof obj === "bigint") {
			var bigIntStr = String(obj) + "n";
			return numericSeparator ? addNumericSeparator(obj, bigIntStr) : bigIntStr;
		}
		var maxDepth = typeof opts.depth === "undefined" ? 5 : opts.depth;
		if (typeof depth === "undefined") depth = 0;
		if (depth >= maxDepth && maxDepth > 0 && typeof obj === "object") return isArray(obj) ? "[Array]" : "[Object]";
		var indent = getIndent(opts, depth);
		if (typeof seen === "undefined") seen = [];
		else if (indexOf(seen, obj) >= 0) return "[Circular]";
		function inspect(value, from, noIndent) {
			if (from) {
				seen = $arrSlice.call(seen);
				seen.push(from);
			}
			if (noIndent) {
				var newOpts = { depth: opts.depth };
				if (has(opts, "quoteStyle")) newOpts.quoteStyle = opts.quoteStyle;
				return inspect_(value, newOpts, depth + 1, seen);
			}
			return inspect_(value, opts, depth + 1, seen);
		}
		if (typeof obj === "function" && !isRegExp(obj)) {
			var name = nameOf(obj);
			var keys = arrObjKeys(obj, inspect);
			return "[Function" + (name ? ": " + name : " (anonymous)") + "]" + (keys.length > 0 ? " { " + $join.call(keys, ", ") + " }" : "");
		}
		if (isSymbol(obj)) {
			var symString = hasShammedSymbols ? $replace.call(String(obj), /^(Symbol\(.*\))_[^)]*$/, "$1") : symToString.call(obj);
			return typeof obj === "object" && !hasShammedSymbols ? markBoxed(symString) : symString;
		}
		if (isElement(obj)) {
			var s = "<" + $toLowerCase.call(String(obj.nodeName));
			var attrs = obj.attributes || [];
			for (var i = 0; i < attrs.length; i++) s += " " + attrs[i].name + "=" + wrapQuotes(quote(attrs[i].value), "double", opts);
			s += ">";
			if (obj.childNodes && obj.childNodes.length) s += "...";
			s += "</" + $toLowerCase.call(String(obj.nodeName)) + ">";
			return s;
		}
		if (isArray(obj)) {
			if (obj.length === 0) return "[]";
			var xs = arrObjKeys(obj, inspect);
			if (indent && !singleLineValues(xs)) return "[" + indentedJoin(xs, indent) + "]";
			return "[ " + $join.call(xs, ", ") + " ]";
		}
		if (isError(obj)) {
			var parts = arrObjKeys(obj, inspect);
			if (!("cause" in Error.prototype) && "cause" in obj && !isEnumerable.call(obj, "cause")) return "{ [" + String(obj) + "] " + $join.call($concat.call("[cause]: " + inspect(obj.cause), parts), ", ") + " }";
			if (parts.length === 0) return "[" + String(obj) + "]";
			return "{ [" + String(obj) + "] " + $join.call(parts, ", ") + " }";
		}
		if (typeof obj === "object" && customInspect) {
			if (inspectSymbol && typeof obj[inspectSymbol] === "function" && utilInspect) return utilInspect(obj, { depth: maxDepth - depth });
			else if (customInspect !== "symbol" && typeof obj.inspect === "function") return obj.inspect();
		}
		if (isMap(obj)) {
			var mapParts = [];
			if (mapForEach) mapForEach.call(obj, function(value, key) {
				mapParts.push(inspect(key, obj, true) + " => " + inspect(value, obj));
			});
			return collectionOf("Map", mapSize.call(obj), mapParts, indent);
		}
		if (isSet(obj)) {
			var setParts = [];
			if (setForEach) setForEach.call(obj, function(value) {
				setParts.push(inspect(value, obj));
			});
			return collectionOf("Set", setSize.call(obj), setParts, indent);
		}
		if (isWeakMap(obj)) return weakCollectionOf("WeakMap");
		if (isWeakSet(obj)) return weakCollectionOf("WeakSet");
		if (isWeakRef(obj)) return weakCollectionOf("WeakRef");
		if (isNumber(obj)) return markBoxed(inspect(Number(obj)));
		if (isBigInt(obj)) return markBoxed(inspect(bigIntValueOf.call(obj)));
		if (isBoolean(obj)) return markBoxed(booleanValueOf.call(obj));
		if (isString(obj)) return markBoxed(inspect(String(obj)));
		if (typeof window !== "undefined" && obj === window) return "{ [object Window] }";
		if (typeof globalThis !== "undefined" && obj === globalThis || typeof global !== "undefined" && obj === global) return "{ [object globalThis] }";
		if (!isDate(obj) && !isRegExp(obj)) {
			var ys = arrObjKeys(obj, inspect);
			var isPlainObject = gPO ? gPO(obj) === Object.prototype : obj instanceof Object || obj.constructor === Object;
			var protoTag = obj instanceof Object ? "" : "null prototype";
			var stringTag = !isPlainObject && toStringTag && Object(obj) === obj && toStringTag in obj ? $slice.call(toStr(obj), 8, -1) : protoTag ? "Object" : "";
			var tag = (isPlainObject || typeof obj.constructor !== "function" ? "" : obj.constructor.name ? obj.constructor.name + " " : "") + (stringTag || protoTag ? "[" + $join.call($concat.call([], stringTag || [], protoTag || []), ": ") + "] " : "");
			if (ys.length === 0) return tag + "{}";
			if (indent) return tag + "{" + indentedJoin(ys, indent) + "}";
			return tag + "{ " + $join.call(ys, ", ") + " }";
		}
		return String(obj);
	};
	function wrapQuotes(s, defaultStyle, opts) {
		var quoteChar = quotes[opts.quoteStyle || defaultStyle];
		return quoteChar + s + quoteChar;
	}
	function quote(s) {
		return $replace.call(String(s), /"/g, "&quot;");
	}
	function canTrustToString(obj) {
		return !toStringTag || !(typeof obj === "object" && (toStringTag in obj || typeof obj[toStringTag] !== "undefined"));
	}
	function isArray(obj) {
		return toStr(obj) === "[object Array]" && canTrustToString(obj);
	}
	function isDate(obj) {
		return toStr(obj) === "[object Date]" && canTrustToString(obj);
	}
	function isRegExp(obj) {
		return toStr(obj) === "[object RegExp]" && canTrustToString(obj);
	}
	function isError(obj) {
		return toStr(obj) === "[object Error]" && canTrustToString(obj);
	}
	function isString(obj) {
		return toStr(obj) === "[object String]" && canTrustToString(obj);
	}
	function isNumber(obj) {
		return toStr(obj) === "[object Number]" && canTrustToString(obj);
	}
	function isBoolean(obj) {
		return toStr(obj) === "[object Boolean]" && canTrustToString(obj);
	}
	function isSymbol(obj) {
		if (hasShammedSymbols) return obj && typeof obj === "object" && obj instanceof Symbol;
		if (typeof obj === "symbol") return true;
		if (!obj || typeof obj !== "object" || !symToString) return false;
		try {
			symToString.call(obj);
			return true;
		} catch (e) {}
		return false;
	}
	function isBigInt(obj) {
		if (!obj || typeof obj !== "object" || !bigIntValueOf) return false;
		try {
			bigIntValueOf.call(obj);
			return true;
		} catch (e) {}
		return false;
	}
	var hasOwn = Object.prototype.hasOwnProperty || function(key) {
		return key in this;
	};
	function has(obj, key) {
		return hasOwn.call(obj, key);
	}
	function toStr(obj) {
		return objectToString.call(obj);
	}
	function nameOf(f) {
		if (f.name) return f.name;
		var m = $match.call(functionToString.call(f), /^function\s*([\w$]+)/);
		if (m) return m[1];
		return null;
	}
	function indexOf(xs, x) {
		if (xs.indexOf) return xs.indexOf(x);
		for (var i = 0, l = xs.length; i < l; i++) if (xs[i] === x) return i;
		return -1;
	}
	function isMap(x) {
		if (!mapSize || !x || typeof x !== "object") return false;
		try {
			mapSize.call(x);
			try {
				setSize.call(x);
			} catch (s) {
				return true;
			}
			return x instanceof Map;
		} catch (e) {}
		return false;
	}
	function isWeakMap(x) {
		if (!weakMapHas || !x || typeof x !== "object") return false;
		try {
			weakMapHas.call(x, weakMapHas);
			try {
				weakSetHas.call(x, weakSetHas);
			} catch (s) {
				return true;
			}
			return x instanceof WeakMap;
		} catch (e) {}
		return false;
	}
	function isWeakRef(x) {
		if (!weakRefDeref || !x || typeof x !== "object") return false;
		try {
			weakRefDeref.call(x);
			return true;
		} catch (e) {}
		return false;
	}
	function isSet(x) {
		if (!setSize || !x || typeof x !== "object") return false;
		try {
			setSize.call(x);
			try {
				mapSize.call(x);
			} catch (m) {
				return true;
			}
			return x instanceof Set;
		} catch (e) {}
		return false;
	}
	function isWeakSet(x) {
		if (!weakSetHas || !x || typeof x !== "object") return false;
		try {
			weakSetHas.call(x, weakSetHas);
			try {
				weakMapHas.call(x, weakMapHas);
			} catch (s) {
				return true;
			}
			return x instanceof WeakSet;
		} catch (e) {}
		return false;
	}
	function isElement(x) {
		if (!x || typeof x !== "object") return false;
		if (typeof HTMLElement !== "undefined" && x instanceof HTMLElement) return true;
		return typeof x.nodeName === "string" && typeof x.getAttribute === "function";
	}
	function inspectString(str, opts) {
		if (str.length > opts.maxStringLength) {
			var remaining = str.length - opts.maxStringLength;
			var trailer = "... " + remaining + " more character" + (remaining > 1 ? "s" : "");
			return inspectString($slice.call(str, 0, opts.maxStringLength), opts) + trailer;
		}
		var quoteRE = quoteREs[opts.quoteStyle || "single"];
		quoteRE.lastIndex = 0;
		return wrapQuotes($replace.call($replace.call(str, quoteRE, "\\$1"), /[\x00-\x1f]/g, lowbyte), "single", opts);
	}
	function lowbyte(c) {
		var n = c.charCodeAt(0);
		var x = {
			8: "b",
			9: "t",
			10: "n",
			12: "f",
			13: "r"
		}[n];
		if (x) return "\\" + x;
		return "\\x" + (n < 16 ? "0" : "") + $toUpperCase.call(n.toString(16));
	}
	function markBoxed(str) {
		return "Object(" + str + ")";
	}
	function weakCollectionOf(type) {
		return type + " { ? }";
	}
	function collectionOf(type, size, entries, indent) {
		var joinedEntries = indent ? indentedJoin(entries, indent) : $join.call(entries, ", ");
		return type + " (" + size + ") {" + joinedEntries + "}";
	}
	function singleLineValues(xs) {
		for (var i = 0; i < xs.length; i++) if (indexOf(xs[i], "\n") >= 0) return false;
		return true;
	}
	function getIndent(opts, depth) {
		var baseIndent;
		if (opts.indent === "	") baseIndent = "	";
		else if (typeof opts.indent === "number" && opts.indent > 0) baseIndent = $join.call(Array(opts.indent + 1), " ");
		else return null;
		return {
			base: baseIndent,
			prev: $join.call(Array(depth + 1), baseIndent)
		};
	}
	function indentedJoin(xs, indent) {
		if (xs.length === 0) return "";
		var lineJoiner = "\n" + indent.prev + indent.base;
		return lineJoiner + $join.call(xs, "," + lineJoiner) + "\n" + indent.prev;
	}
	function arrObjKeys(obj, inspect) {
		var isArr = isArray(obj);
		var xs = [];
		if (isArr) {
			xs.length = obj.length;
			for (var i = 0; i < obj.length; i++) xs[i] = has(obj, i) ? inspect(obj[i], obj) : "";
		}
		var syms = typeof gOPS === "function" ? gOPS(obj) : [];
		var symMap;
		if (hasShammedSymbols) {
			symMap = {};
			for (var k = 0; k < syms.length; k++) symMap["$" + syms[k]] = syms[k];
		}
		for (var key in obj) {
			if (!has(obj, key)) continue;
			if (isArr && String(Number(key)) === key && key < obj.length) continue;
			if (hasShammedSymbols && symMap["$" + key] instanceof Symbol) continue;
			else if ($test.call(/[^\w$]/, key)) xs.push(inspect(key, obj) + ": " + inspect(obj[key], obj));
			else xs.push(key + ": " + inspect(obj[key], obj));
		}
		if (typeof gOPS === "function") {
			for (var j = 0; j < syms.length; j++) if (isEnumerable.call(obj, syms[j])) xs.push("[" + inspect(syms[j]) + "]: " + inspect(obj[syms[j]], obj));
		}
		return xs;
	}
}));
//#endregion
//#region node_modules/side-channel-list/index.js
var require_side_channel_list = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var inspect = require_object_inspect();
	var $TypeError = require_type();
	/** @type {import('./list.d.ts').listGetNode} */
	var listGetNode = function(list, key, isDelete) {
		/** @type {typeof list | NonNullable<(typeof list)['next']>} */
		var prev = list;
		/** @type {(typeof list)['next']} */
		var curr;
		for (; (curr = prev.next) != null; prev = curr) if (curr.key === key) {
			prev.next = curr.next;
			if (!isDelete) {
				curr.next = list.next;
				list.next = curr;
			}
			return curr;
		}
	};
	/** @type {import('./list.d.ts').listGet} */
	var listGet = function(objects, key) {
		if (!objects) return;
		var node = listGetNode(objects, key);
		return node && node.value;
	};
	/** @type {import('./list.d.ts').listSet} */
	var listSet = function(objects, key, value) {
		var node = listGetNode(objects, key);
		if (node) node.value = value;
		else objects.next = {
			key,
			next: objects.next,
			value
		};
	};
	/** @type {import('./list.d.ts').listHas} */
	var listHas = function(objects, key) {
		if (!objects) return false;
		return !!listGetNode(objects, key);
	};
	/** @type {import('./list.d.ts').listDelete} */
	var listDelete = function(objects, key) {
		if (objects) return listGetNode(objects, key, true);
	};
	/** @type {import('.')} */
	module.exports = function getSideChannelList() {
		/** @typedef {ReturnType<typeof getSideChannelList>} Channel */
		/** @typedef {Parameters<Channel['get']>[0]} K */
		/** @typedef {Parameters<Channel['set']>[1]} V */
		/** @type {import('./list.d.ts').RootNode<V, K> | undefined} */ var $o;
		/** @type {Channel} */
		var channel = {
			assert: function(key) {
				if (!channel.has(key)) throw new $TypeError("Side channel does not contain " + inspect(key));
			},
			"delete": function(key) {
				var deletedNode = listDelete($o, key);
				if (deletedNode && $o && !$o.next) $o = void 0;
				return !!deletedNode;
			},
			get: function(key) {
				return listGet($o, key);
			},
			has: function(key) {
				return listHas($o, key);
			},
			set: function(key, value) {
				if (!$o) $o = { next: void 0 };
				listSet($o, key, value);
			}
		};
		return channel;
	};
}));
//#endregion
//#region node_modules/es-object-atoms/index.js
var require_es_object_atoms = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('.')} */
	module.exports = Object;
}));
//#endregion
//#region node_modules/es-errors/index.js
var require_es_errors = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('.')} */
	module.exports = Error;
}));
//#endregion
//#region node_modules/es-errors/eval.js
var require_eval = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./eval')} */
	module.exports = EvalError;
}));
//#endregion
//#region node_modules/es-errors/range.js
var require_range = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./range')} */
	module.exports = RangeError;
}));
//#endregion
//#region node_modules/es-errors/ref.js
var require_ref = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./ref')} */
	module.exports = ReferenceError;
}));
//#endregion
//#region node_modules/es-errors/syntax.js
var require_syntax = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./syntax')} */
	module.exports = SyntaxError;
}));
//#endregion
//#region node_modules/es-errors/uri.js
var require_uri = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./uri')} */
	module.exports = URIError;
}));
//#endregion
//#region node_modules/math-intrinsics/abs.js
var require_abs = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./abs')} */
	module.exports = Math.abs;
}));
//#endregion
//#region node_modules/math-intrinsics/floor.js
var require_floor = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./floor')} */
	module.exports = Math.floor;
}));
//#endregion
//#region node_modules/math-intrinsics/max.js
var require_max = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./max')} */
	module.exports = Math.max;
}));
//#endregion
//#region node_modules/math-intrinsics/min.js
var require_min = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./min')} */
	module.exports = Math.min;
}));
//#endregion
//#region node_modules/math-intrinsics/pow.js
var require_pow = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./pow')} */
	module.exports = Math.pow;
}));
//#endregion
//#region node_modules/math-intrinsics/round.js
var require_round = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./round')} */
	module.exports = Math.round;
}));
//#endregion
//#region node_modules/math-intrinsics/isNaN.js
var require_isNaN = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./isNaN')} */
	module.exports = Number.isNaN || function isNaN(a) {
		return a !== a;
	};
}));
//#endregion
//#region node_modules/math-intrinsics/sign.js
var require_sign = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var $isNaN = require_isNaN();
	/** @type {import('./sign')} */
	module.exports = function sign(number) {
		if ($isNaN(number) || number === 0) return number;
		return number < 0 ? -1 : 1;
	};
}));
//#endregion
//#region node_modules/gopd/gOPD.js
var require_gOPD = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./gOPD')} */
	module.exports = Object.getOwnPropertyDescriptor;
}));
//#endregion
//#region node_modules/gopd/index.js
var require_gopd = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('.')} */
	var $gOPD = require_gOPD();
	if ($gOPD) try {
		$gOPD([], "length");
	} catch (e) {
		$gOPD = null;
	}
	module.exports = $gOPD;
}));
//#endregion
//#region node_modules/es-define-property/index.js
var require_es_define_property = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('.')} */
	var $defineProperty = Object.defineProperty || false;
	if ($defineProperty) try {
		$defineProperty({}, "a", { value: 1 });
	} catch (e) {
		$defineProperty = false;
	}
	module.exports = $defineProperty;
}));
//#endregion
//#region node_modules/has-symbols/shams.js
var require_shams = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./shams')} */
	module.exports = function hasSymbols() {
		if (typeof Symbol !== "function" || typeof Object.getOwnPropertySymbols !== "function") return false;
		if (typeof Symbol.iterator === "symbol") return true;
		/** @type {{ [k in symbol]?: unknown }} */
		var obj = {};
		var sym = Symbol("test");
		var symObj = Object(sym);
		if (typeof sym === "string") return false;
		if (Object.prototype.toString.call(sym) !== "[object Symbol]") return false;
		if (Object.prototype.toString.call(symObj) !== "[object Symbol]") return false;
		var symVal = 42;
		obj[sym] = symVal;
		for (var _ in obj) return false;
		if (typeof Object.keys === "function" && Object.keys(obj).length !== 0) return false;
		if (typeof Object.getOwnPropertyNames === "function" && Object.getOwnPropertyNames(obj).length !== 0) return false;
		var syms = Object.getOwnPropertySymbols(obj);
		if (syms.length !== 1 || syms[0] !== sym) return false;
		if (!Object.prototype.propertyIsEnumerable.call(obj, sym)) return false;
		if (typeof Object.getOwnPropertyDescriptor === "function") {
			var descriptor = Object.getOwnPropertyDescriptor(obj, sym);
			if (descriptor.value !== symVal || descriptor.enumerable !== true) return false;
		}
		return true;
	};
}));
//#endregion
//#region node_modules/has-symbols/index.js
var require_has_symbols = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var origSymbol = typeof Symbol !== "undefined" && Symbol;
	var hasSymbolSham = require_shams();
	/** @type {import('.')} */
	module.exports = function hasNativeSymbols() {
		if (typeof origSymbol !== "function") return false;
		if (typeof Symbol !== "function") return false;
		if (typeof origSymbol("foo") !== "symbol") return false;
		if (typeof Symbol("bar") !== "symbol") return false;
		return hasSymbolSham();
	};
}));
//#endregion
//#region node_modules/get-proto/Reflect.getPrototypeOf.js
var require_Reflect_getPrototypeOf = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./Reflect.getPrototypeOf')} */
	module.exports = typeof Reflect !== "undefined" && Reflect.getPrototypeOf || null;
}));
//#endregion
//#region node_modules/get-proto/Object.getPrototypeOf.js
var require_Object_getPrototypeOf = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./Object.getPrototypeOf')} */
	module.exports = require_es_object_atoms().getPrototypeOf || null;
}));
//#endregion
//#region node_modules/function-bind/implementation.js
var require_implementation = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var ERROR_MESSAGE = "Function.prototype.bind called on incompatible ";
	var toStr = Object.prototype.toString;
	var max = Math.max;
	var funcType = "[object Function]";
	var concatty = function concatty(a, b) {
		var arr = [];
		for (var i = 0; i < a.length; i += 1) arr[i] = a[i];
		for (var j = 0; j < b.length; j += 1) arr[j + a.length] = b[j];
		return arr;
	};
	var slicy = function slicy(arrLike, offset) {
		var arr = [];
		for (var i = offset || 0, j = 0; i < arrLike.length; i += 1, j += 1) arr[j] = arrLike[i];
		return arr;
	};
	var joiny = function(arr, joiner) {
		var str = "";
		for (var i = 0; i < arr.length; i += 1) {
			str += arr[i];
			if (i + 1 < arr.length) str += joiner;
		}
		return str;
	};
	module.exports = function bind(that) {
		var target = this;
		if (typeof target !== "function" || toStr.apply(target) !== funcType) throw new TypeError(ERROR_MESSAGE + target);
		var args = slicy(arguments, 1);
		var bound;
		var binder = function() {
			if (this instanceof bound) {
				var result = target.apply(this, concatty(args, arguments));
				if (Object(result) === result) return result;
				return this;
			}
			return target.apply(that, concatty(args, arguments));
		};
		var boundLength = max(0, target.length - args.length);
		var boundArgs = [];
		for (var i = 0; i < boundLength; i++) boundArgs[i] = "$" + i;
		bound = Function("binder", "return function (" + joiny(boundArgs, ",") + "){ return binder.apply(this,arguments); }")(binder);
		if (target.prototype) {
			var Empty = function Empty() {};
			Empty.prototype = target.prototype;
			bound.prototype = new Empty();
			Empty.prototype = null;
		}
		return bound;
	};
}));
//#endregion
//#region node_modules/function-bind/index.js
var require_function_bind = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var implementation = require_implementation();
	module.exports = Function.prototype.bind || implementation;
}));
//#endregion
//#region node_modules/call-bind-apply-helpers/functionCall.js
var require_functionCall = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./functionCall')} */
	module.exports = Function.prototype.call;
}));
//#endregion
//#region node_modules/call-bind-apply-helpers/functionApply.js
var require_functionApply = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./functionApply')} */
	module.exports = Function.prototype.apply;
}));
//#endregion
//#region node_modules/call-bind-apply-helpers/reflectApply.js
var require_reflectApply = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/** @type {import('./reflectApply')} */
	module.exports = typeof Reflect !== "undefined" && Reflect && Reflect.apply;
}));
//#endregion
//#region node_modules/call-bind-apply-helpers/actualApply.js
var require_actualApply = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var bind = require_function_bind();
	var $apply = require_functionApply();
	var $call = require_functionCall();
	/** @type {import('./actualApply')} */
	module.exports = require_reflectApply() || bind.call($call, $apply);
}));
//#endregion
//#region node_modules/call-bind-apply-helpers/index.js
var require_call_bind_apply_helpers = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var bind = require_function_bind();
	var $TypeError = require_type();
	var $call = require_functionCall();
	var $actualApply = require_actualApply();
	/** @type {(args: [Function, thisArg?: unknown, ...args: unknown[]]) => Function} TODO FIXME, find a way to use import('.') */
	module.exports = function callBindBasic(args) {
		if (args.length < 1 || typeof args[0] !== "function") throw new $TypeError("a function is required");
		return $actualApply(bind, $call, args);
	};
}));
//#endregion
//#region node_modules/dunder-proto/get.js
var require_get = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var callBind = require_call_bind_apply_helpers();
	var gOPD = require_gopd();
	var hasProtoAccessor;
	try {
		hasProtoAccessor = [].__proto__ === Array.prototype;
	} catch (e) {
		if (!e || typeof e !== "object" || !("code" in e) || e.code !== "ERR_PROTO_ACCESS") throw e;
	}
	var desc = !!hasProtoAccessor && gOPD && gOPD(Object.prototype, "__proto__");
	var $Object = Object;
	var $getPrototypeOf = $Object.getPrototypeOf;
	/** @type {import('./get')} */
	module.exports = desc && typeof desc.get === "function" ? callBind([desc.get]) : typeof $getPrototypeOf === "function" ? function getDunder(value) {
		return $getPrototypeOf(value == null ? value : $Object(value));
	} : false;
}));
//#endregion
//#region node_modules/get-proto/index.js
var require_get_proto = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var reflectGetProto = require_Reflect_getPrototypeOf();
	var originalGetProto = require_Object_getPrototypeOf();
	var getDunderProto = require_get();
	/** @type {import('.')} */
	module.exports = reflectGetProto ? function getProto(O) {
		return reflectGetProto(O);
	} : originalGetProto ? function getProto(O) {
		if (!O || typeof O !== "object" && typeof O !== "function") throw new TypeError("getProto: not an object");
		return originalGetProto(O);
	} : getDunderProto ? function getProto(O) {
		return getDunderProto(O);
	} : null;
}));
//#endregion
//#region node_modules/hasown/index.js
var require_hasown = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var call = Function.prototype.call;
	var $hasOwn = Object.prototype.hasOwnProperty;
	/** @type {import('.')} */
	module.exports = require_function_bind().call(call, $hasOwn);
}));
//#endregion
//#region node_modules/get-intrinsic/index.js
var require_get_intrinsic = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var undefined;
	var $Object = require_es_object_atoms();
	var $Error = require_es_errors();
	var $EvalError = require_eval();
	var $RangeError = require_range();
	var $ReferenceError = require_ref();
	var $SyntaxError = require_syntax();
	var $TypeError = require_type();
	var $URIError = require_uri();
	var abs = require_abs();
	var floor = require_floor();
	var max = require_max();
	var min = require_min();
	var pow = require_pow();
	var round = require_round();
	var sign = require_sign();
	var $Function = Function;
	var getEvalledConstructor = function(expressionSyntax) {
		try {
			return $Function("\"use strict\"; return (" + expressionSyntax + ").constructor;")();
		} catch (e) {}
	};
	var $gOPD = require_gopd();
	var $defineProperty = require_es_define_property();
	var throwTypeError = function() {
		throw new $TypeError();
	};
	var ThrowTypeError = $gOPD ? function() {
		try {
			arguments.callee;
			return throwTypeError;
		} catch (calleeThrows) {
			try {
				return $gOPD(arguments, "callee").get;
			} catch (gOPDthrows) {
				return throwTypeError;
			}
		}
	}() : throwTypeError;
	var hasSymbols = require_has_symbols()();
	var getProto = require_get_proto();
	var $ObjectGPO = require_Object_getPrototypeOf();
	var $ReflectGPO = require_Reflect_getPrototypeOf();
	var $apply = require_functionApply();
	var $call = require_functionCall();
	var needsEval = {};
	var TypedArray = typeof Uint8Array === "undefined" || !getProto ? undefined : getProto(Uint8Array);
	var INTRINSICS = {
		__proto__: null,
		"%AggregateError%": typeof AggregateError === "undefined" ? undefined : AggregateError,
		"%Array%": Array,
		"%ArrayBuffer%": typeof ArrayBuffer === "undefined" ? undefined : ArrayBuffer,
		"%ArrayIteratorPrototype%": hasSymbols && getProto ? getProto([][Symbol.iterator]()) : undefined,
		"%AsyncFromSyncIteratorPrototype%": undefined,
		"%AsyncFunction%": needsEval,
		"%AsyncGenerator%": needsEval,
		"%AsyncGeneratorFunction%": needsEval,
		"%AsyncIteratorPrototype%": needsEval,
		"%Atomics%": typeof Atomics === "undefined" ? undefined : Atomics,
		"%BigInt%": typeof BigInt === "undefined" ? undefined : BigInt,
		"%BigInt64Array%": typeof BigInt64Array === "undefined" ? undefined : BigInt64Array,
		"%BigUint64Array%": typeof BigUint64Array === "undefined" ? undefined : BigUint64Array,
		"%Boolean%": Boolean,
		"%DataView%": typeof DataView === "undefined" ? undefined : DataView,
		"%Date%": Date,
		"%decodeURI%": decodeURI,
		"%decodeURIComponent%": decodeURIComponent,
		"%encodeURI%": encodeURI,
		"%encodeURIComponent%": encodeURIComponent,
		"%Error%": $Error,
		"%eval%": eval,
		"%EvalError%": $EvalError,
		"%Float16Array%": typeof Float16Array === "undefined" ? undefined : Float16Array,
		"%Float32Array%": typeof Float32Array === "undefined" ? undefined : Float32Array,
		"%Float64Array%": typeof Float64Array === "undefined" ? undefined : Float64Array,
		"%FinalizationRegistry%": typeof FinalizationRegistry === "undefined" ? undefined : FinalizationRegistry,
		"%Function%": $Function,
		"%GeneratorFunction%": needsEval,
		"%Int8Array%": typeof Int8Array === "undefined" ? undefined : Int8Array,
		"%Int16Array%": typeof Int16Array === "undefined" ? undefined : Int16Array,
		"%Int32Array%": typeof Int32Array === "undefined" ? undefined : Int32Array,
		"%isFinite%": isFinite,
		"%isNaN%": isNaN,
		"%IteratorPrototype%": hasSymbols && getProto ? getProto(getProto([][Symbol.iterator]())) : undefined,
		"%JSON%": typeof JSON === "object" ? JSON : undefined,
		"%Map%": typeof Map === "undefined" ? undefined : Map,
		"%MapIteratorPrototype%": typeof Map === "undefined" || !hasSymbols || !getProto ? undefined : getProto((/* @__PURE__ */ new Map())[Symbol.iterator]()),
		"%Math%": Math,
		"%Number%": Number,
		"%Object%": $Object,
		"%Object.getOwnPropertyDescriptor%": $gOPD,
		"%parseFloat%": parseFloat,
		"%parseInt%": parseInt,
		"%Promise%": typeof Promise === "undefined" ? undefined : Promise,
		"%Proxy%": typeof Proxy === "undefined" ? undefined : Proxy,
		"%RangeError%": $RangeError,
		"%ReferenceError%": $ReferenceError,
		"%Reflect%": typeof Reflect === "undefined" ? undefined : Reflect,
		"%RegExp%": RegExp,
		"%Set%": typeof Set === "undefined" ? undefined : Set,
		"%SetIteratorPrototype%": typeof Set === "undefined" || !hasSymbols || !getProto ? undefined : getProto((/* @__PURE__ */ new Set())[Symbol.iterator]()),
		"%SharedArrayBuffer%": typeof SharedArrayBuffer === "undefined" ? undefined : SharedArrayBuffer,
		"%String%": String,
		"%StringIteratorPrototype%": hasSymbols && getProto ? getProto(""[Symbol.iterator]()) : undefined,
		"%Symbol%": hasSymbols ? Symbol : undefined,
		"%SyntaxError%": $SyntaxError,
		"%ThrowTypeError%": ThrowTypeError,
		"%TypedArray%": TypedArray,
		"%TypeError%": $TypeError,
		"%Uint8Array%": typeof Uint8Array === "undefined" ? undefined : Uint8Array,
		"%Uint8ClampedArray%": typeof Uint8ClampedArray === "undefined" ? undefined : Uint8ClampedArray,
		"%Uint16Array%": typeof Uint16Array === "undefined" ? undefined : Uint16Array,
		"%Uint32Array%": typeof Uint32Array === "undefined" ? undefined : Uint32Array,
		"%URIError%": $URIError,
		"%WeakMap%": typeof WeakMap === "undefined" ? undefined : WeakMap,
		"%WeakRef%": typeof WeakRef === "undefined" ? undefined : WeakRef,
		"%WeakSet%": typeof WeakSet === "undefined" ? undefined : WeakSet,
		"%Function.prototype.call%": $call,
		"%Function.prototype.apply%": $apply,
		"%Object.defineProperty%": $defineProperty,
		"%Object.getPrototypeOf%": $ObjectGPO,
		"%Math.abs%": abs,
		"%Math.floor%": floor,
		"%Math.max%": max,
		"%Math.min%": min,
		"%Math.pow%": pow,
		"%Math.round%": round,
		"%Math.sign%": sign,
		"%Reflect.getPrototypeOf%": $ReflectGPO
	};
	if (getProto) try {
		null.error;
	} catch (e) {
		INTRINSICS["%Error.prototype%"] = getProto(getProto(e));
	}
	var doEval = function doEval(name) {
		var value;
		if (name === "%AsyncFunction%") value = getEvalledConstructor("async function () {}");
		else if (name === "%GeneratorFunction%") value = getEvalledConstructor("function* () {}");
		else if (name === "%AsyncGeneratorFunction%") value = getEvalledConstructor("async function* () {}");
		else if (name === "%AsyncGenerator%") {
			var fn = doEval("%AsyncGeneratorFunction%");
			if (fn) value = fn.prototype;
		} else if (name === "%AsyncIteratorPrototype%") {
			var gen = doEval("%AsyncGenerator%");
			if (gen && getProto) value = getProto(gen.prototype);
		}
		INTRINSICS[name] = value;
		return value;
	};
	var LEGACY_ALIASES = {
		__proto__: null,
		"%ArrayBufferPrototype%": ["ArrayBuffer", "prototype"],
		"%ArrayPrototype%": ["Array", "prototype"],
		"%ArrayProto_entries%": [
			"Array",
			"prototype",
			"entries"
		],
		"%ArrayProto_forEach%": [
			"Array",
			"prototype",
			"forEach"
		],
		"%ArrayProto_keys%": [
			"Array",
			"prototype",
			"keys"
		],
		"%ArrayProto_values%": [
			"Array",
			"prototype",
			"values"
		],
		"%AsyncFunctionPrototype%": ["AsyncFunction", "prototype"],
		"%AsyncGenerator%": ["AsyncGeneratorFunction", "prototype"],
		"%AsyncGeneratorPrototype%": [
			"AsyncGeneratorFunction",
			"prototype",
			"prototype"
		],
		"%BooleanPrototype%": ["Boolean", "prototype"],
		"%DataViewPrototype%": ["DataView", "prototype"],
		"%DatePrototype%": ["Date", "prototype"],
		"%ErrorPrototype%": ["Error", "prototype"],
		"%EvalErrorPrototype%": ["EvalError", "prototype"],
		"%Float32ArrayPrototype%": ["Float32Array", "prototype"],
		"%Float64ArrayPrototype%": ["Float64Array", "prototype"],
		"%FunctionPrototype%": ["Function", "prototype"],
		"%Generator%": ["GeneratorFunction", "prototype"],
		"%GeneratorPrototype%": [
			"GeneratorFunction",
			"prototype",
			"prototype"
		],
		"%Int8ArrayPrototype%": ["Int8Array", "prototype"],
		"%Int16ArrayPrototype%": ["Int16Array", "prototype"],
		"%Int32ArrayPrototype%": ["Int32Array", "prototype"],
		"%JSONParse%": ["JSON", "parse"],
		"%JSONStringify%": ["JSON", "stringify"],
		"%MapPrototype%": ["Map", "prototype"],
		"%NumberPrototype%": ["Number", "prototype"],
		"%ObjectPrototype%": ["Object", "prototype"],
		"%ObjProto_toString%": [
			"Object",
			"prototype",
			"toString"
		],
		"%ObjProto_valueOf%": [
			"Object",
			"prototype",
			"valueOf"
		],
		"%PromisePrototype%": ["Promise", "prototype"],
		"%PromiseProto_then%": [
			"Promise",
			"prototype",
			"then"
		],
		"%Promise_all%": ["Promise", "all"],
		"%Promise_reject%": ["Promise", "reject"],
		"%Promise_resolve%": ["Promise", "resolve"],
		"%RangeErrorPrototype%": ["RangeError", "prototype"],
		"%ReferenceErrorPrototype%": ["ReferenceError", "prototype"],
		"%RegExpPrototype%": ["RegExp", "prototype"],
		"%SetPrototype%": ["Set", "prototype"],
		"%SharedArrayBufferPrototype%": ["SharedArrayBuffer", "prototype"],
		"%StringPrototype%": ["String", "prototype"],
		"%SymbolPrototype%": ["Symbol", "prototype"],
		"%SyntaxErrorPrototype%": ["SyntaxError", "prototype"],
		"%TypedArrayPrototype%": ["TypedArray", "prototype"],
		"%TypeErrorPrototype%": ["TypeError", "prototype"],
		"%Uint8ArrayPrototype%": ["Uint8Array", "prototype"],
		"%Uint8ClampedArrayPrototype%": ["Uint8ClampedArray", "prototype"],
		"%Uint16ArrayPrototype%": ["Uint16Array", "prototype"],
		"%Uint32ArrayPrototype%": ["Uint32Array", "prototype"],
		"%URIErrorPrototype%": ["URIError", "prototype"],
		"%WeakMapPrototype%": ["WeakMap", "prototype"],
		"%WeakSetPrototype%": ["WeakSet", "prototype"]
	};
	var bind = require_function_bind();
	var hasOwn = require_hasown();
	var $concat = bind.call($call, Array.prototype.concat);
	var $spliceApply = bind.call($apply, Array.prototype.splice);
	var $replace = bind.call($call, String.prototype.replace);
	var $strSlice = bind.call($call, String.prototype.slice);
	var $exec = bind.call($call, RegExp.prototype.exec);
	var rePropName = /[^%.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|%$))/g;
	var reEscapeChar = /\\(\\)?/g;
	var stringToPath = function stringToPath(string) {
		var first = $strSlice(string, 0, 1);
		var last = $strSlice(string, -1);
		if (first === "%" && last !== "%") throw new $SyntaxError("invalid intrinsic syntax, expected closing `%`");
		else if (last === "%" && first !== "%") throw new $SyntaxError("invalid intrinsic syntax, expected opening `%`");
		var result = [];
		$replace(string, rePropName, function(match, number, quote, subString) {
			result[result.length] = quote ? $replace(subString, reEscapeChar, "$1") : number || match;
		});
		return result;
	};
	var getBaseIntrinsic = function getBaseIntrinsic(name, allowMissing) {
		var intrinsicName = name;
		var alias;
		if (hasOwn(LEGACY_ALIASES, intrinsicName)) {
			alias = LEGACY_ALIASES[intrinsicName];
			intrinsicName = "%" + alias[0] + "%";
		}
		if (hasOwn(INTRINSICS, intrinsicName)) {
			var value = INTRINSICS[intrinsicName];
			if (value === needsEval) value = doEval(intrinsicName);
			if (typeof value === "undefined" && !allowMissing) throw new $TypeError("intrinsic " + name + " exists, but is not available. Please file an issue!");
			return {
				alias,
				name: intrinsicName,
				value
			};
		}
		throw new $SyntaxError("intrinsic " + name + " does not exist!");
	};
	module.exports = function GetIntrinsic(name, allowMissing) {
		if (typeof name !== "string" || name.length === 0) throw new $TypeError("intrinsic name must be a non-empty string");
		if (arguments.length > 1 && typeof allowMissing !== "boolean") throw new $TypeError("\"allowMissing\" argument must be a boolean");
		if ($exec(/^%?[^%]*%?$/, name) === null) throw new $SyntaxError("`%` may not be present anywhere but at the beginning and end of the intrinsic name");
		var parts = stringToPath(name);
		var intrinsicBaseName = parts.length > 0 ? parts[0] : "";
		var intrinsic = getBaseIntrinsic("%" + intrinsicBaseName + "%", allowMissing);
		var intrinsicRealName = intrinsic.name;
		var value = intrinsic.value;
		var skipFurtherCaching = false;
		var alias = intrinsic.alias;
		if (alias) {
			intrinsicBaseName = alias[0];
			$spliceApply(parts, $concat([0, 1], alias));
		}
		for (var i = 1, isOwn = true; i < parts.length; i += 1) {
			var part = parts[i];
			var first = $strSlice(part, 0, 1);
			var last = $strSlice(part, -1);
			if ((first === "\"" || first === "'" || first === "`" || last === "\"" || last === "'" || last === "`") && first !== last) throw new $SyntaxError("property names with quotes must have matching quotes");
			if (part === "constructor" || !isOwn) skipFurtherCaching = true;
			intrinsicBaseName += "." + part;
			intrinsicRealName = "%" + intrinsicBaseName + "%";
			if (hasOwn(INTRINSICS, intrinsicRealName)) value = INTRINSICS[intrinsicRealName];
			else if (value != null) {
				if (!(part in value)) {
					if (!allowMissing) throw new $TypeError("base intrinsic for " + name + " exists, but the property is not available.");
					return;
				}
				if ($gOPD && i + 1 >= parts.length) {
					var desc = $gOPD(value, part);
					isOwn = !!desc;
					if (isOwn && "get" in desc && !("originalValue" in desc.get)) value = desc.get;
					else value = value[part];
				} else {
					isOwn = hasOwn(value, part);
					value = value[part];
				}
				if (isOwn && !skipFurtherCaching) INTRINSICS[intrinsicRealName] = value;
			}
		}
		return value;
	};
}));
//#endregion
//#region node_modules/call-bound/index.js
var require_call_bound = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var GetIntrinsic = require_get_intrinsic();
	var callBindBasic = require_call_bind_apply_helpers();
	/** @type {(thisArg: string, searchString: string, position?: number) => number} */
	var $indexOf = callBindBasic([GetIntrinsic("%String.prototype.indexOf%")]);
	/** @type {import('.')} */
	module.exports = function callBoundIntrinsic(name, allowMissing) {
		var intrinsic = GetIntrinsic(name, !!allowMissing);
		if (typeof intrinsic === "function" && $indexOf(name, ".prototype.") > -1) return callBindBasic([intrinsic]);
		return intrinsic;
	};
}));
//#endregion
//#region node_modules/side-channel-map/index.js
var require_side_channel_map = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var GetIntrinsic = require_get_intrinsic();
	var callBound = require_call_bound();
	var inspect = require_object_inspect();
	var $TypeError = require_type();
	var $Map = GetIntrinsic("%Map%", true);
	/** @type {<K, V>(thisArg: Map<K, V>, key: K) => V} */
	var $mapGet = callBound("Map.prototype.get", true);
	/** @type {<K, V>(thisArg: Map<K, V>, key: K, value: V) => void} */
	var $mapSet = callBound("Map.prototype.set", true);
	/** @type {<K, V>(thisArg: Map<K, V>, key: K) => boolean} */
	var $mapHas = callBound("Map.prototype.has", true);
	/** @type {<K, V>(thisArg: Map<K, V>, key: K) => boolean} */
	var $mapDelete = callBound("Map.prototype.delete", true);
	/** @type {<K, V>(thisArg: Map<K, V>) => number} */
	var $mapSize = callBound("Map.prototype.size", true);
	/** @type {import('.')} */
	module.exports = !!$Map && function getSideChannelMap() {
		/** @typedef {ReturnType<typeof getSideChannelMap>} Channel */
		/** @typedef {Parameters<Channel['get']>[0]} K */
		/** @typedef {Parameters<Channel['set']>[1]} V */
		/** @type {Map<K, V> | undefined} */ var $m;
		/** @type {Channel} */
		var channel = {
			assert: function(key) {
				if (!channel.has(key)) throw new $TypeError("Side channel does not contain " + inspect(key));
			},
			"delete": function(key) {
				if ($m) {
					var result = $mapDelete($m, key);
					if ($mapSize($m) === 0) $m = void 0;
					return result;
				}
				return false;
			},
			get: function(key) {
				if ($m) return $mapGet($m, key);
			},
			has: function(key) {
				if ($m) return $mapHas($m, key);
				return false;
			},
			set: function(key, value) {
				if (!$m) $m = new $Map();
				$mapSet($m, key, value);
			}
		};
		return channel;
	};
}));
//#endregion
//#region node_modules/side-channel-weakmap/index.js
var require_side_channel_weakmap = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var GetIntrinsic = require_get_intrinsic();
	var callBound = require_call_bound();
	var inspect = require_object_inspect();
	var getSideChannelMap = require_side_channel_map();
	var $TypeError = require_type();
	var $WeakMap = GetIntrinsic("%WeakMap%", true);
	/** @type {<K extends object, V>(thisArg: WeakMap<K, V>, key: K) => V} */
	var $weakMapGet = callBound("WeakMap.prototype.get", true);
	/** @type {<K extends object, V>(thisArg: WeakMap<K, V>, key: K, value: V) => void} */
	var $weakMapSet = callBound("WeakMap.prototype.set", true);
	/** @type {<K extends object, V>(thisArg: WeakMap<K, V>, key: K) => boolean} */
	var $weakMapHas = callBound("WeakMap.prototype.has", true);
	/** @type {<K extends object, V>(thisArg: WeakMap<K, V>, key: K) => boolean} */
	var $weakMapDelete = callBound("WeakMap.prototype.delete", true);
	/** @type {import('.')} */
	module.exports = $WeakMap ? function getSideChannelWeakMap() {
		/** @typedef {ReturnType<typeof getSideChannelWeakMap>} Channel */
		/** @typedef {Parameters<Channel['get']>[0]} K */
		/** @typedef {Parameters<Channel['set']>[1]} V */
		/** @type {WeakMap<K & object, V> | undefined} */ var $wm;
		/** @type {Channel | undefined} */ var $m;
		/** @type {Channel} */
		var channel = {
			assert: function(key) {
				if (!channel.has(key)) throw new $TypeError("Side channel does not contain " + inspect(key));
			},
			"delete": function(key) {
				if ($WeakMap && key && (typeof key === "object" || typeof key === "function")) {
					if ($wm) return $weakMapDelete($wm, key);
				} else if (getSideChannelMap) {
					if ($m) return $m["delete"](key);
				}
				return false;
			},
			get: function(key) {
				if ($WeakMap && key && (typeof key === "object" || typeof key === "function")) {
					if ($wm) return $weakMapGet($wm, key);
				}
				return $m && $m.get(key);
			},
			has: function(key) {
				if ($WeakMap && key && (typeof key === "object" || typeof key === "function")) {
					if ($wm) return $weakMapHas($wm, key);
				}
				return !!$m && $m.has(key);
			},
			set: function(key, value) {
				if ($WeakMap && key && (typeof key === "object" || typeof key === "function")) {
					if (!$wm) $wm = new $WeakMap();
					$weakMapSet($wm, key, value);
				} else if (getSideChannelMap) {
					if (!$m) $m = getSideChannelMap();
					/** @type {NonNullable<typeof $m>} */ $m.set(key, value);
				}
			}
		};
		return channel;
	} : getSideChannelMap;
}));
//#endregion
//#region node_modules/side-channel/index.js
var require_side_channel = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var $TypeError = require_type();
	var inspect = require_object_inspect();
	var getSideChannelList = require_side_channel_list();
	var getSideChannelMap = require_side_channel_map();
	var makeChannel = require_side_channel_weakmap() || getSideChannelMap || getSideChannelList;
	/** @type {import('.')} */
	module.exports = function getSideChannel() {
		/** @typedef {ReturnType<typeof getSideChannel>} Channel */
		/** @type {Channel | undefined} */ var $channelData;
		/** @type {Channel} */
		var channel = {
			assert: function(key) {
				if (!channel.has(key)) throw new $TypeError("Side channel does not contain " + inspect(key));
			},
			"delete": function(key) {
				return !!$channelData && $channelData["delete"](key);
			},
			get: function(key) {
				return $channelData && $channelData.get(key);
			},
			has: function(key) {
				return !!$channelData && $channelData.has(key);
			},
			set: function(key, value) {
				if (!$channelData) $channelData = makeChannel();
				$channelData.set(key, value);
			}
		};
		return channel;
	};
}));
//#endregion
//#region node_modules/qs/lib/formats.js
var require_formats = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var replace = String.prototype.replace;
	var percentTwenties = /%20/g;
	var Format = {
		RFC1738: "RFC1738",
		RFC3986: "RFC3986"
	};
	module.exports = {
		"default": Format.RFC3986,
		formatters: {
			RFC1738: function(value) {
				return replace.call(value, percentTwenties, "+");
			},
			RFC3986: function(value) {
				return String(value);
			}
		},
		RFC1738: Format.RFC1738,
		RFC3986: Format.RFC3986
	};
}));
//#endregion
//#region node_modules/qs/lib/utils.js
var require_utils = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var formats = require_formats();
	var getSideChannel = require_side_channel();
	var has = Object.prototype.hasOwnProperty;
	var isArray = Array.isArray;
	var overflowChannel = getSideChannel();
	var markOverflow = function markOverflow(obj, maxIndex) {
		overflowChannel.set(obj, maxIndex);
		return obj;
	};
	var isOverflow = function isOverflow(obj) {
		return overflowChannel.has(obj);
	};
	var getMaxIndex = function getMaxIndex(obj) {
		return overflowChannel.get(obj);
	};
	var setMaxIndex = function setMaxIndex(obj, maxIndex) {
		overflowChannel.set(obj, maxIndex);
	};
	var hexTable = function() {
		var array = [];
		for (var i = 0; i < 256; ++i) array[array.length] = "%" + ((i < 16 ? "0" : "") + i.toString(16)).toUpperCase();
		return array;
	}();
	var compactQueue = function compactQueue(queue) {
		while (queue.length > 1) {
			var item = queue.pop();
			var obj = item.obj[item.prop];
			if (isArray(obj)) {
				var compacted = [];
				for (var j = 0; j < obj.length; ++j) if (typeof obj[j] !== "undefined") compacted[compacted.length] = obj[j];
				item.obj[item.prop] = compacted;
			}
		}
	};
	var arrayToObject = function arrayToObject(source, options) {
		var obj = options && options.plainObjects ? { __proto__: null } : {};
		for (var i = 0; i < source.length; ++i) if (typeof source[i] !== "undefined") obj[i] = source[i];
		return obj;
	};
	var merge = function merge(target, source, options) {
		if (!source) return target;
		if (typeof source !== "object" && typeof source !== "function") {
			if (isArray(target)) {
				var nextIndex = target.length;
				if (options && typeof options.arrayLimit === "number" && nextIndex > options.arrayLimit) return markOverflow(arrayToObject(target.concat(source), options), nextIndex);
				target[nextIndex] = source;
			} else if (target && typeof target === "object") {
				if (isOverflow(target)) {
					var newIndex = getMaxIndex(target) + 1;
					target[newIndex] = source;
					setMaxIndex(target, newIndex);
				} else if (options && options.strictMerge) return [target, source];
				else if (options && (options.plainObjects || options.allowPrototypes) || !has.call(Object.prototype, source)) target[source] = true;
			} else return [target, source];
			return target;
		}
		if (!target || typeof target !== "object") {
			if (isOverflow(source)) {
				var sourceKeys = Object.keys(source);
				var result = options && options.plainObjects ? {
					__proto__: null,
					0: target
				} : { 0: target };
				for (var m = 0; m < sourceKeys.length; m++) {
					var oldKey = parseInt(sourceKeys[m], 10);
					result[oldKey + 1] = source[sourceKeys[m]];
				}
				return markOverflow(result, getMaxIndex(source) + 1);
			}
			var combined = [target].concat(source);
			if (options && typeof options.arrayLimit === "number" && combined.length > options.arrayLimit) return markOverflow(arrayToObject(combined, options), combined.length - 1);
			return combined;
		}
		var mergeTarget = target;
		if (isArray(target) && !isArray(source)) mergeTarget = arrayToObject(target, options);
		if (isArray(target) && isArray(source)) {
			source.forEach(function(item, i) {
				if (has.call(target, i)) {
					var targetItem = target[i];
					if (targetItem && typeof targetItem === "object" && item && typeof item === "object") target[i] = merge(targetItem, item, options);
					else target[target.length] = item;
				} else target[i] = item;
			});
			return target;
		}
		return Object.keys(source).reduce(function(acc, key) {
			var value = source[key];
			if (has.call(acc, key)) acc[key] = merge(acc[key], value, options);
			else acc[key] = value;
			if (isOverflow(source) && !isOverflow(acc)) markOverflow(acc, getMaxIndex(source));
			if (isOverflow(acc)) {
				var keyNum = parseInt(key, 10);
				if (String(keyNum) === key && keyNum >= 0 && keyNum > getMaxIndex(acc)) setMaxIndex(acc, keyNum);
			}
			return acc;
		}, mergeTarget);
	};
	var assign = function assignSingleSource(target, source) {
		return Object.keys(source).reduce(function(acc, key) {
			acc[key] = source[key];
			return acc;
		}, target);
	};
	var decode = function(str, defaultDecoder, charset) {
		var strWithoutPlus = str.replace(/\+/g, " ");
		if (charset === "iso-8859-1") return strWithoutPlus.replace(/%[0-9a-f]{2}/gi, unescape);
		try {
			return decodeURIComponent(strWithoutPlus);
		} catch (e) {
			return strWithoutPlus;
		}
	};
	var limit = 1024;
	module.exports = {
		arrayToObject,
		assign,
		combine: function combine(a, b, arrayLimit, plainObjects) {
			if (isOverflow(a)) {
				var newIndex = getMaxIndex(a) + 1;
				a[newIndex] = b;
				setMaxIndex(a, newIndex);
				return a;
			}
			var result = [].concat(a, b);
			if (result.length > arrayLimit) return markOverflow(arrayToObject(result, { plainObjects }), result.length - 1);
			return result;
		},
		compact: function compact(value) {
			var queue = [{
				obj: { o: value },
				prop: "o"
			}];
			var refs = [];
			for (var i = 0; i < queue.length; ++i) {
				var item = queue[i];
				var obj = item.obj[item.prop];
				var keys = Object.keys(obj);
				for (var j = 0; j < keys.length; ++j) {
					var key = keys[j];
					var val = obj[key];
					if (typeof val === "object" && val !== null && refs.indexOf(val) === -1) {
						queue[queue.length] = {
							obj,
							prop: key
						};
						refs[refs.length] = val;
					}
				}
			}
			compactQueue(queue);
			return value;
		},
		decode,
		encode: function encode(str, defaultEncoder, charset, kind, format) {
			if (str.length === 0) return str;
			var string = str;
			if (typeof str === "symbol") string = Symbol.prototype.toString.call(str);
			else if (typeof str !== "string") string = String(str);
			if (charset === "iso-8859-1") return escape(string).replace(/%u[0-9a-f]{4}/gi, function($0) {
				return "%26%23" + parseInt($0.slice(2), 16) + "%3B";
			});
			var out = "";
			for (var j = 0; j < string.length; j += limit) {
				var segment = string.length >= limit ? string.slice(j, j + limit) : string;
				var arr = [];
				for (var i = 0; i < segment.length; ++i) {
					var c = segment.charCodeAt(i);
					if (c === 45 || c === 46 || c === 95 || c === 126 || c >= 48 && c <= 57 || c >= 65 && c <= 90 || c >= 97 && c <= 122 || format === formats.RFC1738 && (c === 40 || c === 41)) {
						arr[arr.length] = segment.charAt(i);
						continue;
					}
					if (c < 128) {
						arr[arr.length] = hexTable[c];
						continue;
					}
					if (c < 2048) {
						arr[arr.length] = hexTable[192 | c >> 6] + hexTable[128 | c & 63];
						continue;
					}
					if (c < 55296 || c >= 57344) {
						arr[arr.length] = hexTable[224 | c >> 12] + hexTable[128 | c >> 6 & 63] + hexTable[128 | c & 63];
						continue;
					}
					i += 1;
					c = 65536 + ((c & 1023) << 10 | segment.charCodeAt(i) & 1023);
					arr[arr.length] = hexTable[240 | c >> 18] + hexTable[128 | c >> 12 & 63] + hexTable[128 | c >> 6 & 63] + hexTable[128 | c & 63];
				}
				out += arr.join("");
			}
			return out;
		},
		isBuffer: function isBuffer(obj) {
			if (!obj || typeof obj !== "object") return false;
			return !!(obj.constructor && obj.constructor.isBuffer && obj.constructor.isBuffer(obj));
		},
		isOverflow,
		isRegExp: function isRegExp(obj) {
			return Object.prototype.toString.call(obj) === "[object RegExp]";
		},
		markOverflow,
		maybeMap: function maybeMap(val, fn) {
			if (isArray(val)) {
				var mapped = [];
				for (var i = 0; i < val.length; i += 1) mapped[mapped.length] = fn(val[i]);
				return mapped;
			}
			return fn(val);
		},
		merge
	};
}));
//#endregion
//#region node_modules/qs/lib/stringify.js
var require_stringify = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var getSideChannel = require_side_channel();
	var utils = require_utils();
	var formats = require_formats();
	var has = Object.prototype.hasOwnProperty;
	var arrayPrefixGenerators = {
		brackets: function brackets(prefix) {
			return prefix + "[]";
		},
		comma: "comma",
		indices: function indices(prefix, key) {
			return prefix + "[" + key + "]";
		},
		repeat: function repeat(prefix) {
			return prefix;
		}
	};
	var isArray = Array.isArray;
	var push = Array.prototype.push;
	var pushToArray = function(arr, valueOrArray) {
		push.apply(arr, isArray(valueOrArray) ? valueOrArray : [valueOrArray]);
	};
	var toISO = Date.prototype.toISOString;
	var defaultFormat = formats["default"];
	var defaults = {
		addQueryPrefix: false,
		allowDots: false,
		allowEmptyArrays: false,
		arrayFormat: "indices",
		charset: "utf-8",
		charsetSentinel: false,
		commaRoundTrip: false,
		delimiter: "&",
		encode: true,
		encodeDotInKeys: false,
		encoder: utils.encode,
		encodeValuesOnly: false,
		filter: void 0,
		format: defaultFormat,
		formatter: formats.formatters[defaultFormat],
		indices: false,
		serializeDate: function serializeDate(date) {
			return toISO.call(date);
		},
		skipNulls: false,
		strictNullHandling: false
	};
	var isNonNullishPrimitive = function isNonNullishPrimitive(v) {
		return typeof v === "string" || typeof v === "number" || typeof v === "boolean" || typeof v === "symbol" || typeof v === "bigint";
	};
	var sentinel = {};
	var stringify = function stringify(object, prefix, generateArrayPrefix, commaRoundTrip, allowEmptyArrays, strictNullHandling, skipNulls, encodeDotInKeys, encoder, filter, sort, allowDots, serializeDate, format, formatter, encodeValuesOnly, charset, sideChannel) {
		var obj = object;
		var tmpSc = sideChannel;
		var step = 0;
		var findFlag = false;
		while ((tmpSc = tmpSc.get(sentinel)) !== void 0 && !findFlag) {
			var pos = tmpSc.get(object);
			step += 1;
			if (typeof pos !== "undefined") if (pos === step) throw new RangeError("Cyclic object value");
			else findFlag = true;
			if (typeof tmpSc.get(sentinel) === "undefined") step = 0;
		}
		if (typeof filter === "function") obj = filter(prefix, obj);
		else if (obj instanceof Date) obj = serializeDate(obj);
		else if (generateArrayPrefix === "comma" && isArray(obj)) obj = utils.maybeMap(obj, function(value) {
			if (value instanceof Date) return serializeDate(value);
			return value;
		});
		if (obj === null) {
			if (strictNullHandling) return encoder && !encodeValuesOnly ? encoder(prefix, defaults.encoder, charset, "key", format) : prefix;
			obj = "";
		}
		if (isNonNullishPrimitive(obj) || utils.isBuffer(obj)) {
			if (encoder) return [formatter(encodeValuesOnly ? prefix : encoder(prefix, defaults.encoder, charset, "key", format)) + "=" + formatter(encoder(obj, defaults.encoder, charset, "value", format))];
			return [formatter(prefix) + "=" + formatter(String(obj))];
		}
		var values = [];
		if (typeof obj === "undefined") return values;
		var objKeys;
		if (generateArrayPrefix === "comma" && isArray(obj)) {
			if (encodeValuesOnly && encoder) obj = utils.maybeMap(obj, encoder);
			objKeys = [{ value: obj.length > 0 ? obj.join(",") || null : void 0 }];
		} else if (isArray(filter)) objKeys = filter;
		else {
			var keys = Object.keys(obj);
			objKeys = sort ? keys.sort(sort) : keys;
		}
		var encodedPrefix = encodeDotInKeys ? String(prefix).replace(/\./g, "%2E") : String(prefix);
		var adjustedPrefix = commaRoundTrip && isArray(obj) && obj.length === 1 ? encodedPrefix + "[]" : encodedPrefix;
		if (allowEmptyArrays && isArray(obj) && obj.length === 0) return adjustedPrefix + "[]";
		for (var j = 0; j < objKeys.length; ++j) {
			var key = objKeys[j];
			var value = typeof key === "object" && key && typeof key.value !== "undefined" ? key.value : obj[key];
			if (skipNulls && value === null) continue;
			var encodedKey = allowDots && encodeDotInKeys ? String(key).replace(/\./g, "%2E") : String(key);
			var keyPrefix = isArray(obj) ? typeof generateArrayPrefix === "function" ? generateArrayPrefix(adjustedPrefix, encodedKey) : adjustedPrefix : adjustedPrefix + (allowDots ? "." + encodedKey : "[" + encodedKey + "]");
			sideChannel.set(object, step);
			var valueSideChannel = getSideChannel();
			valueSideChannel.set(sentinel, sideChannel);
			pushToArray(values, stringify(value, keyPrefix, generateArrayPrefix, commaRoundTrip, allowEmptyArrays, strictNullHandling, skipNulls, encodeDotInKeys, generateArrayPrefix === "comma" && encodeValuesOnly && isArray(obj) ? null : encoder, filter, sort, allowDots, serializeDate, format, formatter, encodeValuesOnly, charset, valueSideChannel));
		}
		return values;
	};
	var normalizeStringifyOptions = function normalizeStringifyOptions(opts) {
		if (!opts) return defaults;
		if (typeof opts.allowEmptyArrays !== "undefined" && typeof opts.allowEmptyArrays !== "boolean") throw new TypeError("`allowEmptyArrays` option can only be `true` or `false`, when provided");
		if (typeof opts.encodeDotInKeys !== "undefined" && typeof opts.encodeDotInKeys !== "boolean") throw new TypeError("`encodeDotInKeys` option can only be `true` or `false`, when provided");
		if (opts.encoder !== null && typeof opts.encoder !== "undefined" && typeof opts.encoder !== "function") throw new TypeError("Encoder has to be a function.");
		var charset = opts.charset || defaults.charset;
		if (typeof opts.charset !== "undefined" && opts.charset !== "utf-8" && opts.charset !== "iso-8859-1") throw new TypeError("The charset option must be either utf-8, iso-8859-1, or undefined");
		var format = formats["default"];
		if (typeof opts.format !== "undefined") {
			if (!has.call(formats.formatters, opts.format)) throw new TypeError("Unknown format option provided.");
			format = opts.format;
		}
		var formatter = formats.formatters[format];
		var filter = defaults.filter;
		if (typeof opts.filter === "function" || isArray(opts.filter)) filter = opts.filter;
		var arrayFormat;
		if (opts.arrayFormat in arrayPrefixGenerators) arrayFormat = opts.arrayFormat;
		else if ("indices" in opts) arrayFormat = opts.indices ? "indices" : "repeat";
		else arrayFormat = defaults.arrayFormat;
		if ("commaRoundTrip" in opts && typeof opts.commaRoundTrip !== "boolean") throw new TypeError("`commaRoundTrip` must be a boolean, or absent");
		var allowDots = typeof opts.allowDots === "undefined" ? opts.encodeDotInKeys === true ? true : defaults.allowDots : !!opts.allowDots;
		return {
			addQueryPrefix: typeof opts.addQueryPrefix === "boolean" ? opts.addQueryPrefix : defaults.addQueryPrefix,
			allowDots,
			allowEmptyArrays: typeof opts.allowEmptyArrays === "boolean" ? !!opts.allowEmptyArrays : defaults.allowEmptyArrays,
			arrayFormat,
			charset,
			charsetSentinel: typeof opts.charsetSentinel === "boolean" ? opts.charsetSentinel : defaults.charsetSentinel,
			commaRoundTrip: !!opts.commaRoundTrip,
			delimiter: typeof opts.delimiter === "undefined" ? defaults.delimiter : opts.delimiter,
			encode: typeof opts.encode === "boolean" ? opts.encode : defaults.encode,
			encodeDotInKeys: typeof opts.encodeDotInKeys === "boolean" ? opts.encodeDotInKeys : defaults.encodeDotInKeys,
			encoder: typeof opts.encoder === "function" ? opts.encoder : defaults.encoder,
			encodeValuesOnly: typeof opts.encodeValuesOnly === "boolean" ? opts.encodeValuesOnly : defaults.encodeValuesOnly,
			filter,
			format,
			formatter,
			serializeDate: typeof opts.serializeDate === "function" ? opts.serializeDate : defaults.serializeDate,
			skipNulls: typeof opts.skipNulls === "boolean" ? opts.skipNulls : defaults.skipNulls,
			sort: typeof opts.sort === "function" ? opts.sort : null,
			strictNullHandling: typeof opts.strictNullHandling === "boolean" ? opts.strictNullHandling : defaults.strictNullHandling
		};
	};
	module.exports = function(object, opts) {
		var obj = object;
		var options = normalizeStringifyOptions(opts);
		var objKeys;
		var filter;
		if (typeof options.filter === "function") {
			filter = options.filter;
			obj = filter("", obj);
		} else if (isArray(options.filter)) {
			filter = options.filter;
			objKeys = filter;
		}
		var keys = [];
		if (typeof obj !== "object" || obj === null) return "";
		var generateArrayPrefix = arrayPrefixGenerators[options.arrayFormat];
		var commaRoundTrip = generateArrayPrefix === "comma" && options.commaRoundTrip;
		if (!objKeys) objKeys = Object.keys(obj);
		if (options.sort) objKeys.sort(options.sort);
		var sideChannel = getSideChannel();
		for (var i = 0; i < objKeys.length; ++i) {
			var key = objKeys[i];
			var value = obj[key];
			if (options.skipNulls && value === null) continue;
			pushToArray(keys, stringify(value, key, generateArrayPrefix, commaRoundTrip, options.allowEmptyArrays, options.strictNullHandling, options.skipNulls, options.encodeDotInKeys, options.encode ? options.encoder : null, options.filter, options.sort, options.allowDots, options.serializeDate, options.format, options.formatter, options.encodeValuesOnly, options.charset, sideChannel));
		}
		var joined = keys.join(options.delimiter);
		var prefix = options.addQueryPrefix === true ? "?" : "";
		if (options.charsetSentinel) if (options.charset === "iso-8859-1") prefix += "utf8=%26%2310003%3B&";
		else prefix += "utf8=%E2%9C%93&";
		return joined.length > 0 ? prefix + joined : "";
	};
}));
//#endregion
//#region node_modules/qs/lib/parse.js
var require_parse = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var utils = require_utils();
	var has = Object.prototype.hasOwnProperty;
	var isArray = Array.isArray;
	var defaults = {
		allowDots: false,
		allowEmptyArrays: false,
		allowPrototypes: false,
		allowSparse: false,
		arrayLimit: 20,
		charset: "utf-8",
		charsetSentinel: false,
		comma: false,
		decodeDotInKeys: false,
		decoder: utils.decode,
		delimiter: "&",
		depth: 5,
		duplicates: "combine",
		ignoreQueryPrefix: false,
		interpretNumericEntities: false,
		parameterLimit: 1e3,
		parseArrays: true,
		plainObjects: false,
		strictDepth: false,
		strictMerge: true,
		strictNullHandling: false,
		throwOnLimitExceeded: false
	};
	var interpretNumericEntities = function(str) {
		return str.replace(/&#(\d+);/g, function($0, numberStr) {
			return String.fromCharCode(parseInt(numberStr, 10));
		});
	};
	var parseArrayValue = function(val, options, currentArrayLength) {
		if (val && typeof val === "string" && options.comma && val.indexOf(",") > -1) return val.split(",");
		if (options.throwOnLimitExceeded && currentArrayLength >= options.arrayLimit) throw new RangeError("Array limit exceeded. Only " + options.arrayLimit + " element" + (options.arrayLimit === 1 ? "" : "s") + " allowed in an array.");
		return val;
	};
	var isoSentinel = "utf8=%26%2310003%3B";
	var charsetSentinel = "utf8=%E2%9C%93";
	var parseValues = function parseQueryStringValues(str, options) {
		var obj = { __proto__: null };
		var cleanStr = options.ignoreQueryPrefix ? str.replace(/^\?/, "") : str;
		cleanStr = cleanStr.replace(/%5B/gi, "[").replace(/%5D/gi, "]");
		var limit = options.parameterLimit === Infinity ? void 0 : options.parameterLimit;
		var parts = cleanStr.split(options.delimiter, options.throwOnLimitExceeded && typeof limit !== "undefined" ? limit + 1 : limit);
		if (options.throwOnLimitExceeded && typeof limit !== "undefined" && parts.length > limit) throw new RangeError("Parameter limit exceeded. Only " + limit + " parameter" + (limit === 1 ? "" : "s") + " allowed.");
		var skipIndex = -1;
		var i;
		var charset = options.charset;
		if (options.charsetSentinel) {
			for (i = 0; i < parts.length; ++i) if (parts[i].indexOf("utf8=") === 0) {
				if (parts[i] === charsetSentinel) charset = "utf-8";
				else if (parts[i] === isoSentinel) charset = "iso-8859-1";
				skipIndex = i;
				i = parts.length;
			}
		}
		for (i = 0; i < parts.length; ++i) {
			if (i === skipIndex) continue;
			var part = parts[i];
			var bracketEqualsPos = part.indexOf("]=");
			var pos = bracketEqualsPos === -1 ? part.indexOf("=") : bracketEqualsPos + 1;
			var key;
			var val;
			if (pos === -1) {
				key = options.decoder(part, defaults.decoder, charset, "key");
				val = options.strictNullHandling ? null : "";
			} else {
				key = options.decoder(part.slice(0, pos), defaults.decoder, charset, "key");
				if (key !== null) val = utils.maybeMap(parseArrayValue(part.slice(pos + 1), options, isArray(obj[key]) ? obj[key].length : 0), function(encodedVal) {
					return options.decoder(encodedVal, defaults.decoder, charset, "value");
				});
			}
			if (val && options.interpretNumericEntities && charset === "iso-8859-1") val = interpretNumericEntities(String(val));
			if (part.indexOf("[]=") > -1) val = isArray(val) ? [val] : val;
			if (options.comma && isArray(val) && val.length > options.arrayLimit) {
				if (options.throwOnLimitExceeded) throw new RangeError("Array limit exceeded. Only " + options.arrayLimit + " element" + (options.arrayLimit === 1 ? "" : "s") + " allowed in an array.");
				val = utils.combine([], val, options.arrayLimit, options.plainObjects);
			}
			if (key !== null) {
				var existing = has.call(obj, key);
				if (existing && (options.duplicates === "combine" || part.indexOf("[]=") > -1)) obj[key] = utils.combine(obj[key], val, options.arrayLimit, options.plainObjects);
				else if (!existing || options.duplicates === "last") obj[key] = val;
			}
		}
		return obj;
	};
	var parseObject = function(chain, val, options, valuesParsed) {
		var currentArrayLength = 0;
		if (chain.length > 0 && chain[chain.length - 1] === "[]") {
			var parentKey = chain.slice(0, -1).join("");
			currentArrayLength = Array.isArray(val) && val[parentKey] ? val[parentKey].length : 0;
		}
		var leaf = valuesParsed ? val : parseArrayValue(val, options, currentArrayLength);
		for (var i = chain.length - 1; i >= 0; --i) {
			var obj;
			var root = chain[i];
			if (root === "[]" && options.parseArrays) if (utils.isOverflow(leaf)) obj = leaf;
			else obj = options.allowEmptyArrays && (leaf === "" || options.strictNullHandling && leaf === null) ? [] : utils.combine([], leaf, options.arrayLimit, options.plainObjects);
			else {
				obj = options.plainObjects ? { __proto__: null } : {};
				var cleanRoot = root.charAt(0) === "[" && root.charAt(root.length - 1) === "]" ? root.slice(1, -1) : root;
				var decodedRoot = options.decodeDotInKeys ? cleanRoot.replace(/%2E/g, ".") : cleanRoot;
				var index = parseInt(decodedRoot, 10);
				var isValidArrayIndex = !isNaN(index) && root !== decodedRoot && String(index) === decodedRoot && index >= 0 && options.parseArrays;
				if (!options.parseArrays && decodedRoot === "") obj = { 0: leaf };
				else if (isValidArrayIndex && index < options.arrayLimit) {
					obj = [];
					obj[index] = leaf;
				} else if (isValidArrayIndex && options.throwOnLimitExceeded) throw new RangeError("Array limit exceeded. Only " + options.arrayLimit + " element" + (options.arrayLimit === 1 ? "" : "s") + " allowed in an array.");
				else if (isValidArrayIndex) {
					obj[index] = leaf;
					utils.markOverflow(obj, index);
				} else if (decodedRoot !== "__proto__") obj[decodedRoot] = leaf;
			}
			leaf = obj;
		}
		return leaf;
	};
	var splitKeyIntoSegments = function splitKeyIntoSegments(givenKey, options) {
		var key = options.allowDots ? givenKey.replace(/\.([^.[]+)/g, "[$1]") : givenKey;
		if (options.depth <= 0) {
			if (!options.plainObjects && has.call(Object.prototype, key)) {
				if (!options.allowPrototypes) return;
			}
			return [key];
		}
		var brackets = /(\[[^[\]]*])/;
		var child = /(\[[^[\]]*])/g;
		var segment = brackets.exec(key);
		var parent = segment ? key.slice(0, segment.index) : key;
		var keys = [];
		if (parent) {
			if (!options.plainObjects && has.call(Object.prototype, parent)) {
				if (!options.allowPrototypes) return;
			}
			keys[keys.length] = parent;
		}
		var i = 0;
		while ((segment = child.exec(key)) !== null && i < options.depth) {
			i += 1;
			var segmentContent = segment[1].slice(1, -1);
			if (!options.plainObjects && has.call(Object.prototype, segmentContent)) {
				if (!options.allowPrototypes) return;
			}
			keys[keys.length] = segment[1];
		}
		if (segment) {
			if (options.strictDepth === true) throw new RangeError("Input depth exceeded depth option of " + options.depth + " and strictDepth is true");
			keys[keys.length] = "[" + key.slice(segment.index) + "]";
		}
		return keys;
	};
	var parseKeys = function parseQueryStringKeys(givenKey, val, options, valuesParsed) {
		if (!givenKey) return;
		var keys = splitKeyIntoSegments(givenKey, options);
		if (!keys) return;
		return parseObject(keys, val, options, valuesParsed);
	};
	var normalizeParseOptions = function normalizeParseOptions(opts) {
		if (!opts) return defaults;
		if (typeof opts.allowEmptyArrays !== "undefined" && typeof opts.allowEmptyArrays !== "boolean") throw new TypeError("`allowEmptyArrays` option can only be `true` or `false`, when provided");
		if (typeof opts.decodeDotInKeys !== "undefined" && typeof opts.decodeDotInKeys !== "boolean") throw new TypeError("`decodeDotInKeys` option can only be `true` or `false`, when provided");
		if (opts.decoder !== null && typeof opts.decoder !== "undefined" && typeof opts.decoder !== "function") throw new TypeError("Decoder has to be a function.");
		if (typeof opts.charset !== "undefined" && opts.charset !== "utf-8" && opts.charset !== "iso-8859-1") throw new TypeError("The charset option must be either utf-8, iso-8859-1, or undefined");
		if (typeof opts.throwOnLimitExceeded !== "undefined" && typeof opts.throwOnLimitExceeded !== "boolean") throw new TypeError("`throwOnLimitExceeded` option must be a boolean");
		var charset = typeof opts.charset === "undefined" ? defaults.charset : opts.charset;
		var duplicates = typeof opts.duplicates === "undefined" ? defaults.duplicates : opts.duplicates;
		if (duplicates !== "combine" && duplicates !== "first" && duplicates !== "last") throw new TypeError("The duplicates option must be either combine, first, or last");
		return {
			allowDots: typeof opts.allowDots === "undefined" ? opts.decodeDotInKeys === true ? true : defaults.allowDots : !!opts.allowDots,
			allowEmptyArrays: typeof opts.allowEmptyArrays === "boolean" ? !!opts.allowEmptyArrays : defaults.allowEmptyArrays,
			allowPrototypes: typeof opts.allowPrototypes === "boolean" ? opts.allowPrototypes : defaults.allowPrototypes,
			allowSparse: typeof opts.allowSparse === "boolean" ? opts.allowSparse : defaults.allowSparse,
			arrayLimit: typeof opts.arrayLimit === "number" ? opts.arrayLimit : defaults.arrayLimit,
			charset,
			charsetSentinel: typeof opts.charsetSentinel === "boolean" ? opts.charsetSentinel : defaults.charsetSentinel,
			comma: typeof opts.comma === "boolean" ? opts.comma : defaults.comma,
			decodeDotInKeys: typeof opts.decodeDotInKeys === "boolean" ? opts.decodeDotInKeys : defaults.decodeDotInKeys,
			decoder: typeof opts.decoder === "function" ? opts.decoder : defaults.decoder,
			delimiter: typeof opts.delimiter === "string" || utils.isRegExp(opts.delimiter) ? opts.delimiter : defaults.delimiter,
			depth: typeof opts.depth === "number" || opts.depth === false ? +opts.depth : defaults.depth,
			duplicates,
			ignoreQueryPrefix: opts.ignoreQueryPrefix === true,
			interpretNumericEntities: typeof opts.interpretNumericEntities === "boolean" ? opts.interpretNumericEntities : defaults.interpretNumericEntities,
			parameterLimit: typeof opts.parameterLimit === "number" ? opts.parameterLimit : defaults.parameterLimit,
			parseArrays: opts.parseArrays !== false,
			plainObjects: typeof opts.plainObjects === "boolean" ? opts.plainObjects : defaults.plainObjects,
			strictDepth: typeof opts.strictDepth === "boolean" ? !!opts.strictDepth : defaults.strictDepth,
			strictMerge: typeof opts.strictMerge === "boolean" ? !!opts.strictMerge : defaults.strictMerge,
			strictNullHandling: typeof opts.strictNullHandling === "boolean" ? opts.strictNullHandling : defaults.strictNullHandling,
			throwOnLimitExceeded: typeof opts.throwOnLimitExceeded === "boolean" ? opts.throwOnLimitExceeded : false
		};
	};
	module.exports = function(str, opts) {
		var options = normalizeParseOptions(opts);
		if (str === "" || str === null || typeof str === "undefined") return options.plainObjects ? { __proto__: null } : {};
		var tempObj = typeof str === "string" ? parseValues(str, options) : str;
		var obj = options.plainObjects ? { __proto__: null } : {};
		var keys = Object.keys(tempObj);
		for (var i = 0; i < keys.length; ++i) {
			var key = keys[i];
			var newObj = parseKeys(key, tempObj[key], options, typeof str === "string");
			obj = utils.merge(obj, newObj, options);
		}
		if (options.allowSparse === true) return obj;
		return utils.compact(obj);
	};
}));
//#endregion
//#region node_modules/qs/lib/index.js
var require_lib = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	var stringify = require_stringify();
	var parse = require_parse();
	module.exports = {
		formats: require_formats(),
		parse,
		stringify
	};
}));
//#endregion
//#region node_modules/nprogress/nprogress.js
var require_nprogress = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	(function(root, factory) {
		if (typeof define === "function" && define.amd) define(factory);
		else if (typeof exports === "object") module.exports = factory();
		else root.NProgress = factory();
	})(exports, function() {
		var NProgress = {};
		NProgress.version = "0.2.0";
		var Settings = NProgress.settings = {
			minimum: .08,
			easing: "ease",
			positionUsing: "",
			speed: 200,
			trickle: true,
			trickleRate: .02,
			trickleSpeed: 800,
			showSpinner: true,
			barSelector: "[role=\"bar\"]",
			spinnerSelector: "[role=\"spinner\"]",
			parent: "body",
			template: "<div class=\"bar\" role=\"bar\"><div class=\"peg\"></div></div><div class=\"spinner\" role=\"spinner\"><div class=\"spinner-icon\"></div></div>"
		};
		/**
		* Updates configuration.
		*
		*     NProgress.configure({
		*       minimum: 0.1
		*     });
		*/
		NProgress.configure = function(options) {
			var key, value;
			for (key in options) {
				value = options[key];
				if (value !== void 0 && options.hasOwnProperty(key)) Settings[key] = value;
			}
			return this;
		};
		/**
		* Last number.
		*/
		NProgress.status = null;
		/**
		* Sets the progress bar status, where `n` is a number from `0.0` to `1.0`.
		*
		*     NProgress.set(0.4);
		*     NProgress.set(1.0);
		*/
		NProgress.set = function(n) {
			var started = NProgress.isStarted();
			n = clamp(n, Settings.minimum, 1);
			NProgress.status = n === 1 ? null : n;
			var progress = NProgress.render(!started), bar = progress.querySelector(Settings.barSelector), speed = Settings.speed, ease = Settings.easing;
			progress.offsetWidth;
			queue(function(next) {
				if (Settings.positionUsing === "") Settings.positionUsing = NProgress.getPositioningCSS();
				css(bar, barPositionCSS(n, speed, ease));
				if (n === 1) {
					css(progress, {
						transition: "none",
						opacity: 1
					});
					progress.offsetWidth;
					setTimeout(function() {
						css(progress, {
							transition: "all " + speed + "ms linear",
							opacity: 0
						});
						setTimeout(function() {
							NProgress.remove();
							next();
						}, speed);
					}, speed);
				} else setTimeout(next, speed);
			});
			return this;
		};
		NProgress.isStarted = function() {
			return typeof NProgress.status === "number";
		};
		/**
		* Shows the progress bar.
		* This is the same as setting the status to 0%, except that it doesn't go backwards.
		*
		*     NProgress.start();
		*
		*/
		NProgress.start = function() {
			if (!NProgress.status) NProgress.set(0);
			var work = function() {
				setTimeout(function() {
					if (!NProgress.status) return;
					NProgress.trickle();
					work();
				}, Settings.trickleSpeed);
			};
			if (Settings.trickle) work();
			return this;
		};
		/**
		* Hides the progress bar.
		* This is the *sort of* the same as setting the status to 100%, with the
		* difference being `done()` makes some placebo effect of some realistic motion.
		*
		*     NProgress.done();
		*
		* If `true` is passed, it will show the progress bar even if its hidden.
		*
		*     NProgress.done(true);
		*/
		NProgress.done = function(force) {
			if (!force && !NProgress.status) return this;
			return NProgress.inc(.3 + .5 * Math.random()).set(1);
		};
		/**
		* Increments by a random amount.
		*/
		NProgress.inc = function(amount) {
			var n = NProgress.status;
			if (!n) return NProgress.start();
			else {
				if (typeof amount !== "number") amount = (1 - n) * clamp(Math.random() * n, .1, .95);
				n = clamp(n + amount, 0, .994);
				return NProgress.set(n);
			}
		};
		NProgress.trickle = function() {
			return NProgress.inc(Math.random() * Settings.trickleRate);
		};
		/**
		* Waits for all supplied jQuery promises and
		* increases the progress as the promises resolve.
		*
		* @param $promise jQUery Promise
		*/
		(function() {
			var initial = 0, current = 0;
			NProgress.promise = function($promise) {
				if (!$promise || $promise.state() === "resolved") return this;
				if (current === 0) NProgress.start();
				initial++;
				current++;
				$promise.always(function() {
					current--;
					if (current === 0) {
						initial = 0;
						NProgress.done();
					} else NProgress.set((initial - current) / initial);
				});
				return this;
			};
		})();
		/**
		* (Internal) renders the progress bar markup based on the `template`
		* setting.
		*/
		NProgress.render = function(fromStart) {
			if (NProgress.isRendered()) return document.getElementById("nprogress");
			addClass(document.documentElement, "nprogress-busy");
			var progress = document.createElement("div");
			progress.id = "nprogress";
			progress.innerHTML = Settings.template;
			var bar = progress.querySelector(Settings.barSelector), perc = fromStart ? "-100" : toBarPerc(NProgress.status || 0), parent = document.querySelector(Settings.parent), spinner;
			css(bar, {
				transition: "all 0 linear",
				transform: "translate3d(" + perc + "%,0,0)"
			});
			if (!Settings.showSpinner) {
				spinner = progress.querySelector(Settings.spinnerSelector);
				spinner && removeElement(spinner);
			}
			if (parent != document.body) addClass(parent, "nprogress-custom-parent");
			parent.appendChild(progress);
			return progress;
		};
		/**
		* Removes the element. Opposite of render().
		*/
		NProgress.remove = function() {
			removeClass(document.documentElement, "nprogress-busy");
			removeClass(document.querySelector(Settings.parent), "nprogress-custom-parent");
			var progress = document.getElementById("nprogress");
			progress && removeElement(progress);
		};
		/**
		* Checks if the progress bar is rendered.
		*/
		NProgress.isRendered = function() {
			return !!document.getElementById("nprogress");
		};
		/**
		* Determine which positioning CSS rule to use.
		*/
		NProgress.getPositioningCSS = function() {
			var bodyStyle = document.body.style;
			var vendorPrefix = "WebkitTransform" in bodyStyle ? "Webkit" : "MozTransform" in bodyStyle ? "Moz" : "msTransform" in bodyStyle ? "ms" : "OTransform" in bodyStyle ? "O" : "";
			if (vendorPrefix + "Perspective" in bodyStyle) return "translate3d";
			else if (vendorPrefix + "Transform" in bodyStyle) return "translate";
			else return "margin";
		};
		/**
		* Helpers
		*/
		function clamp(n, min, max) {
			if (n < min) return min;
			if (n > max) return max;
			return n;
		}
		/**
		* (Internal) converts a percentage (`0..1`) to a bar translateX
		* percentage (`-100%..0%`).
		*/
		function toBarPerc(n) {
			return (-1 + n) * 100;
		}
		/**
		* (Internal) returns the correct CSS for changing the bar's
		* position given an n percentage, and speed and ease from Settings
		*/
		function barPositionCSS(n, speed, ease) {
			var barCSS;
			if (Settings.positionUsing === "translate3d") barCSS = { transform: "translate3d(" + toBarPerc(n) + "%,0,0)" };
			else if (Settings.positionUsing === "translate") barCSS = { transform: "translate(" + toBarPerc(n) + "%,0)" };
			else barCSS = { "margin-left": toBarPerc(n) + "%" };
			barCSS.transition = "all " + speed + "ms " + ease;
			return barCSS;
		}
		/**
		* (Internal) Queues a function to be executed.
		*/
		var queue = (function() {
			var pending = [];
			function next() {
				var fn = pending.shift();
				if (fn) fn(next);
			}
			return function(fn) {
				pending.push(fn);
				if (pending.length == 1) next();
			};
		})();
		/**
		* (Internal) Applies css properties to an element, similar to the jQuery 
		* css method.
		*
		* While this helper does assist with vendor prefixed property names, it 
		* does not perform any manipulation of values prior to setting styles.
		*/
		var css = (function() {
			var cssPrefixes = [
				"Webkit",
				"O",
				"Moz",
				"ms"
			], cssProps = {};
			function camelCase(string) {
				return string.replace(/^-ms-/, "ms-").replace(/-([\da-z])/gi, function(match, letter) {
					return letter.toUpperCase();
				});
			}
			function getVendorProp(name) {
				var style = document.body.style;
				if (name in style) return name;
				var i = cssPrefixes.length, capName = name.charAt(0).toUpperCase() + name.slice(1), vendorName;
				while (i--) {
					vendorName = cssPrefixes[i] + capName;
					if (vendorName in style) return vendorName;
				}
				return name;
			}
			function getStyleProp(name) {
				name = camelCase(name);
				return cssProps[name] || (cssProps[name] = getVendorProp(name));
			}
			function applyCss(element, prop, value) {
				prop = getStyleProp(prop);
				element.style[prop] = value;
			}
			return function(element, properties) {
				var args = arguments, prop, value;
				if (args.length == 2) for (prop in properties) {
					value = properties[prop];
					if (value !== void 0 && properties.hasOwnProperty(prop)) applyCss(element, prop, value);
				}
				else applyCss(element, args[1], args[2]);
			};
		})();
		/**
		* (Internal) Determines if an element or space separated list of class names contains a class name.
		*/
		function hasClass(element, name) {
			return (typeof element == "string" ? element : classList(element)).indexOf(" " + name + " ") >= 0;
		}
		/**
		* (Internal) Adds a class to an element.
		*/
		function addClass(element, name) {
			var oldList = classList(element), newList = oldList + name;
			if (hasClass(oldList, name)) return;
			element.className = newList.substring(1);
		}
		/**
		* (Internal) Removes a class from an element.
		*/
		function removeClass(element, name) {
			var oldList = classList(element), newList;
			if (!hasClass(element, name)) return;
			newList = oldList.replace(" " + name + " ", " ");
			element.className = newList.substring(1, newList.length - 1);
		}
		/**
		* (Internal) Gets a space separated list of the class names on the element. 
		* The list is wrapped with a single space on each end to facilitate finding 
		* matches within the list.
		*/
		function classList(element) {
			return (" " + (element.className || "") + " ").replace(/\s+/gi, " ");
		}
		/**
		* (Internal) Removes an element from the DOM.
		*/
		function removeElement(element) {
			element && element.parentNode && element.parentNode.removeChild(element);
		}
		return NProgress;
	});
}));
//#endregion
//#region node_modules/@inertiajs/core/dist/index.esm.js
var import_cjs = /* @__PURE__ */ __toESM(require_cjs());
var import_lib = /* @__PURE__ */ __toESM(require_lib());
var import_nprogress = /* @__PURE__ */ __toESM(require_nprogress());
function T(t, e) {
	let i;
	return function(...r) {
		clearTimeout(i), i = setTimeout(() => t.apply(this, r), e);
	};
}
function f(t, e) {
	return document.dispatchEvent(new CustomEvent(`inertia:${t}`, e));
}
var H$1 = (t) => f("before", {
	cancelable: !0,
	detail: { visit: t }
}), q$1 = (t) => f("error", { detail: { errors: t } }), $$1 = (t) => f("exception", {
	cancelable: !0,
	detail: { exception: t }
}), N$1 = (t) => f("finish", { detail: { visit: t } }), W$1 = (t) => f("invalid", {
	cancelable: !0,
	detail: { response: t }
}), P = (t) => f("navigate", { detail: { page: t } }), K$1 = (t) => f("progress", { detail: { progress: t } }), X$1 = (t) => f("start", { detail: { visit: t } }), B = (t) => f("success", { detail: { page: t } });
function I(t) {
	return t instanceof File || t instanceof Blob || t instanceof FileList && t.length > 0 || t instanceof FormData && Array.from(t.values()).some((e) => I(e)) || typeof t == "object" && t !== null && Object.values(t).some((e) => I(e));
}
function k(t, e = new FormData(), i = null) {
	t = t || {};
	for (let r in t) Object.prototype.hasOwnProperty.call(t, r) && z(e, J$1(i, r), t[r]);
	return e;
}
function J$1(t, e) {
	return t ? t + "[" + e + "]" : e;
}
function z(t, e, i) {
	if (Array.isArray(i)) return Array.from(i.keys()).forEach((r) => z(t, J$1(e, r.toString()), i[r]));
	if (i instanceof Date) return t.append(e, i.toISOString());
	if (i instanceof File) return t.append(e, i, i.name);
	if (i instanceof Blob) return t.append(e, i);
	if (typeof i == "boolean") return t.append(e, i ? "1" : "0");
	if (typeof i == "string") return t.append(e, i);
	if (typeof i == "number") return t.append(e, `${i}`);
	if (i == null) return t.append(e, "");
	k(i, t, e);
}
var _ = {
	modal: null,
	listener: null,
	show(t) {
		typeof t == "object" && (t = `All Inertia requests must receive a valid Inertia response, however a plain JSON response was received.<hr>${JSON.stringify(t)}`);
		let e = document.createElement("html");
		e.innerHTML = t, e.querySelectorAll("a").forEach((r) => r.setAttribute("target", "_top")), this.modal = document.createElement("div"), this.modal.style.position = "fixed", this.modal.style.width = "100vw", this.modal.style.height = "100vh", this.modal.style.padding = "50px", this.modal.style.boxSizing = "border-box", this.modal.style.backgroundColor = "rgba(0, 0, 0, .6)", this.modal.style.zIndex = 2e5, this.modal.addEventListener("click", () => this.hide());
		let i = document.createElement("iframe");
		if (i.style.backgroundColor = "white", i.style.borderRadius = "5px", i.style.width = "100%", i.style.height = "100%", this.modal.appendChild(i), document.body.prepend(this.modal), document.body.style.overflow = "hidden", !i.contentWindow) throw new Error("iframe not yet ready.");
		i.contentWindow.document.open(), i.contentWindow.document.write(e.outerHTML), i.contentWindow.document.close(), this.listener = this.hideOnEscape.bind(this), document.addEventListener("keydown", this.listener);
	},
	hide() {
		this.modal.outerHTML = "", this.modal = null, document.body.style.overflow = "visible", document.removeEventListener("keydown", this.listener);
	},
	hideOnEscape(t) {
		t.keyCode === 27 && this.hide();
	}
};
function b$1(t) {
	return new URL(t.toString(), window.location.toString());
}
function D(t, e, i, r = "brackets") {
	let s = /^https?:\/\//.test(e.toString()), l = s || e.toString().startsWith("/"), h = !l && !e.toString().startsWith("#") && !e.toString().startsWith("?"), g = e.toString().includes("?") || t === "get" && Object.keys(i).length, m = e.toString().includes("#"), c = new URL(e.toString(), "http://localhost");
	return t === "get" && Object.keys(i).length && (c.search = import_lib.stringify((0, import_cjs.default)(import_lib.parse(c.search, { ignoreQueryPrefix: !0 }), i), {
		encodeValuesOnly: !0,
		arrayFormat: r
	}), i = {}), [[
		s ? `${c.protocol}//${c.host}` : "",
		l ? c.pathname : "",
		h ? c.pathname.substring(1) : "",
		g ? c.search : "",
		m ? c.hash : ""
	].join(""), i];
}
function E(t) {
	return t = new URL(t.href), t.hash = "", t;
}
var C$1 = typeof window > "u", Y = !C$1 && /CriOS/.test(window.navigator.userAgent), Z = (t) => {
	requestAnimationFrame(() => {
		requestAnimationFrame(t);
	});
}, F = class {
	constructor() {
		this.visitId = null;
	}
	init({ initialPage: e, resolveComponent: i, swapComponent: r }) {
		this.page = e, this.resolveComponent = i, this.swapComponent = r, this.setNavigationType(), this.clearRememberedStateOnReload(), this.isBackForwardVisit() ? this.handleBackForwardVisit(this.page) : this.isLocationVisit() ? this.handleLocationVisit(this.page) : this.handleInitialPageVisit(this.page), this.setupEventListeners();
	}
	setNavigationType() {
		this.navigationType = window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType("navigation").length > 0 ? window.performance.getEntriesByType("navigation")[0].type : "navigate";
	}
	clearRememberedStateOnReload() {
		this.navigationType === "reload" && window.history.state?.rememberedState && delete window.history.state.rememberedState;
	}
	handleInitialPageVisit(e) {
		let i = window.location.hash;
		this.page.url.includes(i) || (this.page.url += i), this.setPage(e, {
			preserveScroll: !0,
			preserveState: !0
		}).then(() => P(e));
	}
	setupEventListeners() {
		window.addEventListener("popstate", this.handlePopstateEvent.bind(this)), document.addEventListener("scroll", T(this.handleScrollEvent.bind(this), 100), !0);
	}
	scrollRegions() {
		return document.querySelectorAll("[scroll-region]");
	}
	handleScrollEvent(e) {
		typeof e.target.hasAttribute == "function" && e.target.hasAttribute("scroll-region") && this.saveScrollPositions();
	}
	saveScrollPositions() {
		this.replaceState({
			...this.page,
			scrollRegions: Array.from(this.scrollRegions()).map((e) => ({
				top: e.scrollTop,
				left: e.scrollLeft
			}))
		});
	}
	resetScrollPositions() {
		Z(() => {
			window.scrollTo(0, 0), this.scrollRegions().forEach((e) => {
				typeof e.scrollTo == "function" ? e.scrollTo(0, 0) : (e.scrollTop = 0, e.scrollLeft = 0);
			}), this.saveScrollPositions(), window.location.hash && document.getElementById(window.location.hash.slice(1))?.scrollIntoView();
		});
	}
	restoreScrollPositions() {
		Z(() => {
			this.page.scrollRegions && this.scrollRegions().forEach((e, i) => {
				let r = this.page.scrollRegions[i];
				if (r) typeof e.scrollTo == "function" ? e.scrollTo(r.left, r.top) : (e.scrollTop = r.top, e.scrollLeft = r.left);
				else return;
			});
		});
	}
	isBackForwardVisit() {
		return window.history.state && this.navigationType === "back_forward";
	}
	handleBackForwardVisit(e) {
		window.history.state.version = e.version, this.setPage(window.history.state, {
			preserveScroll: !0,
			preserveState: !0
		}).then(() => {
			this.restoreScrollPositions(), P(e);
		});
	}
	locationVisit(e, i) {
		try {
			let r = { preserveScroll: i };
			window.sessionStorage.setItem("inertiaLocationVisit", JSON.stringify(r)), window.location.href = e.href, E(window.location).href === E(e).href && window.location.reload();
		} catch {
			return !1;
		}
	}
	isLocationVisit() {
		try {
			return window.sessionStorage.getItem("inertiaLocationVisit") !== null;
		} catch {
			return !1;
		}
	}
	handleLocationVisit(e) {
		let i = JSON.parse(window.sessionStorage.getItem("inertiaLocationVisit") || "");
		window.sessionStorage.removeItem("inertiaLocationVisit"), e.url += window.location.hash, e.rememberedState = window.history.state?.rememberedState ?? {}, e.scrollRegions = window.history.state?.scrollRegions ?? [], this.setPage(e, {
			preserveScroll: i.preserveScroll,
			preserveState: !0
		}).then(() => {
			i.preserveScroll && this.restoreScrollPositions(), P(e);
		});
	}
	isLocationVisitResponse(e) {
		return !!(e && e.status === 409 && e.headers["x-inertia-location"]);
	}
	isInertiaResponse(e) {
		return !!e?.headers["x-inertia"];
	}
	createVisitId() {
		return this.visitId = {}, this.visitId;
	}
	cancelVisit(e, { cancelled: i = !1, interrupted: r = !1 }) {
		e && !e.completed && !e.cancelled && !e.interrupted && (e.cancelToken.abort(), e.onCancel(), e.completed = !1, e.cancelled = i, e.interrupted = r, N$1(e), e.onFinish(e));
	}
	finishVisit(e) {
		!e.cancelled && !e.interrupted && (e.completed = !0, e.cancelled = !1, e.interrupted = !1, N$1(e), e.onFinish(e));
	}
	resolvePreserveOption(e, i) {
		return typeof e == "function" ? e(i) : e === "errors" ? Object.keys(i.props.errors || {}).length > 0 : e;
	}
	cancel() {
		this.activeVisit && this.cancelVisit(this.activeVisit, { cancelled: !0 });
	}
	visit(e, { method: i = "get", data: r = {}, replace: s = !1, preserveScroll: l = !1, preserveState: h = !1, only: g = [], except: m = [], headers: c = {}, errorBag: o = "", forceFormData: v = !1, onCancelToken: L = () => {}, onBefore: d = () => {}, onStart: p = () => {}, onProgress: x = () => {}, onFinish: y = () => {}, onCancel: ne = () => {}, onSuccess: U = () => {}, onError: G = () => {}, queryStringArrayFormat: A = "brackets" } = {}) {
		let S = typeof e == "string" ? b$1(e) : e;
		if ((I(r) || v) && !(r instanceof FormData) && (r = k(r)), !(r instanceof FormData)) {
			let [n, a] = D(i, S, r, A);
			S = b$1(n), r = a;
		}
		let R = {
			url: S,
			method: i,
			data: r,
			replace: s,
			preserveScroll: l,
			preserveState: h,
			only: g,
			except: m,
			headers: c,
			errorBag: o,
			forceFormData: v,
			queryStringArrayFormat: A,
			cancelled: !1,
			completed: !1,
			interrupted: !1
		};
		if (d(R) === !1 || !H$1(R)) return;
		this.activeVisit && this.cancelVisit(this.activeVisit, { interrupted: !0 }), this.saveScrollPositions();
		let M = this.createVisitId();
		this.activeVisit = {
			...R,
			onCancelToken: L,
			onBefore: d,
			onStart: p,
			onProgress: x,
			onFinish: y,
			onCancel: ne,
			onSuccess: U,
			onError: G,
			queryStringArrayFormat: A,
			cancelToken: new AbortController()
		}, L({ cancel: () => {
			this.activeVisit && this.cancelVisit(this.activeVisit, { cancelled: !0 });
		} }), X$1(R), p(R);
		let j = !!(g.length || m.length);
		axios({
			method: i,
			url: E(S).href,
			data: i === "get" ? {} : r,
			params: i === "get" ? r : {},
			signal: this.activeVisit.cancelToken.signal,
			headers: {
				...c,
				Accept: "text/html, application/xhtml+xml",
				"X-Requested-With": "XMLHttpRequest",
				"X-Inertia": !0,
				...j ? { "X-Inertia-Partial-Component": this.page.component } : {},
				...g.length ? { "X-Inertia-Partial-Data": g.join(",") } : {},
				...m.length ? { "X-Inertia-Partial-Except": m.join(",") } : {},
				...o && o.length ? { "X-Inertia-Error-Bag": o } : {},
				...this.page.version ? { "X-Inertia-Version": this.page.version } : {}
			},
			onUploadProgress: (n) => {
				r instanceof FormData && (n.percentage = n.progress ? Math.round(n.progress * 100) : 0, K$1(n), x(n));
			}
		}).then((n) => {
			if (!this.isInertiaResponse(n)) return Promise.reject({ response: n });
			let a = n.data;
			j && a.component === this.page.component && (a.props = {
				...this.page.props,
				...a.props
			}), l = this.resolvePreserveOption(l, a), h = this.resolvePreserveOption(h, a), h && window.history.state?.rememberedState && a.component === this.page.component && (a.rememberedState = window.history.state.rememberedState);
			let w = S, V = b$1(a.url);
			return w.hash && !V.hash && E(w).href === V.href && (V.hash = w.hash, a.url = V.href), this.setPage(a, {
				visitId: M,
				replace: s,
				preserveScroll: l,
				preserveState: h
			});
		}).then(() => {
			let n = this.page.props.errors || {};
			if (Object.keys(n).length > 0) {
				let a = o ? n[o] ? n[o] : {} : n;
				return q$1(a), G(a);
			}
			return B(this.page), U(this.page);
		}).catch((n) => {
			if (this.isInertiaResponse(n.response)) return this.setPage(n.response.data, { visitId: M });
			if (this.isLocationVisitResponse(n.response)) {
				let a = b$1(n.response.headers["x-inertia-location"]), w = S;
				w.hash && !a.hash && E(w).href === a.href && (a.hash = w.hash), this.locationVisit(a, l === !0);
			} else if (n.response) W$1(n.response) && _.show(n.response.data);
			else return Promise.reject(n);
		}).then(() => {
			this.activeVisit && this.finishVisit(this.activeVisit);
		}).catch((n) => {
			if (!axios.isCancel(n)) {
				let a = $$1(n);
				if (this.activeVisit && this.finishVisit(this.activeVisit), a) return Promise.reject(n);
			}
		});
	}
	setPage(e, { visitId: i = this.createVisitId(), replace: r = !1, preserveScroll: s = !1, preserveState: l = !1 } = {}) {
		return Promise.resolve(this.resolveComponent(e.component)).then((h) => {
			i === this.visitId && (e.scrollRegions = this.page.scrollRegions || [], e.rememberedState = e.rememberedState || {}, r = r || b$1(e.url).href === window.location.href, r ? this.replaceState(e) : this.pushState(e), this.swapComponent({
				component: h,
				page: e,
				preserveState: l
			}).then(() => {
				s ? this.restoreScrollPositions() : this.resetScrollPositions(), r || P(e);
			}));
		});
	}
	pushState(e) {
		this.page = e, Y ? setTimeout(() => window.history.pushState(e, "", e.url)) : window.history.pushState(e, "", e.url);
	}
	replaceState(e) {
		this.page = e, Y ? setTimeout(() => window.history.replaceState(e, "", e.url)) : window.history.replaceState(e, "", e.url);
	}
	handlePopstateEvent(e) {
		if (e.state !== null) {
			let i = e.state, r = this.createVisitId();
			Promise.resolve(this.resolveComponent(i.component)).then((s) => {
				r === this.visitId && (this.page = i, this.swapComponent({
					component: s,
					page: i,
					preserveState: !1
				}).then(() => {
					this.restoreScrollPositions(), P(i);
				}));
			});
		} else {
			let i = b$1(this.page.url);
			i.hash = window.location.hash, this.replaceState({
				...this.page,
				url: i.href
			}), this.resetScrollPositions();
		}
	}
	get(e, i = {}, r = {}) {
		return this.visit(e, {
			...r,
			method: "get",
			data: i
		});
	}
	reload(e = {}) {
		return this.visit(window.location.href, {
			...e,
			preserveScroll: !0,
			preserveState: !0
		});
	}
	replace(e, i = {}) {
		return console.warn(`Inertia.replace() has been deprecated and will be removed in a future release. Please use Inertia.${i.method ?? "get"}() instead.`), this.visit(e, {
			preserveState: !0,
			...i,
			replace: !0
		});
	}
	post(e, i = {}, r = {}) {
		return this.visit(e, {
			preserveState: !0,
			...r,
			method: "post",
			data: i
		});
	}
	put(e, i = {}, r = {}) {
		return this.visit(e, {
			preserveState: !0,
			...r,
			method: "put",
			data: i
		});
	}
	patch(e, i = {}, r = {}) {
		return this.visit(e, {
			preserveState: !0,
			...r,
			method: "patch",
			data: i
		});
	}
	delete(e, i = {}) {
		return this.visit(e, {
			preserveState: !0,
			...i,
			method: "delete"
		});
	}
	remember(e, i = "default") {
		C$1 || this.replaceState({
			...this.page,
			rememberedState: {
				...this.page?.rememberedState,
				[i]: e
			}
		});
	}
	restore(e = "default") {
		if (!C$1) return window.history.state?.rememberedState?.[e];
	}
	on(e, i) {
		if (C$1) return () => {};
		let r = (s) => {
			let l = i(s);
			s.cancelable && !s.defaultPrevented && l === !1 && s.preventDefault();
		};
		return document.addEventListener(`inertia:${e}`, r), () => document.removeEventListener(`inertia:${e}`, r);
	}
};
var se = {
	buildDOMElement(t) {
		let e = document.createElement("template");
		e.innerHTML = t;
		let i = e.content.firstChild;
		if (!t.startsWith("<script ")) return i;
		let r = document.createElement("script");
		return r.innerHTML = i.innerHTML, i.getAttributeNames().forEach((s) => {
			r.setAttribute(s, i.getAttribute(s) || "");
		}), r;
	},
	isInertiaManagedElement(t) {
		return t.nodeType === Node.ELEMENT_NODE && t.getAttribute("inertia") !== null;
	},
	findMatchingElementIndex(t, e) {
		let i = t.getAttribute("inertia");
		return i !== null ? e.findIndex((r) => r.getAttribute("inertia") === i) : -1;
	},
	update: T(function(t) {
		let e = t.map((r) => this.buildDOMElement(r));
		Array.from(document.head.childNodes).filter((r) => this.isInertiaManagedElement(r)).forEach((r) => {
			let s = this.findMatchingElementIndex(r, e);
			if (s === -1) {
				r?.parentNode?.removeChild(r);
				return;
			}
			let l = e.splice(s, 1)[0];
			l && !r.isEqualNode(l) && r?.parentNode?.replaceChild(l, r);
		}), e.forEach((r) => document.head.appendChild(r));
	}, 1)
};
function ee(t, e, i) {
	let r = {}, s = 0;
	function l() {
		let o = s += 1;
		return r[o] = [], o.toString();
	}
	function h(o) {
		o === null || Object.keys(r).indexOf(o) === -1 || (delete r[o], c());
	}
	function g(o, v = []) {
		o !== null && Object.keys(r).indexOf(o) > -1 && (r[o] = v), c();
	}
	function m() {
		let o = e(""), v = { ...o ? { title: `<title inertia="">${o}</title>` } : {} }, L = Object.values(r).reduce((d, p) => d.concat(p), []).reduce((d, p) => {
			if (p.indexOf("<") === -1) return d;
			if (p.indexOf("<title ") === 0) {
				let y = p.match(/(<title [^>]+>)(.*?)(<\/title>)/);
				return d.title = y ? `${y[1]}${e(y[2])}${y[3]}` : p, d;
			}
			let x = p.match(/ inertia="[^"]+"/);
			return x ? d[x[0]] = p : d[Object.keys(d).length] = p, d;
		}, v);
		return Object.values(L);
	}
	function c() {
		t ? i(m()) : se.update(m());
	}
	return c(), {
		forceUpdate: c,
		createProvider: function() {
			let o = l();
			return {
				update: (v) => g(o, v),
				disconnect: () => h(o)
			};
		}
	};
}
var te = null;
function ae(t) {
	document.addEventListener("inertia:start", le$1.bind(null, t)), document.addEventListener("inertia:progress", ce$1), document.addEventListener("inertia:finish", de$1);
}
function le$1(t) {
	te = setTimeout(() => import_nprogress.default.start(), t);
}
function ce$1(t) {
	import_nprogress.default.isStarted() && t.detail.progress?.percentage && import_nprogress.default.set(Math.max(import_nprogress.default.status, t.detail.progress.percentage / 100 * .9));
}
function de$1(t) {
	if (clearTimeout(te), import_nprogress.default.isStarted()) t.detail.visit.completed ? import_nprogress.default.done() : t.detail.visit.interrupted ? import_nprogress.default.set(0) : t.detail.visit.cancelled && (import_nprogress.default.done(), import_nprogress.default.remove());
	else return;
}
function pe$1(t) {
	let e = document.createElement("style");
	e.type = "text/css", e.textContent = `
    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: ${t};

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px ${t}, 0 0 5px ${t};
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: ${t};
      border-left-color: ${t};
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `, document.head.appendChild(e);
}
function ie$1({ delay: t = 250, color: e = "#29d", includeCSS: i = !0, showSpinner: r = !1 } = {}) {
	ae(t), import_nprogress.default.configure({ showSpinner: r }), i && pe$1(e);
}
function re(t) {
	let e = t.currentTarget.tagName.toLowerCase() === "a";
	return !(t.target && (t?.target).isContentEditable || t.defaultPrevented || e && t.altKey || e && t.ctrlKey || e && t.metaKey || e && t.shiftKey || e && "button" in t && t.button !== 0);
}
var Ne = new F();
//#endregion
//#region node_modules/lodash.isequal/index.js
var require_lodash_isequal = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/**
	* Lodash (Custom Build) <https://lodash.com/>
	* Build: `lodash modularize exports="npm" -o ./`
	* Copyright JS Foundation and other contributors <https://js.foundation/>
	* Released under MIT license <https://lodash.com/license>
	* Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
	* Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
	*/
	/** Used as the size to enable large array optimizations. */
	var LARGE_ARRAY_SIZE = 200;
	/** Used to stand-in for `undefined` hash values. */
	var HASH_UNDEFINED = "__lodash_hash_undefined__";
	/** Used to compose bitmasks for value comparisons. */
	var COMPARE_PARTIAL_FLAG = 1, COMPARE_UNORDERED_FLAG = 2;
	/** Used as references for various `Number` constants. */
	var MAX_SAFE_INTEGER = 9007199254740991;
	/** `Object#toString` result references. */
	var argsTag = "[object Arguments]", arrayTag = "[object Array]", asyncTag = "[object AsyncFunction]", boolTag = "[object Boolean]", dateTag = "[object Date]", errorTag = "[object Error]", funcTag = "[object Function]", genTag = "[object GeneratorFunction]", mapTag = "[object Map]", numberTag = "[object Number]", nullTag = "[object Null]", objectTag = "[object Object]", promiseTag = "[object Promise]", proxyTag = "[object Proxy]", regexpTag = "[object RegExp]", setTag = "[object Set]", stringTag = "[object String]", symbolTag = "[object Symbol]", undefinedTag = "[object Undefined]", weakMapTag = "[object WeakMap]";
	var arrayBufferTag = "[object ArrayBuffer]", dataViewTag = "[object DataView]", float32Tag = "[object Float32Array]", float64Tag = "[object Float64Array]", int8Tag = "[object Int8Array]", int16Tag = "[object Int16Array]", int32Tag = "[object Int32Array]", uint8Tag = "[object Uint8Array]", uint8ClampedTag = "[object Uint8ClampedArray]", uint16Tag = "[object Uint16Array]", uint32Tag = "[object Uint32Array]";
	/**
	* Used to match `RegExp`
	* [syntax characters](http://ecma-international.org/ecma-262/7.0/#sec-patterns).
	*/
	var reRegExpChar = /[\\^$.*+?()[\]{}|]/g;
	/** Used to detect host constructors (Safari). */
	var reIsHostCtor = /^\[object .+?Constructor\]$/;
	/** Used to detect unsigned integer values. */
	var reIsUint = /^(?:0|[1-9]\d*)$/;
	/** Used to identify `toStringTag` values of typed arrays. */
	var typedArrayTags = {};
	typedArrayTags[float32Tag] = typedArrayTags[float64Tag] = typedArrayTags[int8Tag] = typedArrayTags[int16Tag] = typedArrayTags[int32Tag] = typedArrayTags[uint8Tag] = typedArrayTags[uint8ClampedTag] = typedArrayTags[uint16Tag] = typedArrayTags[uint32Tag] = true;
	typedArrayTags[argsTag] = typedArrayTags[arrayTag] = typedArrayTags[arrayBufferTag] = typedArrayTags[boolTag] = typedArrayTags[dataViewTag] = typedArrayTags[dateTag] = typedArrayTags[errorTag] = typedArrayTags[funcTag] = typedArrayTags[mapTag] = typedArrayTags[numberTag] = typedArrayTags[objectTag] = typedArrayTags[regexpTag] = typedArrayTags[setTag] = typedArrayTags[stringTag] = typedArrayTags[weakMapTag] = false;
	/** Detect free variable `global` from Node.js. */
	var freeGlobal = typeof global == "object" && global && global.Object === Object && global;
	/** Detect free variable `self`. */
	var freeSelf = typeof self == "object" && self && self.Object === Object && self;
	/** Used as a reference to the global object. */
	var root = freeGlobal || freeSelf || Function("return this")();
	/** Detect free variable `exports`. */
	var freeExports = typeof exports == "object" && exports && !exports.nodeType && exports;
	/** Detect free variable `module`. */
	var freeModule = freeExports && typeof module == "object" && module && !module.nodeType && module;
	/** Detect the popular CommonJS extension `module.exports`. */
	var moduleExports = freeModule && freeModule.exports === freeExports;
	/** Detect free variable `process` from Node.js. */
	var freeProcess = moduleExports && freeGlobal.process;
	/** Used to access faster Node.js helpers. */
	var nodeUtil = function() {
		try {
			return freeProcess && freeProcess.binding && freeProcess.binding("util");
		} catch (e) {}
	}();
	var nodeIsTypedArray = nodeUtil && nodeUtil.isTypedArray;
	/**
	* A specialized version of `_.filter` for arrays without support for
	* iteratee shorthands.
	*
	* @private
	* @param {Array} [array] The array to iterate over.
	* @param {Function} predicate The function invoked per iteration.
	* @returns {Array} Returns the new filtered array.
	*/
	function arrayFilter(array, predicate) {
		var index = -1, length = array == null ? 0 : array.length, resIndex = 0, result = [];
		while (++index < length) {
			var value = array[index];
			if (predicate(value, index, array)) result[resIndex++] = value;
		}
		return result;
	}
	/**
	* Appends the elements of `values` to `array`.
	*
	* @private
	* @param {Array} array The array to modify.
	* @param {Array} values The values to append.
	* @returns {Array} Returns `array`.
	*/
	function arrayPush(array, values) {
		var index = -1, length = values.length, offset = array.length;
		while (++index < length) array[offset + index] = values[index];
		return array;
	}
	/**
	* A specialized version of `_.some` for arrays without support for iteratee
	* shorthands.
	*
	* @private
	* @param {Array} [array] The array to iterate over.
	* @param {Function} predicate The function invoked per iteration.
	* @returns {boolean} Returns `true` if any element passes the predicate check,
	*  else `false`.
	*/
	function arraySome(array, predicate) {
		var index = -1, length = array == null ? 0 : array.length;
		while (++index < length) if (predicate(array[index], index, array)) return true;
		return false;
	}
	/**
	* The base implementation of `_.times` without support for iteratee shorthands
	* or max array length checks.
	*
	* @private
	* @param {number} n The number of times to invoke `iteratee`.
	* @param {Function} iteratee The function invoked per iteration.
	* @returns {Array} Returns the array of results.
	*/
	function baseTimes(n, iteratee) {
		var index = -1, result = Array(n);
		while (++index < n) result[index] = iteratee(index);
		return result;
	}
	/**
	* The base implementation of `_.unary` without support for storing metadata.
	*
	* @private
	* @param {Function} func The function to cap arguments for.
	* @returns {Function} Returns the new capped function.
	*/
	function baseUnary(func) {
		return function(value) {
			return func(value);
		};
	}
	/**
	* Checks if a `cache` value for `key` exists.
	*
	* @private
	* @param {Object} cache The cache to query.
	* @param {string} key The key of the entry to check.
	* @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
	*/
	function cacheHas(cache, key) {
		return cache.has(key);
	}
	/**
	* Gets the value at `key` of `object`.
	*
	* @private
	* @param {Object} [object] The object to query.
	* @param {string} key The key of the property to get.
	* @returns {*} Returns the property value.
	*/
	function getValue(object, key) {
		return object == null ? void 0 : object[key];
	}
	/**
	* Converts `map` to its key-value pairs.
	*
	* @private
	* @param {Object} map The map to convert.
	* @returns {Array} Returns the key-value pairs.
	*/
	function mapToArray(map) {
		var index = -1, result = Array(map.size);
		map.forEach(function(value, key) {
			result[++index] = [key, value];
		});
		return result;
	}
	/**
	* Creates a unary function that invokes `func` with its argument transformed.
	*
	* @private
	* @param {Function} func The function to wrap.
	* @param {Function} transform The argument transform.
	* @returns {Function} Returns the new function.
	*/
	function overArg(func, transform) {
		return function(arg) {
			return func(transform(arg));
		};
	}
	/**
	* Converts `set` to an array of its values.
	*
	* @private
	* @param {Object} set The set to convert.
	* @returns {Array} Returns the values.
	*/
	function setToArray(set) {
		var index = -1, result = Array(set.size);
		set.forEach(function(value) {
			result[++index] = value;
		});
		return result;
	}
	/** Used for built-in method references. */
	var arrayProto = Array.prototype, funcProto = Function.prototype, objectProto = Object.prototype;
	/** Used to detect overreaching core-js shims. */
	var coreJsData = root["__core-js_shared__"];
	/** Used to resolve the decompiled source of functions. */
	var funcToString = funcProto.toString;
	/** Used to check objects for own properties. */
	var hasOwnProperty = objectProto.hasOwnProperty;
	/** Used to detect methods masquerading as native. */
	var maskSrcKey = function() {
		var uid = /[^.]+$/.exec(coreJsData && coreJsData.keys && coreJsData.keys.IE_PROTO || "");
		return uid ? "Symbol(src)_1." + uid : "";
	}();
	/**
	* Used to resolve the
	* [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
	* of values.
	*/
	var nativeObjectToString = objectProto.toString;
	/** Used to detect if a method is native. */
	var reIsNative = RegExp("^" + funcToString.call(hasOwnProperty).replace(reRegExpChar, "\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, "$1.*?") + "$");
	/** Built-in value references. */
	var Buffer = moduleExports ? root.Buffer : void 0, Symbol = root.Symbol, Uint8Array = root.Uint8Array, propertyIsEnumerable = objectProto.propertyIsEnumerable, splice = arrayProto.splice, symToStringTag = Symbol ? Symbol.toStringTag : void 0;
	var nativeGetSymbols = Object.getOwnPropertySymbols, nativeIsBuffer = Buffer ? Buffer.isBuffer : void 0, nativeKeys = overArg(Object.keys, Object);
	var DataView = getNative(root, "DataView"), Map = getNative(root, "Map"), Promise = getNative(root, "Promise"), Set = getNative(root, "Set"), WeakMap = getNative(root, "WeakMap"), nativeCreate = getNative(Object, "create");
	/** Used to detect maps, sets, and weakmaps. */
	var dataViewCtorString = toSource(DataView), mapCtorString = toSource(Map), promiseCtorString = toSource(Promise), setCtorString = toSource(Set), weakMapCtorString = toSource(WeakMap);
	/** Used to convert symbols to primitives and strings. */
	var symbolProto = Symbol ? Symbol.prototype : void 0, symbolValueOf = symbolProto ? symbolProto.valueOf : void 0;
	/**
	* Creates a hash object.
	*
	* @private
	* @constructor
	* @param {Array} [entries] The key-value pairs to cache.
	*/
	function Hash(entries) {
		var index = -1, length = entries == null ? 0 : entries.length;
		this.clear();
		while (++index < length) {
			var entry = entries[index];
			this.set(entry[0], entry[1]);
		}
	}
	/**
	* Removes all key-value entries from the hash.
	*
	* @private
	* @name clear
	* @memberOf Hash
	*/
	function hashClear() {
		this.__data__ = nativeCreate ? nativeCreate(null) : {};
		this.size = 0;
	}
	/**
	* Removes `key` and its value from the hash.
	*
	* @private
	* @name delete
	* @memberOf Hash
	* @param {Object} hash The hash to modify.
	* @param {string} key The key of the value to remove.
	* @returns {boolean} Returns `true` if the entry was removed, else `false`.
	*/
	function hashDelete(key) {
		var result = this.has(key) && delete this.__data__[key];
		this.size -= result ? 1 : 0;
		return result;
	}
	/**
	* Gets the hash value for `key`.
	*
	* @private
	* @name get
	* @memberOf Hash
	* @param {string} key The key of the value to get.
	* @returns {*} Returns the entry value.
	*/
	function hashGet(key) {
		var data = this.__data__;
		if (nativeCreate) {
			var result = data[key];
			return result === HASH_UNDEFINED ? void 0 : result;
		}
		return hasOwnProperty.call(data, key) ? data[key] : void 0;
	}
	/**
	* Checks if a hash value for `key` exists.
	*
	* @private
	* @name has
	* @memberOf Hash
	* @param {string} key The key of the entry to check.
	* @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
	*/
	function hashHas(key) {
		var data = this.__data__;
		return nativeCreate ? data[key] !== void 0 : hasOwnProperty.call(data, key);
	}
	/**
	* Sets the hash `key` to `value`.
	*
	* @private
	* @name set
	* @memberOf Hash
	* @param {string} key The key of the value to set.
	* @param {*} value The value to set.
	* @returns {Object} Returns the hash instance.
	*/
	function hashSet(key, value) {
		var data = this.__data__;
		this.size += this.has(key) ? 0 : 1;
		data[key] = nativeCreate && value === void 0 ? HASH_UNDEFINED : value;
		return this;
	}
	Hash.prototype.clear = hashClear;
	Hash.prototype["delete"] = hashDelete;
	Hash.prototype.get = hashGet;
	Hash.prototype.has = hashHas;
	Hash.prototype.set = hashSet;
	/**
	* Creates an list cache object.
	*
	* @private
	* @constructor
	* @param {Array} [entries] The key-value pairs to cache.
	*/
	function ListCache(entries) {
		var index = -1, length = entries == null ? 0 : entries.length;
		this.clear();
		while (++index < length) {
			var entry = entries[index];
			this.set(entry[0], entry[1]);
		}
	}
	/**
	* Removes all key-value entries from the list cache.
	*
	* @private
	* @name clear
	* @memberOf ListCache
	*/
	function listCacheClear() {
		this.__data__ = [];
		this.size = 0;
	}
	/**
	* Removes `key` and its value from the list cache.
	*
	* @private
	* @name delete
	* @memberOf ListCache
	* @param {string} key The key of the value to remove.
	* @returns {boolean} Returns `true` if the entry was removed, else `false`.
	*/
	function listCacheDelete(key) {
		var data = this.__data__, index = assocIndexOf(data, key);
		if (index < 0) return false;
		if (index == data.length - 1) data.pop();
		else splice.call(data, index, 1);
		--this.size;
		return true;
	}
	/**
	* Gets the list cache value for `key`.
	*
	* @private
	* @name get
	* @memberOf ListCache
	* @param {string} key The key of the value to get.
	* @returns {*} Returns the entry value.
	*/
	function listCacheGet(key) {
		var data = this.__data__, index = assocIndexOf(data, key);
		return index < 0 ? void 0 : data[index][1];
	}
	/**
	* Checks if a list cache value for `key` exists.
	*
	* @private
	* @name has
	* @memberOf ListCache
	* @param {string} key The key of the entry to check.
	* @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
	*/
	function listCacheHas(key) {
		return assocIndexOf(this.__data__, key) > -1;
	}
	/**
	* Sets the list cache `key` to `value`.
	*
	* @private
	* @name set
	* @memberOf ListCache
	* @param {string} key The key of the value to set.
	* @param {*} value The value to set.
	* @returns {Object} Returns the list cache instance.
	*/
	function listCacheSet(key, value) {
		var data = this.__data__, index = assocIndexOf(data, key);
		if (index < 0) {
			++this.size;
			data.push([key, value]);
		} else data[index][1] = value;
		return this;
	}
	ListCache.prototype.clear = listCacheClear;
	ListCache.prototype["delete"] = listCacheDelete;
	ListCache.prototype.get = listCacheGet;
	ListCache.prototype.has = listCacheHas;
	ListCache.prototype.set = listCacheSet;
	/**
	* Creates a map cache object to store key-value pairs.
	*
	* @private
	* @constructor
	* @param {Array} [entries] The key-value pairs to cache.
	*/
	function MapCache(entries) {
		var index = -1, length = entries == null ? 0 : entries.length;
		this.clear();
		while (++index < length) {
			var entry = entries[index];
			this.set(entry[0], entry[1]);
		}
	}
	/**
	* Removes all key-value entries from the map.
	*
	* @private
	* @name clear
	* @memberOf MapCache
	*/
	function mapCacheClear() {
		this.size = 0;
		this.__data__ = {
			"hash": new Hash(),
			"map": new (Map || ListCache)(),
			"string": new Hash()
		};
	}
	/**
	* Removes `key` and its value from the map.
	*
	* @private
	* @name delete
	* @memberOf MapCache
	* @param {string} key The key of the value to remove.
	* @returns {boolean} Returns `true` if the entry was removed, else `false`.
	*/
	function mapCacheDelete(key) {
		var result = getMapData(this, key)["delete"](key);
		this.size -= result ? 1 : 0;
		return result;
	}
	/**
	* Gets the map value for `key`.
	*
	* @private
	* @name get
	* @memberOf MapCache
	* @param {string} key The key of the value to get.
	* @returns {*} Returns the entry value.
	*/
	function mapCacheGet(key) {
		return getMapData(this, key).get(key);
	}
	/**
	* Checks if a map value for `key` exists.
	*
	* @private
	* @name has
	* @memberOf MapCache
	* @param {string} key The key of the entry to check.
	* @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
	*/
	function mapCacheHas(key) {
		return getMapData(this, key).has(key);
	}
	/**
	* Sets the map `key` to `value`.
	*
	* @private
	* @name set
	* @memberOf MapCache
	* @param {string} key The key of the value to set.
	* @param {*} value The value to set.
	* @returns {Object} Returns the map cache instance.
	*/
	function mapCacheSet(key, value) {
		var data = getMapData(this, key), size = data.size;
		data.set(key, value);
		this.size += data.size == size ? 0 : 1;
		return this;
	}
	MapCache.prototype.clear = mapCacheClear;
	MapCache.prototype["delete"] = mapCacheDelete;
	MapCache.prototype.get = mapCacheGet;
	MapCache.prototype.has = mapCacheHas;
	MapCache.prototype.set = mapCacheSet;
	/**
	*
	* Creates an array cache object to store unique values.
	*
	* @private
	* @constructor
	* @param {Array} [values] The values to cache.
	*/
	function SetCache(values) {
		var index = -1, length = values == null ? 0 : values.length;
		this.__data__ = new MapCache();
		while (++index < length) this.add(values[index]);
	}
	/**
	* Adds `value` to the array cache.
	*
	* @private
	* @name add
	* @memberOf SetCache
	* @alias push
	* @param {*} value The value to cache.
	* @returns {Object} Returns the cache instance.
	*/
	function setCacheAdd(value) {
		this.__data__.set(value, HASH_UNDEFINED);
		return this;
	}
	/**
	* Checks if `value` is in the array cache.
	*
	* @private
	* @name has
	* @memberOf SetCache
	* @param {*} value The value to search for.
	* @returns {number} Returns `true` if `value` is found, else `false`.
	*/
	function setCacheHas(value) {
		return this.__data__.has(value);
	}
	SetCache.prototype.add = SetCache.prototype.push = setCacheAdd;
	SetCache.prototype.has = setCacheHas;
	/**
	* Creates a stack cache object to store key-value pairs.
	*
	* @private
	* @constructor
	* @param {Array} [entries] The key-value pairs to cache.
	*/
	function Stack(entries) {
		var data = this.__data__ = new ListCache(entries);
		this.size = data.size;
	}
	/**
	* Removes all key-value entries from the stack.
	*
	* @private
	* @name clear
	* @memberOf Stack
	*/
	function stackClear() {
		this.__data__ = new ListCache();
		this.size = 0;
	}
	/**
	* Removes `key` and its value from the stack.
	*
	* @private
	* @name delete
	* @memberOf Stack
	* @param {string} key The key of the value to remove.
	* @returns {boolean} Returns `true` if the entry was removed, else `false`.
	*/
	function stackDelete(key) {
		var data = this.__data__, result = data["delete"](key);
		this.size = data.size;
		return result;
	}
	/**
	* Gets the stack value for `key`.
	*
	* @private
	* @name get
	* @memberOf Stack
	* @param {string} key The key of the value to get.
	* @returns {*} Returns the entry value.
	*/
	function stackGet(key) {
		return this.__data__.get(key);
	}
	/**
	* Checks if a stack value for `key` exists.
	*
	* @private
	* @name has
	* @memberOf Stack
	* @param {string} key The key of the entry to check.
	* @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
	*/
	function stackHas(key) {
		return this.__data__.has(key);
	}
	/**
	* Sets the stack `key` to `value`.
	*
	* @private
	* @name set
	* @memberOf Stack
	* @param {string} key The key of the value to set.
	* @param {*} value The value to set.
	* @returns {Object} Returns the stack cache instance.
	*/
	function stackSet(key, value) {
		var data = this.__data__;
		if (data instanceof ListCache) {
			var pairs = data.__data__;
			if (!Map || pairs.length < LARGE_ARRAY_SIZE - 1) {
				pairs.push([key, value]);
				this.size = ++data.size;
				return this;
			}
			data = this.__data__ = new MapCache(pairs);
		}
		data.set(key, value);
		this.size = data.size;
		return this;
	}
	Stack.prototype.clear = stackClear;
	Stack.prototype["delete"] = stackDelete;
	Stack.prototype.get = stackGet;
	Stack.prototype.has = stackHas;
	Stack.prototype.set = stackSet;
	/**
	* Creates an array of the enumerable property names of the array-like `value`.
	*
	* @private
	* @param {*} value The value to query.
	* @param {boolean} inherited Specify returning inherited property names.
	* @returns {Array} Returns the array of property names.
	*/
	function arrayLikeKeys(value, inherited) {
		var isArr = isArray(value), isArg = !isArr && isArguments(value), isBuff = !isArr && !isArg && isBuffer(value), isType = !isArr && !isArg && !isBuff && isTypedArray(value), skipIndexes = isArr || isArg || isBuff || isType, result = skipIndexes ? baseTimes(value.length, String) : [], length = result.length;
		for (var key in value) if ((inherited || hasOwnProperty.call(value, key)) && !(skipIndexes && (key == "length" || isBuff && (key == "offset" || key == "parent") || isType && (key == "buffer" || key == "byteLength" || key == "byteOffset") || isIndex(key, length)))) result.push(key);
		return result;
	}
	/**
	* Gets the index at which the `key` is found in `array` of key-value pairs.
	*
	* @private
	* @param {Array} array The array to inspect.
	* @param {*} key The key to search for.
	* @returns {number} Returns the index of the matched value, else `-1`.
	*/
	function assocIndexOf(array, key) {
		var length = array.length;
		while (length--) if (eq(array[length][0], key)) return length;
		return -1;
	}
	/**
	* The base implementation of `getAllKeys` and `getAllKeysIn` which uses
	* `keysFunc` and `symbolsFunc` to get the enumerable property names and
	* symbols of `object`.
	*
	* @private
	* @param {Object} object The object to query.
	* @param {Function} keysFunc The function to get the keys of `object`.
	* @param {Function} symbolsFunc The function to get the symbols of `object`.
	* @returns {Array} Returns the array of property names and symbols.
	*/
	function baseGetAllKeys(object, keysFunc, symbolsFunc) {
		var result = keysFunc(object);
		return isArray(object) ? result : arrayPush(result, symbolsFunc(object));
	}
	/**
	* The base implementation of `getTag` without fallbacks for buggy environments.
	*
	* @private
	* @param {*} value The value to query.
	* @returns {string} Returns the `toStringTag`.
	*/
	function baseGetTag(value) {
		if (value == null) return value === void 0 ? undefinedTag : nullTag;
		return symToStringTag && symToStringTag in Object(value) ? getRawTag(value) : objectToString(value);
	}
	/**
	* The base implementation of `_.isArguments`.
	*
	* @private
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is an `arguments` object,
	*/
	function baseIsArguments(value) {
		return isObjectLike(value) && baseGetTag(value) == argsTag;
	}
	/**
	* The base implementation of `_.isEqual` which supports partial comparisons
	* and tracks traversed objects.
	*
	* @private
	* @param {*} value The value to compare.
	* @param {*} other The other value to compare.
	* @param {boolean} bitmask The bitmask flags.
	*  1 - Unordered comparison
	*  2 - Partial comparison
	* @param {Function} [customizer] The function to customize comparisons.
	* @param {Object} [stack] Tracks traversed `value` and `other` objects.
	* @returns {boolean} Returns `true` if the values are equivalent, else `false`.
	*/
	function baseIsEqual(value, other, bitmask, customizer, stack) {
		if (value === other) return true;
		if (value == null || other == null || !isObjectLike(value) && !isObjectLike(other)) return value !== value && other !== other;
		return baseIsEqualDeep(value, other, bitmask, customizer, baseIsEqual, stack);
	}
	/**
	* A specialized version of `baseIsEqual` for arrays and objects which performs
	* deep comparisons and tracks traversed objects enabling objects with circular
	* references to be compared.
	*
	* @private
	* @param {Object} object The object to compare.
	* @param {Object} other The other object to compare.
	* @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
	* @param {Function} customizer The function to customize comparisons.
	* @param {Function} equalFunc The function to determine equivalents of values.
	* @param {Object} [stack] Tracks traversed `object` and `other` objects.
	* @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
	*/
	function baseIsEqualDeep(object, other, bitmask, customizer, equalFunc, stack) {
		var objIsArr = isArray(object), othIsArr = isArray(other), objTag = objIsArr ? arrayTag : getTag(object), othTag = othIsArr ? arrayTag : getTag(other);
		objTag = objTag == argsTag ? objectTag : objTag;
		othTag = othTag == argsTag ? objectTag : othTag;
		var objIsObj = objTag == objectTag, othIsObj = othTag == objectTag, isSameTag = objTag == othTag;
		if (isSameTag && isBuffer(object)) {
			if (!isBuffer(other)) return false;
			objIsArr = true;
			objIsObj = false;
		}
		if (isSameTag && !objIsObj) {
			stack || (stack = new Stack());
			return objIsArr || isTypedArray(object) ? equalArrays(object, other, bitmask, customizer, equalFunc, stack) : equalByTag(object, other, objTag, bitmask, customizer, equalFunc, stack);
		}
		if (!(bitmask & COMPARE_PARTIAL_FLAG)) {
			var objIsWrapped = objIsObj && hasOwnProperty.call(object, "__wrapped__"), othIsWrapped = othIsObj && hasOwnProperty.call(other, "__wrapped__");
			if (objIsWrapped || othIsWrapped) {
				var objUnwrapped = objIsWrapped ? object.value() : object, othUnwrapped = othIsWrapped ? other.value() : other;
				stack || (stack = new Stack());
				return equalFunc(objUnwrapped, othUnwrapped, bitmask, customizer, stack);
			}
		}
		if (!isSameTag) return false;
		stack || (stack = new Stack());
		return equalObjects(object, other, bitmask, customizer, equalFunc, stack);
	}
	/**
	* The base implementation of `_.isNative` without bad shim checks.
	*
	* @private
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is a native function,
	*  else `false`.
	*/
	function baseIsNative(value) {
		if (!isObject(value) || isMasked(value)) return false;
		return (isFunction(value) ? reIsNative : reIsHostCtor).test(toSource(value));
	}
	/**
	* The base implementation of `_.isTypedArray` without Node.js optimizations.
	*
	* @private
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
	*/
	function baseIsTypedArray(value) {
		return isObjectLike(value) && isLength(value.length) && !!typedArrayTags[baseGetTag(value)];
	}
	/**
	* The base implementation of `_.keys` which doesn't treat sparse arrays as dense.
	*
	* @private
	* @param {Object} object The object to query.
	* @returns {Array} Returns the array of property names.
	*/
	function baseKeys(object) {
		if (!isPrototype(object)) return nativeKeys(object);
		var result = [];
		for (var key in Object(object)) if (hasOwnProperty.call(object, key) && key != "constructor") result.push(key);
		return result;
	}
	/**
	* A specialized version of `baseIsEqualDeep` for arrays with support for
	* partial deep comparisons.
	*
	* @private
	* @param {Array} array The array to compare.
	* @param {Array} other The other array to compare.
	* @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
	* @param {Function} customizer The function to customize comparisons.
	* @param {Function} equalFunc The function to determine equivalents of values.
	* @param {Object} stack Tracks traversed `array` and `other` objects.
	* @returns {boolean} Returns `true` if the arrays are equivalent, else `false`.
	*/
	function equalArrays(array, other, bitmask, customizer, equalFunc, stack) {
		var isPartial = bitmask & COMPARE_PARTIAL_FLAG, arrLength = array.length, othLength = other.length;
		if (arrLength != othLength && !(isPartial && othLength > arrLength)) return false;
		var stacked = stack.get(array);
		if (stacked && stack.get(other)) return stacked == other;
		var index = -1, result = true, seen = bitmask & COMPARE_UNORDERED_FLAG ? new SetCache() : void 0;
		stack.set(array, other);
		stack.set(other, array);
		while (++index < arrLength) {
			var arrValue = array[index], othValue = other[index];
			if (customizer) var compared = isPartial ? customizer(othValue, arrValue, index, other, array, stack) : customizer(arrValue, othValue, index, array, other, stack);
			if (compared !== void 0) {
				if (compared) continue;
				result = false;
				break;
			}
			if (seen) {
				if (!arraySome(other, function(othValue, othIndex) {
					if (!cacheHas(seen, othIndex) && (arrValue === othValue || equalFunc(arrValue, othValue, bitmask, customizer, stack))) return seen.push(othIndex);
				})) {
					result = false;
					break;
				}
			} else if (!(arrValue === othValue || equalFunc(arrValue, othValue, bitmask, customizer, stack))) {
				result = false;
				break;
			}
		}
		stack["delete"](array);
		stack["delete"](other);
		return result;
	}
	/**
	* A specialized version of `baseIsEqualDeep` for comparing objects of
	* the same `toStringTag`.
	*
	* **Note:** This function only supports comparing values with tags of
	* `Boolean`, `Date`, `Error`, `Number`, `RegExp`, or `String`.
	*
	* @private
	* @param {Object} object The object to compare.
	* @param {Object} other The other object to compare.
	* @param {string} tag The `toStringTag` of the objects to compare.
	* @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
	* @param {Function} customizer The function to customize comparisons.
	* @param {Function} equalFunc The function to determine equivalents of values.
	* @param {Object} stack Tracks traversed `object` and `other` objects.
	* @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
	*/
	function equalByTag(object, other, tag, bitmask, customizer, equalFunc, stack) {
		switch (tag) {
			case dataViewTag:
				if (object.byteLength != other.byteLength || object.byteOffset != other.byteOffset) return false;
				object = object.buffer;
				other = other.buffer;
			case arrayBufferTag:
				if (object.byteLength != other.byteLength || !equalFunc(new Uint8Array(object), new Uint8Array(other))) return false;
				return true;
			case boolTag:
			case dateTag:
			case numberTag: return eq(+object, +other);
			case errorTag: return object.name == other.name && object.message == other.message;
			case regexpTag:
			case stringTag: return object == other + "";
			case mapTag: var convert = mapToArray;
			case setTag:
				var isPartial = bitmask & COMPARE_PARTIAL_FLAG;
				convert || (convert = setToArray);
				if (object.size != other.size && !isPartial) return false;
				var stacked = stack.get(object);
				if (stacked) return stacked == other;
				bitmask |= COMPARE_UNORDERED_FLAG;
				stack.set(object, other);
				var result = equalArrays(convert(object), convert(other), bitmask, customizer, equalFunc, stack);
				stack["delete"](object);
				return result;
			case symbolTag: if (symbolValueOf) return symbolValueOf.call(object) == symbolValueOf.call(other);
		}
		return false;
	}
	/**
	* A specialized version of `baseIsEqualDeep` for objects with support for
	* partial deep comparisons.
	*
	* @private
	* @param {Object} object The object to compare.
	* @param {Object} other The other object to compare.
	* @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
	* @param {Function} customizer The function to customize comparisons.
	* @param {Function} equalFunc The function to determine equivalents of values.
	* @param {Object} stack Tracks traversed `object` and `other` objects.
	* @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
	*/
	function equalObjects(object, other, bitmask, customizer, equalFunc, stack) {
		var isPartial = bitmask & COMPARE_PARTIAL_FLAG, objProps = getAllKeys(object), objLength = objProps.length;
		if (objLength != getAllKeys(other).length && !isPartial) return false;
		var index = objLength;
		while (index--) {
			var key = objProps[index];
			if (!(isPartial ? key in other : hasOwnProperty.call(other, key))) return false;
		}
		var stacked = stack.get(object);
		if (stacked && stack.get(other)) return stacked == other;
		var result = true;
		stack.set(object, other);
		stack.set(other, object);
		var skipCtor = isPartial;
		while (++index < objLength) {
			key = objProps[index];
			var objValue = object[key], othValue = other[key];
			if (customizer) var compared = isPartial ? customizer(othValue, objValue, key, other, object, stack) : customizer(objValue, othValue, key, object, other, stack);
			if (!(compared === void 0 ? objValue === othValue || equalFunc(objValue, othValue, bitmask, customizer, stack) : compared)) {
				result = false;
				break;
			}
			skipCtor || (skipCtor = key == "constructor");
		}
		if (result && !skipCtor) {
			var objCtor = object.constructor, othCtor = other.constructor;
			if (objCtor != othCtor && "constructor" in object && "constructor" in other && !(typeof objCtor == "function" && objCtor instanceof objCtor && typeof othCtor == "function" && othCtor instanceof othCtor)) result = false;
		}
		stack["delete"](object);
		stack["delete"](other);
		return result;
	}
	/**
	* Creates an array of own enumerable property names and symbols of `object`.
	*
	* @private
	* @param {Object} object The object to query.
	* @returns {Array} Returns the array of property names and symbols.
	*/
	function getAllKeys(object) {
		return baseGetAllKeys(object, keys, getSymbols);
	}
	/**
	* Gets the data for `map`.
	*
	* @private
	* @param {Object} map The map to query.
	* @param {string} key The reference key.
	* @returns {*} Returns the map data.
	*/
	function getMapData(map, key) {
		var data = map.__data__;
		return isKeyable(key) ? data[typeof key == "string" ? "string" : "hash"] : data.map;
	}
	/**
	* Gets the native function at `key` of `object`.
	*
	* @private
	* @param {Object} object The object to query.
	* @param {string} key The key of the method to get.
	* @returns {*} Returns the function if it's native, else `undefined`.
	*/
	function getNative(object, key) {
		var value = getValue(object, key);
		return baseIsNative(value) ? value : void 0;
	}
	/**
	* A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
	*
	* @private
	* @param {*} value The value to query.
	* @returns {string} Returns the raw `toStringTag`.
	*/
	function getRawTag(value) {
		var isOwn = hasOwnProperty.call(value, symToStringTag), tag = value[symToStringTag];
		try {
			value[symToStringTag] = void 0;
			var unmasked = true;
		} catch (e) {}
		var result = nativeObjectToString.call(value);
		if (unmasked) if (isOwn) value[symToStringTag] = tag;
		else delete value[symToStringTag];
		return result;
	}
	/**
	* Creates an array of the own enumerable symbols of `object`.
	*
	* @private
	* @param {Object} object The object to query.
	* @returns {Array} Returns the array of symbols.
	*/
	var getSymbols = !nativeGetSymbols ? stubArray : function(object) {
		if (object == null) return [];
		object = Object(object);
		return arrayFilter(nativeGetSymbols(object), function(symbol) {
			return propertyIsEnumerable.call(object, symbol);
		});
	};
	/**
	* Gets the `toStringTag` of `value`.
	*
	* @private
	* @param {*} value The value to query.
	* @returns {string} Returns the `toStringTag`.
	*/
	var getTag = baseGetTag;
	if (DataView && getTag(new DataView(/* @__PURE__ */ new ArrayBuffer(1))) != dataViewTag || Map && getTag(new Map()) != mapTag || Promise && getTag(Promise.resolve()) != promiseTag || Set && getTag(new Set()) != setTag || WeakMap && getTag(new WeakMap()) != weakMapTag) getTag = function(value) {
		var result = baseGetTag(value), Ctor = result == objectTag ? value.constructor : void 0, ctorString = Ctor ? toSource(Ctor) : "";
		if (ctorString) switch (ctorString) {
			case dataViewCtorString: return dataViewTag;
			case mapCtorString: return mapTag;
			case promiseCtorString: return promiseTag;
			case setCtorString: return setTag;
			case weakMapCtorString: return weakMapTag;
		}
		return result;
	};
	/**
	* Checks if `value` is a valid array-like index.
	*
	* @private
	* @param {*} value The value to check.
	* @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
	* @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
	*/
	function isIndex(value, length) {
		length = length == null ? MAX_SAFE_INTEGER : length;
		return !!length && (typeof value == "number" || reIsUint.test(value)) && value > -1 && value % 1 == 0 && value < length;
	}
	/**
	* Checks if `value` is suitable for use as unique object key.
	*
	* @private
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is suitable, else `false`.
	*/
	function isKeyable(value) {
		var type = typeof value;
		return type == "string" || type == "number" || type == "symbol" || type == "boolean" ? value !== "__proto__" : value === null;
	}
	/**
	* Checks if `func` has its source masked.
	*
	* @private
	* @param {Function} func The function to check.
	* @returns {boolean} Returns `true` if `func` is masked, else `false`.
	*/
	function isMasked(func) {
		return !!maskSrcKey && maskSrcKey in func;
	}
	/**
	* Checks if `value` is likely a prototype object.
	*
	* @private
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is a prototype, else `false`.
	*/
	function isPrototype(value) {
		var Ctor = value && value.constructor;
		return value === (typeof Ctor == "function" && Ctor.prototype || objectProto);
	}
	/**
	* Converts `value` to a string using `Object.prototype.toString`.
	*
	* @private
	* @param {*} value The value to convert.
	* @returns {string} Returns the converted string.
	*/
	function objectToString(value) {
		return nativeObjectToString.call(value);
	}
	/**
	* Converts `func` to its source code.
	*
	* @private
	* @param {Function} func The function to convert.
	* @returns {string} Returns the source code.
	*/
	function toSource(func) {
		if (func != null) {
			try {
				return funcToString.call(func);
			} catch (e) {}
			try {
				return func + "";
			} catch (e) {}
		}
		return "";
	}
	/**
	* Performs a
	* [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
	* comparison between two values to determine if they are equivalent.
	*
	* @static
	* @memberOf _
	* @since 4.0.0
	* @category Lang
	* @param {*} value The value to compare.
	* @param {*} other The other value to compare.
	* @returns {boolean} Returns `true` if the values are equivalent, else `false`.
	* @example
	*
	* var object = { 'a': 1 };
	* var other = { 'a': 1 };
	*
	* _.eq(object, object);
	* // => true
	*
	* _.eq(object, other);
	* // => false
	*
	* _.eq('a', 'a');
	* // => true
	*
	* _.eq('a', Object('a'));
	* // => false
	*
	* _.eq(NaN, NaN);
	* // => true
	*/
	function eq(value, other) {
		return value === other || value !== value && other !== other;
	}
	/**
	* Checks if `value` is likely an `arguments` object.
	*
	* @static
	* @memberOf _
	* @since 0.1.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is an `arguments` object,
	*  else `false`.
	* @example
	*
	* _.isArguments(function() { return arguments; }());
	* // => true
	*
	* _.isArguments([1, 2, 3]);
	* // => false
	*/
	var isArguments = baseIsArguments(function() {
		return arguments;
	}()) ? baseIsArguments : function(value) {
		return isObjectLike(value) && hasOwnProperty.call(value, "callee") && !propertyIsEnumerable.call(value, "callee");
	};
	/**
	* Checks if `value` is classified as an `Array` object.
	*
	* @static
	* @memberOf _
	* @since 0.1.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is an array, else `false`.
	* @example
	*
	* _.isArray([1, 2, 3]);
	* // => true
	*
	* _.isArray(document.body.children);
	* // => false
	*
	* _.isArray('abc');
	* // => false
	*
	* _.isArray(_.noop);
	* // => false
	*/
	var isArray = Array.isArray;
	/**
	* Checks if `value` is array-like. A value is considered array-like if it's
	* not a function and has a `value.length` that's an integer greater than or
	* equal to `0` and less than or equal to `Number.MAX_SAFE_INTEGER`.
	*
	* @static
	* @memberOf _
	* @since 4.0.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is array-like, else `false`.
	* @example
	*
	* _.isArrayLike([1, 2, 3]);
	* // => true
	*
	* _.isArrayLike(document.body.children);
	* // => true
	*
	* _.isArrayLike('abc');
	* // => true
	*
	* _.isArrayLike(_.noop);
	* // => false
	*/
	function isArrayLike(value) {
		return value != null && isLength(value.length) && !isFunction(value);
	}
	/**
	* Checks if `value` is a buffer.
	*
	* @static
	* @memberOf _
	* @since 4.3.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is a buffer, else `false`.
	* @example
	*
	* _.isBuffer(new Buffer(2));
	* // => true
	*
	* _.isBuffer(new Uint8Array(2));
	* // => false
	*/
	var isBuffer = nativeIsBuffer || stubFalse;
	/**
	* Performs a deep comparison between two values to determine if they are
	* equivalent.
	*
	* **Note:** This method supports comparing arrays, array buffers, booleans,
	* date objects, error objects, maps, numbers, `Object` objects, regexes,
	* sets, strings, symbols, and typed arrays. `Object` objects are compared
	* by their own, not inherited, enumerable properties. Functions and DOM
	* nodes are compared by strict equality, i.e. `===`.
	*
	* @static
	* @memberOf _
	* @since 0.1.0
	* @category Lang
	* @param {*} value The value to compare.
	* @param {*} other The other value to compare.
	* @returns {boolean} Returns `true` if the values are equivalent, else `false`.
	* @example
	*
	* var object = { 'a': 1 };
	* var other = { 'a': 1 };
	*
	* _.isEqual(object, other);
	* // => true
	*
	* object === other;
	* // => false
	*/
	function isEqual(value, other) {
		return baseIsEqual(value, other);
	}
	/**
	* Checks if `value` is classified as a `Function` object.
	*
	* @static
	* @memberOf _
	* @since 0.1.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is a function, else `false`.
	* @example
	*
	* _.isFunction(_);
	* // => true
	*
	* _.isFunction(/abc/);
	* // => false
	*/
	function isFunction(value) {
		if (!isObject(value)) return false;
		var tag = baseGetTag(value);
		return tag == funcTag || tag == genTag || tag == asyncTag || tag == proxyTag;
	}
	/**
	* Checks if `value` is a valid array-like length.
	*
	* **Note:** This method is loosely based on
	* [`ToLength`](http://ecma-international.org/ecma-262/7.0/#sec-tolength).
	*
	* @static
	* @memberOf _
	* @since 4.0.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
	* @example
	*
	* _.isLength(3);
	* // => true
	*
	* _.isLength(Number.MIN_VALUE);
	* // => false
	*
	* _.isLength(Infinity);
	* // => false
	*
	* _.isLength('3');
	* // => false
	*/
	function isLength(value) {
		return typeof value == "number" && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
	}
	/**
	* Checks if `value` is the
	* [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
	* of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
	*
	* @static
	* @memberOf _
	* @since 0.1.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is an object, else `false`.
	* @example
	*
	* _.isObject({});
	* // => true
	*
	* _.isObject([1, 2, 3]);
	* // => true
	*
	* _.isObject(_.noop);
	* // => true
	*
	* _.isObject(null);
	* // => false
	*/
	function isObject(value) {
		var type = typeof value;
		return value != null && (type == "object" || type == "function");
	}
	/**
	* Checks if `value` is object-like. A value is object-like if it's not `null`
	* and has a `typeof` result of "object".
	*
	* @static
	* @memberOf _
	* @since 4.0.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is object-like, else `false`.
	* @example
	*
	* _.isObjectLike({});
	* // => true
	*
	* _.isObjectLike([1, 2, 3]);
	* // => true
	*
	* _.isObjectLike(_.noop);
	* // => false
	*
	* _.isObjectLike(null);
	* // => false
	*/
	function isObjectLike(value) {
		return value != null && typeof value == "object";
	}
	/**
	* Checks if `value` is classified as a typed array.
	*
	* @static
	* @memberOf _
	* @since 3.0.0
	* @category Lang
	* @param {*} value The value to check.
	* @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
	* @example
	*
	* _.isTypedArray(new Uint8Array);
	* // => true
	*
	* _.isTypedArray([]);
	* // => false
	*/
	var isTypedArray = nodeIsTypedArray ? baseUnary(nodeIsTypedArray) : baseIsTypedArray;
	/**
	* Creates an array of the own enumerable property names of `object`.
	*
	* **Note:** Non-object values are coerced to objects. See the
	* [ES spec](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
	* for more details.
	*
	* @static
	* @since 0.1.0
	* @memberOf _
	* @category Object
	* @param {Object} object The object to query.
	* @returns {Array} Returns the array of property names.
	* @example
	*
	* function Foo() {
	*   this.a = 1;
	*   this.b = 2;
	* }
	*
	* Foo.prototype.c = 3;
	*
	* _.keys(new Foo);
	* // => ['a', 'b'] (iteration order is not guaranteed)
	*
	* _.keys('hi');
	* // => ['0', '1']
	*/
	function keys(object) {
		return isArrayLike(object) ? arrayLikeKeys(object) : baseKeys(object);
	}
	/**
	* This method returns a new empty array.
	*
	* @static
	* @memberOf _
	* @since 4.13.0
	* @category Util
	* @returns {Array} Returns the new empty array.
	* @example
	*
	* var arrays = _.times(2, _.stubArray);
	*
	* console.log(arrays);
	* // => [[], []]
	*
	* console.log(arrays[0] === arrays[1]);
	* // => false
	*/
	function stubArray() {
		return [];
	}
	/**
	* This method returns `false`.
	*
	* @static
	* @memberOf _
	* @since 4.13.0
	* @category Util
	* @returns {boolean} Returns `false`.
	* @example
	*
	* _.times(2, _.stubFalse);
	* // => [false, false]
	*/
	function stubFalse() {
		return false;
	}
	module.exports = isEqual;
}));
//#endregion
//#region node_modules/@inertiajs/react/dist/index.esm.js
var import_react = /* @__PURE__ */ __toESM(require_react(), 1);
var import_lodash_isequal = /* @__PURE__ */ __toESM(require_lodash_isequal(), 1);
var V = (0, import_react.createContext)(void 0);
V.displayName = "InertiaHeadContext";
var M = V;
var K = (0, import_react.createContext)(void 0);
K.displayName = "InertiaPageContext";
var H = K;
function N({ children: l, initialPage: p, initialComponent: a, resolveComponent: i, titleCallback: n, onHeadUpdate: F }) {
	let [d, y] = (0, import_react.useState)({
		component: a || null,
		page: p,
		key: null
	}), u = (0, import_react.useMemo)(() => ee(typeof window > "u", n || ((c) => c), F || (() => {})), []);
	if ((0, import_react.useEffect)(() => {
		Ne.init({
			initialPage: p,
			resolveComponent: i,
			swapComponent: async ({ component: c, page: e, preserveState: s }) => {
				y((r) => ({
					component: c,
					page: e,
					key: s ? r.key : Date.now()
				}));
			}
		}), Ne.on("navigate", () => u.forceUpdate());
	}, []), !d.component) return (0, import_react.createElement)(M.Provider, { value: u }, (0, import_react.createElement)(H.Provider, { value: d.page }, null));
	let f = l || (({ Component: c, props: e, key: s }) => {
		let r = (0, import_react.createElement)(c, {
			key: s,
			...e
		});
		return typeof c.layout == "function" ? c.layout(r) : Array.isArray(c.layout) ? c.layout.concat(r).reverse().reduce((g, T) => (0, import_react.createElement)(T, {
			children: g,
			...e
		})) : r;
	});
	return (0, import_react.createElement)(M.Provider, { value: u }, (0, import_react.createElement)(H.Provider, { value: d.page }, f({
		Component: d.component,
		key: d.key,
		props: d.page.props
	})));
}
N.displayName = "Inertia";
async function W({ id: l = "app", resolve: p, setup: a, title: i, progress: n = {}, page: F, render: d }) {
	let y = typeof window > "u", u = y ? null : document.getElementById(l), f = F || JSON.parse(u.dataset.page), c = (r) => Promise.resolve(p(r)).then((g) => g.default || g), e = [], s = await c(f.component).then((r) => a({
		el: u,
		App: N,
		props: {
			initialPage: f,
			initialComponent: r,
			resolveComponent: c,
			titleCallback: i,
			onHeadUpdate: y ? (g) => e = g : null
		}
	}));
	if (!y && n && ie$1(n), y) {
		let r = await d((0, import_react.createElement)("div", {
			id: l,
			"data-page": JSON.stringify(f)
		}, s));
		return {
			head: e,
			body: r
		};
	}
}
var Te = function({ children: l, title: p }) {
	let a = (0, import_react.useContext)(M), i = (0, import_react.useMemo)(() => a.createProvider(), [a]);
	(0, import_react.useEffect)(() => () => {
		i.disconnect();
	}, [i]);
	function n(e) {
		return [
			"area",
			"base",
			"br",
			"col",
			"embed",
			"hr",
			"img",
			"input",
			"keygen",
			"link",
			"meta",
			"param",
			"source",
			"track",
			"wbr"
		].indexOf(e.type) > -1;
	}
	function F(e) {
		let s = Object.keys(e.props).reduce((r, g) => {
			if ([
				"head-key",
				"children",
				"dangerouslySetInnerHTML"
			].includes(g)) return r;
			let T = e.props[g];
			return T === "" ? r + ` ${g}` : r + ` ${g}="${T}"`;
		}, "");
		return `<${e.type}${s}>`;
	}
	function d(e) {
		return typeof e.props.children == "string" ? e.props.children : e.props.children.reduce((s, r) => s + y(r), "");
	}
	function y(e) {
		let s = F(e);
		return e.props.children && (s += d(e)), e.props.dangerouslySetInnerHTML && (s += e.props.dangerouslySetInnerHTML.__html), n(e) || (s += `</${e.type}>`), s;
	}
	function u(e) {
		return import_react.cloneElement(e, { inertia: e.props["head-key"] !== void 0 ? e.props["head-key"] : "" });
	}
	function f(e) {
		return y(u(e));
	}
	function c(e) {
		let s = import_react.Children.toArray(e).filter((r) => r).map((r) => f(r));
		return p && !s.find((r) => r.startsWith("<title")) && s.push(`<title inertia>${p}</title>`), s;
	}
	return i.update(c(l)), null;
}, Se = Te;
var C = () => {}, J = (0, import_react.forwardRef)(({ children: l, as: p = "a", data: a = {}, href: i, method: n = "get", preserveScroll: F = !1, preserveState: d = null, replace: y = !1, only: u = [], except: f = [], headers: c = {}, queryStringArrayFormat: e = "brackets", onClick: s = C, onCancelToken: r = C, onBefore: g = C, onStart: T = C, onProgress: O = C, onFinish: k = C, onCancel: L = C, onSuccess: R = C, onError: B = C, ...I }, A) => {
	let v = (0, import_react.useCallback)((E) => {
		s(E), re(E) && (E.preventDefault(), Ne.visit(i, {
			data: a,
			method: n,
			preserveScroll: F,
			preserveState: d ?? n !== "get",
			replace: y,
			only: u,
			except: f,
			headers: c,
			onCancelToken: r,
			onBefore: g,
			onStart: T,
			onProgress: O,
			onFinish: k,
			onCancel: L,
			onSuccess: R,
			onError: B
		}));
	}, [
		a,
		i,
		n,
		F,
		d,
		y,
		u,
		f,
		c,
		s,
		r,
		g,
		T,
		O,
		k,
		L,
		R,
		B
	]);
	p = p.toLowerCase(), n = n.toLowerCase();
	let [j, U] = D(n, i || "", a, e);
	return i = j, a = U, p === "a" && n !== "get" && console.warn(`Creating POST/PUT/PATCH/DELETE <a> links is discouraged as it causes "Open Link in New Tab/Window" accessibility issues.

Please specify a more appropriate element using the "as" attribute. For example:

<Link href="${i}" method="${n}" as="button">...</Link>`), (0, import_react.createElement)(p, {
		...I,
		...p === "a" ? { href: i } : {},
		ref: A,
		onClick: v
	}, l);
});
J.displayName = "InertiaLink";
var xe = J;
function w(l, p) {
	let [a, i] = (0, import_react.useState)(() => {
		let n = Ne.restore(p);
		return n !== void 0 ? n : l;
	});
	return (0, import_react.useEffect)(() => {
		Ne.remember(a, p);
	}, [a, p]), [a, i];
}
function G(l, p) {
	let a = (0, import_react.useRef)(null), i = typeof l == "string" ? l : null, [n, F] = (0, import_react.useState)((typeof l == "string" ? p : l) || {}), d = (0, import_react.useRef)(null), y = (0, import_react.useRef)(null), [u, f] = i ? w(n, `${i}:data`) : (0, import_react.useState)(n), [c, e] = i ? w({}, `${i}:errors`) : (0, import_react.useState)({}), [s, r] = (0, import_react.useState)(!1), [g, T] = (0, import_react.useState)(!1), [O, k] = (0, import_react.useState)(null), [L, R] = (0, import_react.useState)(!1), [B, I] = (0, import_react.useState)(!1), A = (0, import_react.useRef)((o) => o);
	(0, import_react.useEffect)(() => (a.current = !0, () => {
		a.current = !1;
	}), []);
	let v = (0, import_react.useCallback)((o, P, t = {}) => {
		let h = {
			...t,
			onCancelToken: (m) => {
				if (d.current = m, t.onCancelToken) return t.onCancelToken(m);
			},
			onBefore: (m) => {
				if (R(!1), I(!1), clearTimeout(y.current), t.onBefore) return t.onBefore(m);
			},
			onStart: (m) => {
				if (T(!0), t.onStart) return t.onStart(m);
			},
			onProgress: (m) => {
				if (k(m), t.onProgress) return t.onProgress(m);
			},
			onSuccess: (m) => {
				if (a.current && (T(!1), k(null), e({}), r(!1), R(!0), I(!0), y.current = setTimeout(() => {
					a.current && I(!1);
				}, 2e3)), t.onSuccess) return t.onSuccess(m);
			},
			onError: (m) => {
				if (a.current && (T(!1), k(null), e(m), r(!0)), t.onError) return t.onError(m);
			},
			onCancel: () => {
				if (a.current && (T(!1), k(null)), t.onCancel) return t.onCancel();
			},
			onFinish: () => {
				if (a.current && (T(!1), k(null)), d.current = null, t.onFinish) return t.onFinish();
			}
		};
		o === "delete" ? Ne.delete(P, {
			...h,
			data: A.current(u)
		}) : Ne[o](P, A.current(u), h);
	}, [u, e]), j = (0, import_react.useCallback)((o, P) => {
		f(typeof o == "string" ? (t) => ({
			...t,
			[o]: P
		}) : typeof o == "function" ? (t) => o(t) : o);
	}, [f]), U = (0, import_react.useCallback)((o, P) => {
		F(typeof o > "u" ? () => u : (t) => ({
			...t,
			...typeof o == "string" ? { [o]: P } : o
		}));
	}, [u, F]), E = (0, import_react.useCallback)((...o) => {
		o.length === 0 ? f(n) : f((P) => Object.keys(n).filter((t) => o.includes(t)).reduce((t, h) => (t[h] = n[h], t), { ...P }));
	}, [f, n]), Y = (0, import_react.useCallback)((o, P) => {
		e((t) => {
			let h = {
				...t,
				...typeof o == "string" ? { [o]: P } : o
			};
			return r(Object.keys(h).length > 0), h;
		});
	}, [e, r]), Z = (0, import_react.useCallback)((...o) => {
		e((P) => {
			let t = Object.keys(P).reduce((h, m) => ({
				...h,
				...o.length > 0 && !o.includes(m) ? { [m]: P[m] } : {}
			}), {});
			return r(Object.keys(t).length > 0), t;
		});
	}, [e, r]), D = (o) => (P, t) => {
		v(o, P, t);
	}, ee = (0, import_react.useCallback)(D("get"), [v]), te = (0, import_react.useCallback)(D("post"), [v]), re = (0, import_react.useCallback)(D("put"), [v]), oe = (0, import_react.useCallback)(D("patch"), [v]), ne = (0, import_react.useCallback)(D("delete"), [v]), ae = (0, import_react.useCallback)(() => {
		d.current && d.current.cancel();
	}, []), se = (0, import_react.useCallback)((o) => {
		A.current = o;
	}, []);
	return {
		data: u,
		setData: j,
		isDirty: !(0, import_lodash_isequal.default)(u, n),
		errors: c,
		hasErrors: s,
		processing: g,
		progress: O,
		wasSuccessful: L,
		recentlySuccessful: B,
		transform: se,
		setDefaults: U,
		reset: E,
		setError: Y,
		clearErrors: Z,
		submit: v,
		get: ee,
		post: te,
		put: re,
		patch: oe,
		delete: ne,
		cancel: ae
	};
}
function X() {
	let l = (0, import_react.useContext)(H);
	if (!l) throw new Error("usePage must be used within the Inertia component");
	return l;
}
var At = Ne;
//#endregion
export { Se as Head, xe as Link, W as createInertiaApp, At as router, G as useForm, X as usePage, w as useRemember };

//# sourceMappingURL=@inertiajs_react.js.map