! function(t) {
    if ("object" == typeof exports && "undefined" != typeof module) module.exports = t();
    else if ("function" == typeof define && define.amd) define([], t);
    else {
        ("undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : this).RisiBank = t()
    }
}(function() {
    return function a(o, s, l) {
        function c(e, t) {
            if (!s[e]) {
                if (!o[e]) {
                    var n = "function" == typeof require && require;
                    if (!t && n) return n(e, !0);
                    if (u) return u(e, !0);
                    var i = new Error("Cannot find module '" + e + "'");
                    throw i.code = "MODULE_NOT_FOUND", i
                }
                var r = s[e] = {
                    exports: {}
                };
                o[e][0].call(r.exports, function(t) {
                    return c(o[e][1][t] || t)
                }, r, r.exports, a, o, s, l)
            }
            return s[e].exports
        }
        for (var u = "function" == typeof require && require, t = 0; t < l.length; t++) c(l[t]);
        return c
    }({
        1: [function(t, e, n) {
            "use strict";
            var f = this && this.__awaiter || function(a, o, s, l) {
                    return new(s || (s = Promise))(function(t, e) {
                        function n(t) {
                            try {
                                r(l.next(t))
                            } catch (t) {
                                e(t)
                            }
                        }

                        function i(t) {
                            try {
                                r(l.throw(t))
                            } catch (t) {
                                e(t)
                            }
                        }

                        function r(e) {
                            e.done ? t(e.value) : new s(function(t) {
                                t(e.value)
                            }).then(n, i)
                        }
                        r((l = l.apply(a, o || [])).next())
                    })
                },
                d = this && this.__generator || function(n, i) {
                    var r, a, o, t, s = {
                        label: 0,
                        sent: function() {
                            if (1 & o[0]) throw o[1];
                            return o[1]
                        },
                        trys: [],
                        ops: []
                    };
                    return t = {
                        next: e(0),
                        throw: e(1),
                        return: e(2)
                    }, "function" == typeof Symbol && (t[Symbol.iterator] = function() {
                        return this
                    }), t;

                    function e(e) {
                        return function(t) {
                            return function(e) {
                                if (r) throw new TypeError("Generator is already executing.");
                                for (; s;) try {
                                    if (r = 1, a && (o = 2 & e[0] ? a.return : e[0] ? a.throw || ((o = a.return) && o.call(a), 0) : a.next) && !(o = o.call(a, e[1])).done) return o;
                                    switch (a = 0, o && (e = [2 & e[0], o.value]), e[0]) {
                                        case 0:
                                        case 1:
                                            o = e;
                                            break;
                                        case 4:
                                            return s.label++, {
                                                value: e[1],
                                                done: !1
                                            };
                                        case 5:
                                            s.label++, a = e[1], e = [0];
                                            continue;
                                        case 7:
                                            e = s.ops.pop(), s.trys.pop();
                                            continue;
                                        default:
                                            if (!(o = 0 < (o = s.trys).length && o[o.length - 1]) && (6 === e[0] || 2 === e[0])) {
                                                s = 0;
                                                continue
                                            }
                                            if (3 === e[0] && (!o || e[1] > o[0] && e[1] < o[3])) {
                                                s.label = e[1];
                                                break
                                            }
                                            if (6 === e[0] && s.label < o[1]) {
                                                s.label = o[1], o = e;
                                                break
                                            }
                                            if (o && s.label < o[2]) {
                                                s.label = o[2], s.ops.push(e);
                                                break
                                            }
                                            o[2] && s.ops.pop(), s.trys.pop();
                                            continue
                                    }
                                    e = i.call(n, s)
                                } catch (t) {
                                    e = [6, t], a = 0
                                } finally {
                                    r = o = 0
                                }
                                if (5 & e[0]) throw e[1];
                                return {
                                    value: e[0] ? e[1] : void 0,
                                    done: !0
                                }
                            }([e, t])
                        }
                    }
                },
                v = this && this.__values || function(t) {
                    var e = "function" == typeof Symbol && t[Symbol.iterator],
                        n = 0;
                    return e ? e.call(t) : {
                        next: function() {
                            return t && n >= t.length && (t = void 0), {
                                value: t && t[n++],
                                done: !t
                            }
                        }
                    }
                };
            Object.defineProperty(n, "__esModule", {
                value: !0
            });
            var i = t("./View"),
                r = t("./Model"),
                a = function() {
                    function t() {
                        this.view = new i.View(this), this.model = new r.Model(this), this.target = function() {}
                    }
                    return t.prototype.getDefaultCallback = function(e) {
                        var n = this;
                        return function(t) {
                            console.log(t + " has been selected"), n.view.hide(), document.getElementById(e).value += " " + t
                        }
                    }, t.prototype.activate = function(h) {
                        return f(this, void 0, void 0, function() {
                            var e, n, i, r, a, o, s, l, c, u = this;
                            return d(this, function(t) {
                                switch (t.label) {
                                    case 0:
                                        return this.target = "string" == typeof h ? this.getDefaultCallback(h) : h, this.view.show(), [4, this.model.load("views")];
                                    case 1:
                                        a = t.sent(), this.view.populate(a), this.view.htmlCloseButton.addEventListener("click", function() {
                                            u.view.hide()
                                        });
                                        try {
                                            for (o = v(this.view.htmlMenuButtons.values()), s = o.next(); !s.done; s = o.next()) s.value.getElementsByClassName("risibank-navigation-button")[0].addEventListener("click", function(s) {
                                                return f(u, void 0, void 0, function() {
                                                    var e, n, i, r, a, o;
                                                    return d(this, function(t) {
                                                        switch (t.label) {
                                                            case 0:
                                                                return e = s.currentTarget.dataset.nav, this.view.showSection(e), "search" !== e ? [3, 2] : (this.view.populate([]), i = (n = this.view).populate, [4, this.model.search(this.view.htmlSearchInput.value)]);
                                                            case 1:
                                                                return i.apply(n, [t.sent()]), [3, 4];
                                                            case 2:
                                                                return r = this.model.load(e), o = (a = this.view).populate, [4, r];
                                                            case 3:
                                                                o.apply(a, [t.sent()]), t.label = 4;
                                                            case 4:
                                                                return [2]
                                                        }
                                                    })
                                                })
                                            })
                                        } catch (t) {
                                            e = {
                                                error: t
                                            }
                                        } finally {
                                            try {
                                                s && !s.done && (n = o.return) && n.call(o)
                                            } finally {
                                                if (e) throw e.error
                                            }
                                        }
                                        try {
                                            for (l = v(this.view.htmlResetButtons), c = l.next(); !c.done; c = l.next()) c.value.addEventListener("click", function(t) {
                                                return f(u, void 0, void 0, function() {
                                                    var e, n;
                                                    return d(this, function(t) {
                                                        switch (t.label) {
                                                            case 0:
                                                                return this.view.showSection("views"), n = (e = this.view).populate, [4, this.model.load("views")];
                                                            case 1:
                                                                return n.apply(e, [t.sent()]), [2]
                                                        }
                                                    })
                                                })
                                            })
                                        } catch (t) {
                                            i = {
                                                error: t
                                            }
                                        } finally {
                                            try {
                                                c && !c.done && (r = l.return) && r.call(l)
                                            } finally {
                                                if (i) throw i.error
                                            }
                                        }
                                        return this.view.htmlSearchInput.addEventListener("change", function(i) {
                                            return f(u, void 0, void 0, function() {
                                                var e, n;
                                                return d(this, function(t) {
                                                    switch (t.label) {
                                                        case 0:
                                                            return n = (e = this.view).populate, [4, this.model.search(i.target.value)];
                                                        case 1:
                                                            return n.apply(e, [t.sent()]), [2]
                                                    }
                                                })
                                            })
                                        }), [2]
                                }
                            })
                        })
                    }, t.prototype.onStickerChoice = function(t) {
                        this.view.hide(), this.target(t)
                    }, t
                }();
            n.Controller = a
        }, {
            "./Model": 2,
            "./View": 3
        }],
        2: [function(t, e, n) {
            "use strict";
            var r = this && this.__awaiter || function(a, o, s, l) {
                    return new(s || (s = Promise))(function(t, e) {
                        function n(t) {
                            try {
                                r(l.next(t))
                            } catch (t) {
                                e(t)
                            }
                        }

                        function i(t) {
                            try {
                                r(l.throw(t))
                            } catch (t) {
                                e(t)
                            }
                        }

                        function r(e) {
                            e.done ? t(e.value) : new s(function(t) {
                                t(e.value)
                            }).then(n, i)
                        }
                        r((l = l.apply(a, o || [])).next())
                    })
                },
                a = this && this.__generator || function(n, i) {
                    var r, a, o, t, s = {
                        label: 0,
                        sent: function() {
                            if (1 & o[0]) throw o[1];
                            return o[1]
                        },
                        trys: [],
                        ops: []
                    };
                    return t = {
                        next: e(0),
                        throw: e(1),
                        return: e(2)
                    }, "function" == typeof Symbol && (t[Symbol.iterator] = function() {
                        return this
                    }), t;

                    function e(e) {
                        return function(t) {
                            return function(e) {
                                if (r) throw new TypeError("Generator is already executing.");
                                for (; s;) try {
                                    if (r = 1, a && (o = 2 & e[0] ? a.return : e[0] ? a.throw || ((o = a.return) && o.call(a), 0) : a.next) && !(o = o.call(a, e[1])).done) return o;
                                    switch (a = 0, o && (e = [2 & e[0], o.value]), e[0]) {
                                        case 0:
                                        case 1:
                                            o = e;
                                            break;
                                        case 4:
                                            return s.label++, {
                                                value: e[1],
                                                done: !1
                                            };
                                        case 5:
                                            s.label++, a = e[1], e = [0];
                                            continue;
                                        case 7:
                                            e = s.ops.pop(), s.trys.pop();
                                            continue;
                                        default:
                                            if (!(o = 0 < (o = s.trys).length && o[o.length - 1]) && (6 === e[0] || 2 === e[0])) {
                                                s = 0;
                                                continue
                                            }
                                            if (3 === e[0] && (!o || e[1] > o[0] && e[1] < o[3])) {
                                                s.label = e[1];
                                                break
                                            }
                                            if (6 === e[0] && s.label < o[1]) {
                                                s.label = o[1], o = e;
                                                break
                                            }
                                            if (o && s.label < o[2]) {
                                                s.label = o[2], s.ops.push(e);
                                                break
                                            }
                                            o[2] && s.ops.pop(), s.trys.pop();
                                            continue
                                    }
                                    e = i.call(n, s)
                                } catch (t) {
                                    e = [6, t], a = 0
                                } finally {
                                    r = o = 0
                                }
                                if (5 & e[0]) throw e[1];
                                return {
                                    value: e[0] ? e[1] : void 0,
                                    done: !0
                                }
                            }([e, t])
                        }
                    }
                };
            Object.defineProperty(n, "__esModule", {
                value: !0
            });
            var o = t("./model/RisiBankAPI"),
                i = function() {
                    function t(t) {
                        this.cachedSearches = {}, this.controller = t
                    }
                    return t.prototype.search = function(i) {
                        return r(this, void 0, void 0, function() {
                            var e, n;
                            return a(this, function(t) {
                                switch (t.label) {
                                    case 0:
                                        return void 0 !== this.cachedSearches[i] ? [3, 2] : (e = this.cachedSearches, n = i, [4, o.RisiBankAPI.search(i)]);
                                    case 1:
                                        e[n] = t.sent().stickers, t.label = 2;
                                    case 2:
                                        return [2, this.cachedSearches[i]]
                                }
                            })
                        })
                    }, t.prototype.load = function(n) {
                        return r(this, void 0, void 0, function() {
                            var e;
                            return a(this, function(t) {
                                switch (t.label) {
                                    case 0:
                                        return void 0 !== this.cachedHomePage ? [3, 2] : (e = this, [4, o.RisiBankAPI.load()]);
                                    case 1:
                                        e.cachedHomePage = t.sent(), t.label = 2;
                                    case 2:
                                        return [2, this.cachedHomePage[n]]
                                }
                            })
                        })
                    }, t
                }();
            n.Model = i
        }, {
            "./model/RisiBankAPI": 5
        }],
        3: [function(t, e, n) {
            "use strict";
            var v = this && this.__values || function(t) {
                var e = "function" == typeof Symbol && t[Symbol.iterator],
                    n = 0;
                return e ? e.call(t) : {
                    next: function() {
                        return t && n >= t.length && (t = void 0), {
                            value: t && t[n++],
                            done: !t
                        }
                    }
                }
            };
            Object.defineProperty(n, "__esModule", {
                value: !0
            });
            var i = function() {
                function a(t) {
                    this.htmlMenuButtons = new Map, this.isBeenInitialized = !1, this.controller = t
                }
                return a.prototype.init = function() {
                    var e, t;
                    if (!this.isBeenInitialized) {
                        this.htmlContent = document.createElement("div"), this.htmlContent.innerHTML = a.HTML_DIV_TEMPLATE, this.htmlContent.id = "risibank-overlay-container", this.htmlContent.classList.add("hidden"), document.body.appendChild(this.htmlContent), this.htmlCloseButton = document.getElementById(a.HTML_CLOSE_BUTTON_ID), this.htmlStickerContainer = document.getElementById(a.HTML_STICKER_CONTAINER_ID);
                        try {
                            for (var n = v(Array.from(document.getElementsByClassName("risibank-navigation"))), i = n.next(); !i.done; i = n.next()) {
                                var r = i.value;
                                this.htmlMenuButtons.set(r.dataset.nav, r)
                            }
                        } catch (t) {
                            e = {
                                error: t
                            }
                        } finally {
                            try {
                                i && !i.done && (t = n.return) && t.call(n)
                            } finally {
                                if (e) throw e.error
                            }
                        }
                        this.htmlResetButtons = Array.from(document.getElementsByClassName("risibank-navigation-return")), this.htmlSearchInput = document.getElementById(a.HTML_SEARCH_INPUT_ID), this.isBeenInitialized = !0
                    }
                }, a.prototype.show = function() {
                    this.init(), this.htmlContent.classList.remove("hidden")
                }, a.prototype.hide = function() {
                    this.htmlContent.classList.add("hidden")
                }, a.prototype.showSection = function(t) {
                    var e, n, i, r, a, o;
                    try {
                        for (var s = v(this.htmlMenuButtons.values()), l = s.next(); !l.done; l = s.next()) {
                            (d = l.value).classList.remove("selected")
                        }
                    } catch (t) {
                        e = {
                            error: t
                        }
                    } finally {
                        try {
                            l && !l.done && (n = s.return) && n.call(s)
                        } finally {
                            if (e) throw e.error
                        }
                    }
                    this.htmlMenuButtons.get(t).classList.add("selected");
                    try {
                        for (var c = v(this.htmlMenuButtons.values()), u = c.next(); !u.done; u = c.next()) {
                            (d = u.value).classList.remove("hidden")
                        }
                    } catch (t) {
                        i = {
                            error: t
                        }
                    } finally {
                        try {
                            u && !u.done && (r = c.return) && r.call(c)
                        } finally {
                            if (i) throw i.error
                        }
                    }
                    try {
                        for (var h = v(this.htmlMenuButtons.values()), f = h.next(); !f.done; f = h.next()) {
                            var d;
                            (d = f.value).classList.contains("risibank-hide-when-" + t) && d.classList.add("hidden")
                        }
                    } catch (t) {
                        a = {
                            error: t
                        }
                    } finally {
                        try {
                            f && !f.done && (o = h.return) && o.call(h)
                        } finally {
                            if (a) throw a.error
                        }
                    }
                }, a.prototype.populate = function(t) {
                    var e, n, i, r, a = this;
                    this.htmlStickerContainer.innerHTML = "";
                    try {
                        for (var o = v(t), s = o.next(); !s.done; s = o.next()) {
                            var l = s.value,
                                c = "https://risibank.fr/cache/stickers/d" + Math.floor(l.id / 100) + "/" + l.id + "-static." + l.ext;
                            this.htmlStickerContainer.innerHTML += '\n                <div class="risibank-sticker" data-link="' + l.risibank_link + '">\n                    <img src="' + c + '">\n                </div>\n            '
                        }
                    } catch (t) {
                        e = {
                            error: t
                        }
                    } finally {
                        try {
                            s && !s.done && (n = o.return) && n.call(o)
                        } finally {
                            if (e) throw e.error
                        }
                    }
                    var u = Array.from(document.getElementsByClassName("risibank-sticker")),
                        h = function(t) {
                            t.addEventListener("click", function() {
                                return a.controller.onStickerChoice(t.dataset.link)
                            })
                        };
                    try {
                        for (var f = v(u), d = f.next(); !d.done; d = f.next()) {
                            h(d.value)
                        }
                    } catch (t) {
                        i = {
                            error: t
                        }
                    } finally {
                        try {
                            d && !d.done && (r = f.return) && r.call(f)
                        } finally {
                            if (i) throw i.error
                        }
                    }
                }, a.HTML_CLOSE_BUTTON_ID = "risibank-close", a.HTML_STICKER_CONTAINER_ID = "risibank-stickers", a.HTML_SEARCH_INPUT_ID = "risibank-search", a
            }();
            (n.View = i).HTML_DIV_TEMPLATE = '\n\n    <div id="risibank-overlay">\n        <div id="risibank-banner">\n            <img id="risibank-logo" src="https://risibank.fr/src/picts/banner-light.png">\n            <img id="' + i.HTML_CLOSE_BUTTON_ID + '" src="https://4feuilles.org/css/close.png">\n        </div>\n        <div id="' + i.HTML_STICKER_CONTAINER_ID + '">\n            \x3c!-- contains the actual stickers --\x3e\n        </div>\n        <div id="risibank-navigation">\n            \x3c!-- menu & links --\x3e\n            <ul>\n            \n                <li class="risibank-navigation" data-nav="search">\n                    <a class="risibank-navigation-button risibank-hide-when-search" data-nav="search" href="javascript:">\n                        🔍                    </a>\n                    <input id="' + i.HTML_SEARCH_INPUT_ID + '" class="risibank-navigation-submenu" type="text" placeholder="tags..">\n                    <button class="risibank-navigation-submenu risibank-navigation-return">\n                        ❌                    </button>\n                </li>\n                \n                <li class="risibank-navigation risibank-hide-when-search selected" data-nav="views">\n                    <a class="risibank-navigation-button" data-nav="views" href="javascript:">\n                        Populaire\n                    </a>\n                </li>\n                \n                <li class="risibank-navigation risibank-hide-when-search" data-nav="trending">\n                    <a class="risibank-navigation-button" data-nav="trending" href="javascript:">\n                        🌶Hot\n                    </a>\n                </li>\n                \n                <li class="risibank-navigation risibank-hide-when-search" data-nav="tms">\n                    <a class="risibank-navigation-button" data-nav="tms" href="javascript:">\n                        Récent\n                    </a>\n                </li>\n                \n                <li class="risibank-navigation risibank-hide-when-search" data-nav="random">\n                    <a class="risibank-navigation-button" data-nav="random" href="javascript:">\n                        Hasard\n                    </a>\n                </li>\n            </ul>\n        </div>\n    </div>\n\n'
        }, {}],
        4: [function(t, e, n) {
            "use strict";
            Object.defineProperty(n, "__esModule", {
                value: !0
            });
            var i = t("./Controller");
            e.exports = new i.Controller
        }, {
            "./Controller": 1
        }],
        5: [function(t, e, n) {
            "use strict";
            var i = this && this.__awaiter || function(a, o, s, l) {
                    return new(s || (s = Promise))(function(t, e) {
                        function n(t) {
                            try {
                                r(l.next(t))
                            } catch (t) {
                                e(t)
                            }
                        }

                        function i(t) {
                            try {
                                r(l.throw(t))
                            } catch (t) {
                                e(t)
                            }
                        }

                        function r(e) {
                            e.done ? t(e.value) : new s(function(t) {
                                t(e.value)
                            }).then(n, i)
                        }
                        r((l = l.apply(a, o || [])).next())
                    })
                },
                r = this && this.__generator || function(n, i) {
                    var r, a, o, t, s = {
                        label: 0,
                        sent: function() {
                            if (1 & o[0]) throw o[1];
                            return o[1]
                        },
                        trys: [],
                        ops: []
                    };
                    return t = {
                        next: e(0),
                        throw: e(1),
                        return: e(2)
                    }, "function" == typeof Symbol && (t[Symbol.iterator] = function() {
                        return this
                    }), t;

                    function e(e) {
                        return function(t) {
                            return function(e) {
                                if (r) throw new TypeError("Generator is already executing.");
                                for (; s;) try {
                                    if (r = 1, a && (o = 2 & e[0] ? a.return : e[0] ? a.throw || ((o = a.return) && o.call(a), 0) : a.next) && !(o = o.call(a, e[1])).done) return o;
                                    switch (a = 0, o && (e = [2 & e[0], o.value]), e[0]) {
                                        case 0:
                                        case 1:
                                            o = e;
                                            break;
                                        case 4:
                                            return s.label++, {
                                                value: e[1],
                                                done: !1
                                            };
                                        case 5:
                                            s.label++, a = e[1], e = [0];
                                            continue;
                                        case 7:
                                            e = s.ops.pop(), s.trys.pop();
                                            continue;
                                        default:
                                            if (!(o = 0 < (o = s.trys).length && o[o.length - 1]) && (6 === e[0] || 2 === e[0])) {
                                                s = 0;
                                                continue
                                            }
                                            if (3 === e[0] && (!o || e[1] > o[0] && e[1] < o[3])) {
                                                s.label = e[1];
                                                break
                                            }
                                            if (6 === e[0] && s.label < o[1]) {
                                                s.label = o[1], o = e;
                                                break
                                            }
                                            if (o && s.label < o[2]) {
                                                s.label = o[2], s.ops.push(e);
                                                break
                                            }
                                            o[2] && s.ops.pop(), s.trys.pop();
                                            continue
                                    }
                                    e = i.call(n, s)
                                } catch (t) {
                                    e = [6, t], a = 0
                                } finally {
                                    r = o = 0
                                }
                                if (5 & e[0]) throw e[1];
                                return {
                                    value: e[0] ? e[1] : void 0,
                                    done: !0
                                }
                            }([e, t])
                        }
                    }
                };
            Object.defineProperty(n, "__esModule", {
                value: !0
            });
            var a = function() {
                function n() {}
                return n.getUrl = function(t) {
                    return this.PROTOCOL + "://" + this.HOST + "/" + t
                }, n.load = function() {
                    return i(this, void 0, void 0, function() {
                        return r(this, function(t) {
                            switch (t.label) {
                                case 0:
                                    return [4, fetch(n.getUrl("api/v0/load"))];
                                case 1:
                                    return [4, t.sent().json()];
                                case 2:
                                    return [2, t.sent().stickers]
                            }
                        })
                    })
                }, n.search = function(e) {
                    return i(this, void 0, void 0, function() {
                        return r(this, function(t) {
                            switch (t.label) {
                                case 0:
                                    return [4, fetch(n.getUrl("api/v0/search"), {
                                        method: "POST",
                                        body: JSON.stringify({
                                            search: e
                                        }),
                                        headers: {
                                            "Content-Type": "application/json"
                                        }
                                    })];
                                case 1:
                                    return [2, t.sent().json()]
                            }
                        })
                    })
                }, n.PROTOCOL = "https", n.HOST = "api.risibank.fr", n.LOAD_URL = "v0/load", n.SEARCH_URL = "v0/search", n
            }();
            n.RisiBankAPI = a
        }, {}]
    }, {}, [4])(4)
});
// # sourceMappingURL=risibank.js.map