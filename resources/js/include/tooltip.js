export function tooltip() {
    $(document).tooltip({
        position: {
            show: null,
            position: {
                my: "left top",
                at: "left bottom"
            },
            open: function( event, ui ) {
              //  ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
            },

        }
    })

}
