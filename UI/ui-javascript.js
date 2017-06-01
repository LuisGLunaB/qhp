function TRANSLATE(key){
  return TRANSLATIONS[key][LANGUAGE];
}

function ui_confirm(message){
   var answer = confirm(message);
   return answer;
}
