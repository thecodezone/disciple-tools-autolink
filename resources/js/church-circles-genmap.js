(function () {
    "use strict";
    let localizedObject = window.wpApiGenmapper;
    let chartDiv = jQuery("#chart"); // retrieves the chart div in the metrics page
    jQuery(document).ready(function () {
        show_template_overview();
    });

    function add_url_params(url, params = {}){
        let urlObj = new URL(url);

        // Merge existing params with new params.
        for(const key in params) {
            urlObj.searchParams.append(key, params[key]);
        }

        // Construct the final URL.
        return urlObj.toString();
    }

    function show_template_overview() {
        const windowHeight = document.documentElement.clientHeight;
        chartDiv.empty().html(`
          <div class="grid-x">
            <div class="cell">
              <aside id="left-menu">
              </aside>
              <section id="alert-message" class="alert-message">
              </section>
              <section id="edit-group" class="edit-group">
              </section>
              <section id="genmapper-graph" style="height:${
                document.documentElement.clientHeight - 250
            }px">
                <svg id="genmapper-graph-svg" width="100%"></svg>
              </section>
              <section id="genmapper-graph-v2" style="
              height: ${document.documentElement.clientHeight - 250}px;
              width: 85%;
              border: 3px solid #343a40;
              background-color: #FFFFFF;
              overflow:scroll;
              ">
                <div id="genmap-v2"></div>
              </section>
            </div>
          </div>
        `);

        // Determine genmap style to be displayed.
        const show_tree_genmap = Boolean(JSON.parse(localizedObject['show_tree_genmap']));
        if (show_tree_genmap === true) {
            jQuery('#genmapper-graph').hide();
        } else {
          jQuery('#genmapper-graph-v2').hide();
        }

        window.genmapper = new window.genMapperClass();
        get_groups();

        $("#reset_tree").on("click", function () {
            get_groups();
        });
    }

    function get_groups(group = null) {
        let loading_spinner = $(".loading-spinner");
        loading_spinner.addClass("active");

        jQuery(document).ready(function () {
            return jQuery
                .ajax({
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    url: add_url_params($autolink.urls.route + "api/genmap", {"node": group}),
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("X-WP-Nonce", $autolink.nonce);
                    },
                })
                .fail(function (err) {
                    displayError(err);
                })
                .then((e) => {
                    loading_spinner.removeClass("active");

                    if ('flat_connections' in e) {
                      window.genmapper.importJSON(e['flat_connections'], group === null);
                      window.genmapper.origPosition(true);
                    }

                    if ('tree_connections' in e) {
                      window.genmapper.chart_tree(e['tree_connections']);
                    }
                });
        });
    }

    function displayError(err, msg) {
        window.genmapper.displayAlert();
        if (
            err.responseJSON &&
            err.responseJSON.data &&
            err.responseJSON.data.record
        ) {
            let msg =
                err.responseJSON.message +
                ` <a target="_blank" href="${err.responseJSON.data.link}">${localizedObject.translation.open_record_label}</a>`;
            window.genmapper.displayAlert(msg);
        }
    }

    chartDiv.on("rebase-node-requested", function (e, node) {
        get_groups(node.data.id);
    });

    chartDiv.on("reset-requested", function (e, parent) {
        get_groups();
    });
})();
