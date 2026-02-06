jQuery(document).ready(function ($) {
    var handheldMenu = $(".fframe_handheld");

    handheldMenu.on("click", function () {
        $(this).parent().find(".fframe_menu").toggleClass("fframe_menu_open");
    });

    $(".fframe_menu_item a").on("click", function () {
        handheldMenu.parent().find(".fframe_menu").removeClass("fframe_menu_open");
    });

    jQuery(".update-nag,.notice, #wpbody-content > .updated, #wpbody-content > .error").remove();

    jQuery(".toplevel_page_fluent-support a").on("click", function () {
        jQuery(".toplevel_page_fluent-support li").removeClass("current");
        jQuery(this).parent().addClass("current");
    });

    jQuery(document)
        .off("click", ".fs_offcanvas_menu_label a")
        .on("click", ".fs_offcanvas_menu_label a", function () {
            jQuery(".fs_offcanvas_menu_label").removeClass("current");
            jQuery(this).closest(".fs_offcanvas_menu_label").addClass("current");
        });

    var menuToggle = document.querySelector("[data-fs-menu-toggle]");

    if (menuToggle) {
        var menuParent = menuToggle.parentNode;
        var offcanvasMenu = menuParent.querySelector("[data-fs-offcanvas-menu]");
        var closeButton = menuParent.querySelector("[data-fs-offcanvas-menu-close]");
        var menuOverlay = menuParent.querySelector("[data-fs-offcanvas-menu-overlay]");

        if (menuToggle && offcanvasMenu && menuOverlay) {
            menuToggle.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                menuOverlay.classList.toggle("active");
                offcanvasMenu.classList.toggle("open");
                document.body.style.overflow = "hidden";
            });

            if (closeButton) {
                closeButton.addEventListener("click", function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    menuOverlay.classList.remove("active");
                    offcanvasMenu.classList.remove("open");
                    document.body.style.overflow = "";
                });
            }

            menuOverlay.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                menuOverlay.classList.remove("active");
                offcanvasMenu.classList.remove("open");
                document.body.style.overflow = "";
            });
        }
    }

    var ticketViewRoutePattern = /#\/tickets\/\d+\/view/;

    function cleanupStuckSupportOverlays() {
        if (!ticketViewRoutePattern.test(window.location.hash)) {
            return;
        }

        document.body.classList.remove("el-popup-parent--hidden", "el-overflow-hidden");

        if (!document.querySelector(".el-overlay, .v-modal")) {
            document.body.style.overflow = "";
            return;
        }

        var hasVisibleDialog = !!document.querySelector(
            ".el-dialog, .el-drawer, .el-message-box, .el-image-viewer__wrapper"
        );

        if (hasVisibleDialog) {
            return;
        }

        document.querySelectorAll(".el-overlay, .v-modal").forEach(function (overlayElement) {
            overlayElement.remove();
        });

        document.body.style.overflow = "";
    }

    window.addEventListener("hashchange", function () {
        setTimeout(cleanupStuckSupportOverlays, 30);
    });

    setTimeout(cleanupStuckSupportOverlays, 30);
});
