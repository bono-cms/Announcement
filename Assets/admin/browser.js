$(function(){
	$.delete({
		categories : {
			announce : {
				url : "/admin/module/announcement/announce/delete.ajax"
			},
			category : {
				url : "/admin/module/announcement/category/delete.ajax"
			}
		}
	});
});