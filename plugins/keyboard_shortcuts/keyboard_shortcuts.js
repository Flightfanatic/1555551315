function keyboard_shortcuts_show_help() {
	$('#keyboard_shortcuts_help').dialog('open');
}

$(function() {

	$('#keyboard_shortcuts_help').dialog({
		autoOpen: false,
		draggable: false,
		modal: true,
		resizable: false,
		width: 830,
		title: cmail.gettext("keyboard_shortcuts.keyboard_shortcuts")
	});

	if(cmail.env.keyboard_shortcuts != 'disabled')
	  cmail.env.keyboard_shortcuts = true;
        else
	  cmail.env.keyboard_shortcuts = false;

	$('#quicksearchbox').focus(function (e) {
		cmail.env.keyboard_shortcuts = false;
	});
	$('#quicksearchbox').blur(function (e) {
		cmail.env.keyboard_shortcuts = true;
	});

        $().keypress(function (e) {
		if (!cmail.env.keyboard_shortcuts || cmail.env.action == 'compose' || cmail.env.task == 'login' || e.ctrlKey)
			return true;

		if (cmail.env.action == '') {	// list mailbox
			switch (e.which) {
				case 63:		// ? = help
					keyboard_shortcuts_show_help();
					return false;
				case 65:		// A = mark all as read
					cmail.command('select-all');
					cmail.command('mark', 'read');
                	        	return false;
				case 82:		// R = reply-all
					if (cmail.message_list.selection.length == 1)
						cmail.command('reply-all');
					return false;
				case 97:		// a = select all
					cmail.command('select-all');
					return false;
				case 99:		// c = compose
					cmail.command('compose');
					return false;
				case 102:		// f = forward
					if (cmail.message_list.selection.length == 1)
						cmail.command('forward');
					return false;
				case 106:		// j = previous page (similar to Gmail)
					cmail.command('previouspage');
					return false;
				case 107:		// k = next page (similar to Gmail)
					cmail.command('nextpage');
					return false;
				case 112:		// p = print
					if (cmail.message_list.selection.length == 1)
						cmail.command('print');
					return false;
				case 114:		// r = reply
					if (cmail.message_list.selection.length == 1)
						cmail.command('reply');
					return false;
				case 115:		// s = search
					$('#quicksearchbox').focus();
					$('#quicksearchbox').select();
					return false;
				case 117:		// u = update (check for mail)
					cmail.command('checkmail');
					return false;
                	}
		} else if (cmail.env.action == 'show' || cmail.env.action == 'preview') {
			switch (e.which) {
				case 82:		// R = reply-all
					cmail.command('reply-all');
					return false;
				case 102:		// f = forward
					cmail.command('forward');
					return false;
				case 106:		// j = previous message (similar to Gmail)
					cmail.command('previousmessage');
					return false;
				case 107:		// k = next message (similar to Gmail)
					cmail.command('nextmessage');
					return false;
				case 112:		// p = print
					cmail.command('print');
					return false;
				case 114:		// r = reply
					cmail.command('reply');
					return false;
			}
		}
        });
});
