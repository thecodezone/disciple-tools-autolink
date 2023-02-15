<?php include( 'parts/app-header.php' ); ?>

<?php include( 'parts/church-view-tabs.php' ); ?>

<div class="tree container">

    <dt-tile title="<?php esc_html_e( 'Church Tree' ); ?>">
        <div class="section__inner">
                <div id ="wrapper" >
                    <span class ="loading-spinner active" > < /span >
                </div>
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
  window.post_item( 'tree', {} )
    .done(function(data){
      window.load_domenu(data)
      jQuery('#initial-loading-spinner').hide()
    })
}

window.load_domenu = ( data ) => {

jQuery( '#domenu-0' ).domenu({
    data: JSON.stringify( data.tree ),
    maxDepth: 500,
    refuseConfirmDelay: 500, // does not delete immediately but requires a second click to confirm.
    select2:                {
      support:     false, // Enable Select2 support
    }
}).parseJson()
    .onItemDrop(function( e ) {
        if ( typeof e.prevObject !== 'undefined' && typeof e[0].id !== 'undefined' ) { // runs twice on drop. with and without prevObject
            console.log( 'onItemDrop' )
            jQuery( '.loading-spinner' ).addClass( 'active' )

            let new_parent = e[0].parentNode.parentNode.id
            let self = e[0].id

          // console.log(' - new parent: '+ new_parent)
          // console.log(' - self: '+ self)

            let prev_parent_object = jQuery( '#' +e[0].id )
            let previous_parent = prev_parent_object.data( 'prev_parent' )

            prev_parent_object.attr( 'data-prev_parent', new_parent ) // set previous

            if ( new_parent !== previous_parent ) {
                window.post_item( 'onItemDrop', { new_parent: new_parent, self: self, previous_parent: previous_parent } ).done(function( drop_data ){
                    jQuery( '.loading-spinner' ).removeClass( 'active' )
                    if ( drop_data ) {
                        console.log( 'success onItemDrop' )
                    }
                    else {
                        console.log( 'did not edit item' )
                    }
                })
            }
        }
    })
    .onItemSetParent(function( e ) {
        if ( typeof e[0] !== 'undefined' ) {
            jQuery( '#' + e[0].id + ' button.item-remove:first' ).hide();
        }
    })
    .onItemUnsetParent(function( e ) {
        if ( typeof e[0] !== 'undefined' ) {
            jQuery( '#' + e[0].id + ' button.item-remove:first' ).show();
        }
    })


  // list prep
jQuery.each( jQuery( '#domenu-0 .item-name' ), function( i,v ){
    // move and set the title to id
    jQuery( this ).parent().parent().attr( 'id', jQuery( this ).html() )
})
  // set the previous parent data element
jQuery.each( data.parent_list, function( ii,vv ) {
    if ( vv !== null && vv !== "undefined" ) {
        let target = jQuery( '#' +ii )
        if ( target.length > 0 ) {
            target.attr( 'data-prev_parent', vv )
        }
    }
})
  // show delete for last item
  jQuery( "li:not(:has(>ol)) .item-remove" ).show()
  // set title
jQuery.each(jQuery( '.item-name' ), function( ix,vx ) {
    let old_title = jQuery( this ).html()
    jQuery( this ).html( data.title_list[old_title] )
})
  // set listener for edit button
jQuery( '#domenu-0 .item-edit' ).on('click', function( e ) {
    window.open_edit_modal( e.currentTarget.parentNode.parentNode.parentNode.id )
})
jQuery( '#domenu-0 .item-add-child' ).on('click', function( e ) {
    window.open_create_modal( e.currentTarget.parentNode.parentNode.parentNode.id )
})
}

window.post_item = ( action, data ) => {
  jQuery('.loading-spinner').addClass('active')
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: action, parts: <?php echo wp_json_encode( $parts ); ?>, data: data }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: `<?php echo esc_url( $fetch_url ); ?>`,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', `<?php echo wp_create_nonce( 'wp_rest' ); ?>` )
    }
  })
    .done(function(e) {
      console.log(e)
      jQuery('#error').html(e)
      jQuery('.loading-spinner').removeClass('active')
    })
    .fail(function(e) {
      console.log(e)
      jQuery('#error').html(e)
      jQuery('.loading-spinner').removeClass('active')
    })
}

load_tree()
</script>
