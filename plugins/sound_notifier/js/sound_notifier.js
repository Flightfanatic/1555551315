/* sound_notifier plugin script */

function cmail_playsound()
{
  soundManager.play('notify','plugins/sound_notifier/sounds/' + cmail.env.sound_notifier);
}

// callback for app-onload event
if (window.cmail)
{
  cmail.addEventListener('plugin.sound', cmail_playsound);
}

function cmail_playsound_onchange(sound)
{
  cmail.env.sound_notifier = sounds[sound];
  if(cmail.env.sound_notifier_last)
    soundManager.stop(cmail.env.sound_notifier_last);
  soundManager.play(sound,'plugins/sound_notifier/sounds/' + cmail.env.sound_notifier);
  cmail.env.sound_notifier_last = sound;
}
