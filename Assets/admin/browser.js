$(function(){
	$.delete({
		categories : {
			announce : {
				url : "/admin/module/announcement/announce/delete"
			},
			category : {
				url : "/admin/module/announcement/category/delete"
			}
		}
	});
});