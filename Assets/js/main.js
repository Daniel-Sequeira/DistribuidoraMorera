(function () {
	"use strict";

	var treeviewMenu = $('.app-menu');

	// Toggle Sidebar (data-toggle y data-bs-toggle)
	$('[data-toggle="sidebar"], [data-bs-toggle="sidebar"]').click(function(event) {
		event.preventDefault();
		$('.app').toggleClass('sidenav-toggled');
	});

	// Activate sidebar treeview toggle
	$("[data-toggle='treeview'], [data-bs-toggle='treeview']").click(function(event) {
		event.preventDefault();
		if(!$(this).parent().hasClass('is-expanded')) {
			treeviewMenu.find("[data-toggle='treeview'], [data-bs-toggle='treeview']").parent().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
	});
	$("[data-toggle='treeview'], [data-bs-toggle='treeview']").parent('.is-expanded').addClass('is-expanded');

	// Activate Bootstrap 5 tooltips 
	if (typeof bootstrap !== 'undefined') {
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]'));
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl);
		});
	}

})();