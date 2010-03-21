/* pwtools */

if (window.cmail) {
    cmail.addEventListener('init', function(evt) {
    if(cmail.pwtools){
    cmail.register_command('plugin.pwtools-save', function() { 
    var input_enabled = $("[name='_pwtoolsenabled']");
    var input_question = $("[name='_pwtoolsquestion']");
    var input_answer = $("[name='_pwtoolsanswer']");
    var input_answerconfirm = $("[name='_pwtoolsanswerconfirm']");

    if(input_question.val() == ""){
      parent.cmail.display_message(cmail.gettext('pwtoolsquestionempty','pwtools'),'error');
      input_question.focus();
      return false;
    }
    if(input_answer.val() == ""){
      parent.cmail.display_message(cmail.gettext('pwtoolsanswerempty','pwtools'),'error');
      input_answer.focus();
      return false;
    }
    if(input_answer.val().toLowerCase() != input_answerconfirm.val().toLowerCase()){
      parent.cmail.display_message(cmail.gettext('pwtoolsanswernotmatch','pwtools'),'error');
      input_answerconfirm.focus();
      return false;
    }
    if(input_enabled.attr('checked')){
      parent.cmail.display_message(cmail.gettext('pwtoolsactive','pwtools'),'confirmation');
      document.forms.pwtoolsform.submit();      
    }
    else{
      parent.cmail.display_message(cmail.gettext('pwtoolsinactive','pwtools'),'warning');
      setTimeout('document.forms.pwtoolsform.submit();', 6000);      
    }

    }, true);
  }})
}

function pwTools(){

  var input_user = $("[name='_user']");
  
  user = input_user.val();
  if(user == ""){
    cmail.display_message(cmail.gettext('pwtoolsuserempty','pwtools'),'error');  
  }
  else{
    if(user == 'undefined')
      document.location.href = "./";
    else
      document.location.href = "./?_task=settings&_action=plugin.pwtools_reset&_username=" + escape(user);
  }
}
