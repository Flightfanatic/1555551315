function show_rss_link(){
  var add_url = "";
  var url = "";
  var input;
  if(cmail.env.flag_filter)
    add_url = '&_flag=' + urlencode(cmail.env.flag_filter);
  url = window.location.protocol + "//" + window.location.host + window.location.pathname + '?_task=mail&_action=plugin.rss&_mbox='+urlencode(cmail.env.mailbox) + add_url;
  input = prompt("RSS Feed URL", url);
  if(input){
    if(parent.roundcube_wrapper)
      parent.location.href = input;
    else
      document.location.href = input;
  }
}