/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(["jquery", "mage/mage"], function ($, mage) {
    "use strict";
    return {
        tree: {},
        waitFor: function (elt, callback) {
            let initializer = null;
            initializer = setInterval(function () {
                if ($(elt).length > 0) {
                    setTimeout(callback, 500);
                    clearInterval(initializer);
                }
            }, 200);
        },
        init: function () {
            this.waitFor("#cat-json-tree", function () {
                this.tree = $.parseJSON($("#cat-json-tree").val());
                let root = this.tree[1];
                this.displayChildren(root);
            }.bind(this));
        },
        displayChildren: function (node, parentElement) {
            if (typeof parentElement !== "undefined") {
                $(parentElement).parent().find("ul").remove();
            }
            let children = node["children"];
            $.each(children, function (index, child) {
                this.constructNode(this.tree[child], parentElement);
                this.initAutoComplete(child);
            }.bind(this));
            this.updateSelectionAndMapping(children);
        },
        constructNode: function (node, parentElement) {
            let content = "<ul class='tv-mapping closed'>";
            content += "<li>";
            content += "<div class='selector' id='main-cat-" + node["id"] + "'>";
            if (node["children"].length > 0) {
                content += "<span class='tv-switcher closed' id='" + node["id"] + "'></span>";
            } else {
                content += "<span class='empty'></span>";
            }
            content += "<input type='checkbox' class='category' id='cat_id_" + node["id"] + "' name='cat_id_" + node["id"] + "'/>";
            content += node["text"];
            content += "<span class='small'>[ID:" + node["id"] + "]</span>";
            content += "<span class='mapped'>";
            content += "<br/>";
            content += "<span>" + $.mage.__("mapped as :") + "</span>";
            content += "</span>&nbsp;";
            content += "<label class='mage-suggest-search-label'>";
            content += "<input placeholder='" + $.mage.__("your google product category") + "' title='" + $.mage.__("Press `End.` on your keyboard in order to apply this value to all the sub-categories") + "' type='text' class='mapping' id='category_mapping_" + node['id'] + "' class='mapping' />";
            content += "</label>";
            content += "</div>";
            // children
            content += "</li>";
            content += "</ul>";
            if (typeof parentElement === "undefined") {
                $(content).insertAfter("#cat-json-tree");
            } else {
                $(content).insertAfter(parentElement);
            }
        },
        /**
         * When a mapping change or when a category is (un)selected
         * @returns {undefined}
         */
        updateSelection: function () {
            let selection = {};
            if ($("#simplegoogleshopping_categories").val() !== "*" && $("#simplegoogleshopping_categories").val() !== "") {
                selection = $("#simplegoogleshopping_categories").val().evalJSON();
            }
            $("input.category").each(function () {
                let elt = $(this);
                let id = elt.attr("id").replace("cat_id_", "");
                let mapping = $("#category_mapping_" + id).val();
                selection[id] = {c: ($(this).prop("checked") === true ? "1" : "0"), m: mapping};
            });
            $("#simplegoogleshopping_categories").val(JSON.stringify(selection));
        },
        /**
         * Select all children categories
         */
        selectChildren: function (parentId, cats) {
            let categories = {};
            if (typeof cats === "undefined") {
                categories = $("#simplegoogleshopping_categories").val().evalJSON();
            } else {
                categories = cats;
            }
            let checked = categories[parentId]["c"];
            let children = this.tree[parentId]["children"];
            children.each(function (child) {
                if (typeof categories[child] === "undefined") {
                    categories[child] = {"c": 0, "m": ""};
                }
                categories[child]["c"] = checked;
                $("#cat_id_" + child).prop("checked", checked === "1");
                if (checked === "1") {
                    $("#cat_id_" + child).parent().addClass("selected");
                } else {
                    $("#cat_id_" + child).parent().removeClass("selected");
                }
                this.selectChildren(child, categories);
            }.bind(this));
            if (typeof cats === "undefined") {
                $("#simplegoogleshopping_categories").val(JSON.stringify(categories));
            }
        },
        updateSelectionAndMapping: function (children) {
            let categories = {};
            if ($("#simplegoogleshopping_categories").val() !== "*" && $("#simplegoogleshopping_categories").val() !== "") {
                categories = $("#simplegoogleshopping_categories").val().evalJSON();
            } else {
                $("#simplegoogleshopping_categories").val(JSON.stringify(categories));
            }
            for (let i in children) {
                let id = children[i];
                if (typeof categories[id] !== "undefined") {
                    let cat = categories[id];
                    if (cat["c"] === "1") { // if checked
                        $("#cat_id_" + id).prop("checked", true);
                        $("#cat_id_" + id).parent().addClass("selected");
                    }
                    // set the category mapping
                    $("#category_mapping_" + id).val(cat["m"]);
                }
            }
        },
        /**
         * Load the categories filter (exclude/include)
         * @returns {undefined}
         */
        loadCategoriesFilter: function () {
            if ($("#simplegoogleshopping_category_filter").val() === "") {
                $("#simplegoogleshopping_category_filter").val(1);
            }
            if ($("#simplegoogleshopping_category_type").val() === "") {
                $("#simplegoogleshopping_category_type").val(0);
            }
            $("#category_filter_" + $("#simplegoogleshopping_category_filter").val()).prop("checked", true);
            $("#category_type_" + $("#simplegoogleshopping_category_type").val()).prop("checked", true);
        },
        /**
         * Update all children with the parent mapping
         */
        updateChildrenMapping: function (mapping, parentId, cats) {
            let categories = {};
            if (typeof cats === "undefined") {
                categories = $("#simplegoogleshopping_categories").val().evalJSON();
            } else {
                categories = cats;
            }
            let children = this.tree[parentId]["children"];
            children.each(function (child) {
                if (typeof categories[child] === "undefined") {
                    categories[child] = {"c": 0, "m": ""};
                }
                categories[child]["m"] = mapping;
                $("#category_mapping_" + child).val(mapping);
                this.updateChildrenMapping(mapping, child, categories);
            }.bind(this));
            if (typeof cats === "undefined") {
                $("#simplegoogleshopping_categories").val(JSON.stringify(categories));
            }
        },
        /**
         * Initialize autocomplete fields for the mapping
         * @returns {undefined}
         */
        initAutoComplete: function (id) {
            let list = $("#category_mapping_" + id);
            list.each(function (i) {
                $(list[i]).autocomplete({
                    source: Utils.categoriesUrl + "?file=" + $("#simplegoogleshopping_feed_taxonomy").val(),
                    minLength: 2,
                    select: function (event, ui) {
                        this.updateSelection();
                    }.bind(this)
                });
            }.bind(this));
        },
        /**
         * Re-init the autocomplete fields with a new taxonomy file
         * @returns {undefined}
         */
        updateAutoComplete: function () {
            $(".mapping").each(function () {
                $(this).autocomplete("option", "source", Utils.categoriesUrl + "?file=" + $("#simplegoogleshopping_feed_taxonomy").val());
            });
        }
    };
});