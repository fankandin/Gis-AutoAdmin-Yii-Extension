var $inputs = window.opener.$('[id="'+ document.location.hash.substr(1) +'"]').siblings('input');
var $srid = $inputs.filter('[name$="[srid]"]');
var $lat = $inputs.filter('[name$="[lat]"]');
var $lon = $inputs.filter('[name$="[lon]"]');
