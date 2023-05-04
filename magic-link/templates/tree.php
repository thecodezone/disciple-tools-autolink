<style>
    .dd3-content {
        background: -webkit-linear-gradient(top, #f4f4f4 10%, #c9c9c900 25%);
        background: linear-gradient(top, #f4f4f4 10%, #c9c9c900 25%);
    }

    .dd-item--owned > .dd3-content {
        background: -webkit-linear-gradient(top, #fdfdfd 10%, #c1efc3 100%);
        background: linear-gradient(top, #fdfdfd 10%, #c1efc3 100%);
    }

    #u .dd3-content {
        background: linear-gradient(top, #f4f4f4 10%, #fbf2c4 100%);
        background: -webkit-linear-gradient(top, #f4f4f4 10%, #fbf2c4 100%);
    }

    #u > .dd-handle {
        display: none;
        pointer-events: none;
    }

    #u > .dd3-content {
        background: -webkit-linear-gradient(top, #c8c8c8 0%, #8c8c8c 100%);
        background: linear-gradient(top, #c8c8c8 0%, #8c8c8c 100%);
    }

    #u > .dd3-content > .item-name {
        color: white;
        text-shadow: 0 1px 0 #333;
    }

    .dd-item--owned > .dd-list > .dd-item--coaching * {
        pointer-events: none;
    }

    .dd-item--owned > .dd-list > .dd-item--coaching > .dd-handle {
        pointer-events: all;
    }

    .dd-item--owned > .dd-list > .dd-item--coaching > button {
        display: none !important;
    }

    dt-alert {
        display: none;
    }

    dt-alert.active {
        display: block;
    }

</style>

<?php include( 'parts/app-header.php' ); ?>

<?php include( 'parts/church-view-tabs.php' ); ?>

<div class="tree container">

    <dt-tile title="<?php esc_html_e( 'Church Tree' ); ?>">
        <div class="section__inner">
            <div class="loading-spinner__wrapper">
                <div class="loading-spinner active"></div>
            </div>
            <dt-alert class="alert--error"
                      context="alert">
                <?php esc_html_e( 'An unexpected error has occurred.', 'disciple-tools-autolink' ); ?>
            </dt-alert>
            <dt-alert class="alert--invalid"
                      context="alert">
                <?php esc_html_e( 'Invalid group nesting.', 'disciple-tools-autolink' ); ?>
            </dt-alert>
            <div id="wrapper"></div>
        </div>
</div>
</dt-tile>

</div>

<?php include( 'parts/app-footer.php' ); ?>

<script>
    window.load_tree = () => {
        jQuery('#wrapper').html(`
<div class="dd" id="domenu-0">
  <button class="dd-new-item" style="display: none;">+</button>
  <li class="dd-item-blueprint">
    <button class="collapse" data-action="collapse" type="button" style="display: none;">–</button>
    <button class="expand" data-action="expand" type="button" style="display: none;">+</button>
    <div class="dd-handle dd3-handle">&nbsp;</div>
    <div class="dd3-content">
      <span class="item-name" style="pointer-events: none;">[item_name]</span>
      <div class="dd-button-container">
      </div>
      <div class="dd-edit-box" style="display: none;">
        <input type="text" name="title" autocomplete="off" placeholder="Item" {?numeric.increment}">
      </div>
    </div>
  </li>
  <ol class="dd-list"></ol>
</div>
  `)
        window.post_item('tree', {})
            .done(function (data) {
                jQuery('.loading-spinner').addClass('active')
                window.load_domenu(data)
                jQuery('.loading-spinner').removeClass('active')
            })
    }

    window.load_domenu = (data) => {

        const find_item = (id, tree = null) => {
            if (!tree) {
                tree = data.tree
            }

            for (let i = 0; i < tree.length; i++) {

                if (tree[i].id == id) {
                    return tree[i]
                }

                if (tree[i].children) {
                    const item = find_item(id, tree[i].children)
                    if (item) {
                        return item
                    }
                }
            }
            return null
        }

        const domenu = jQuery('#domenu-0').domenu({
            data: JSON.stringify(data.tree),
            maxDepth: 500,
            refuseConfirmDelay: 500, // does not delete immediately but requires a second click to confirm.
            select2: {
                support: false, // Enable Select2 support
            }
        }).parseJson()
            .onItemDrop(function (e) {
                if (typeof e.prevObject !== 'undefined' && typeof e[0].id !== 'undefined') { // runs twice on drop. with and without prevObject
                    jQuery('.loading-spinner').addClass('active')

                    let new_parent = e[0].parentNode.parentNode.id
                    let self = e[0].id

                    setListData(data)

                    let prev_parent_object = jQuery('#' + e[0].id)
                    let previous_parent = prev_parent_object.data('prev_parent')

                    prev_parent_object.attr('data-prev_parent', new_parent) // set previous


                    if (new_parent !== previous_parent) {
                        jQuery('dt-alert').each(function () {
                            jQuery(this).removeClass('active')
                        });
                        window.post_item('onItemDrop', {
                            new_parent: new_parent,
                            self,
                            previous_parent: previous_parent
                        }).done(function (result) {
                            if (result === 'reload') {
                                jQuery('.alert--invalid').addClass('active')
                                load_tree()
                            } else if (!result) {
                                jQuery('.alert--error').addClass('active')
                            }
                        })
                    }
                }
            })
            .onItemSetParent(function (e) {
                if (typeof e[0] !== 'undefined') {
                    jQuery('#' + e[0].id + ' button.item-remove:first').hide();
                }
            })
            .onItemUnsetParent(function (e) {
                if (typeof e[0] !== 'undefined') {
                    jQuery('#' + e[0].id + ' button.item-remove:first').show();
                }
            })


        // list prep
        jQuery.each(jQuery('#domenu-0 .item-name'), function (i, v) {
            // move and set the title to id
            let id = jQuery(this).html()
            if (id !== 'u') {
                id = parseInt(id)
            }
            const item = find_item(id)

            jQuery(this).parent().parent().attr('id', id)

            if (!item) {
                return;
            }
            if (item['coaching']) {
                jQuery(this).parent().parent().addClass('dd-item--coaching')
            } else {
                jQuery(this).parent().parent().addClass('dd-item--owned')
            }
            if (item && item['has_parent']) {
                jQuery(this).parent().parent().addClass('dd-item--has-parent')
            }
        })

        // set the previous parent data element
        function setListData(data) {
            jQuery.each(data.parent_list, function (ii, vv) {
                if (vv !== null && vv !== "undefined") {
                    let target = jQuery('#' + ii)
                    if (target.length > 0) {
                        target.attr('data-prev_parent', vv)
                    }
                }
            })
        }

        setListData(data)

        // show delete for last item
        jQuery("li:not(:has(>ol)) .item-remove").show()
        // set properties
        jQuery.each(jQuery('.item-name'), function (ix, vx) {
            let old_title = jQuery(this).html()
            jQuery(this).html(data.title_list[old_title])
        })

        window.setTimeout(function () {
            domenu.collapse(el => jQuery(el).hasClass('dd-item--coaching'))
        }, 1)

    }

    window.post_item = (action, data) => {
        jQuery('.loading-spinner').addClass('active')
        return jQuery.ajax({
            type: "POST",
            data: JSON.stringify({action: action, parts: <?php echo wp_json_encode( $parts ); ?>, data: data}),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            url: `<?php echo esc_url( $fetch_url ); ?>`,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', `<?php echo wp_create_nonce( 'wp_rest' ); ?>`)
            }
        })
            .done(function (e) {
                jQuery('#error').html(e)
                jQuery('.loading-spinner').removeClass('active')
            })
            .fail(function (e) {
                jQuery('#error').html(e)
                jQuery('.loading-spinner').removeClass('active')
            })
    }

    load_tree()
</script>
