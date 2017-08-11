
jQuery(document).ready(function($){  
	//checkbox-tree
	if ($("ul.expandable").length>0) {
		$("ul.expandable").collapsibleCheckboxTree({
			  // When checking a box, all parents are checked (Default: true)
				   checkParents : false,
			  // When checking a box, all children are checked (Default: false)
				   checkChildren : false,
			  // When unchecking a box, all children are unchecked (Default: true)
				   uncheckChildren : true,
			  // 'expand' (fully expanded), 'collapse' (fully collapsed) or 'default'
				   initialState : 'default'

		 });
	}

})
