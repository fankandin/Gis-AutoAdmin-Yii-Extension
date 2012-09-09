var EGistabIndex;
function EGisAddCoordsRow($coords, x, y)
{
	var $ol = $coords.find('ol');
	var $liLast = $ol.find('li').last();
	EGistabIndex = parseInt($liLast.find('[tabindex]').last().attr('tabindex'));
	var rowI = parseInt($liLast.attr('row-i'));
	$liLast.clone(true, true).appendTo($ol);
	$liLast = $ol.find('li').last();	//now it's a new row

	var se = new RegExp('\\['+ rowI +'\\]');
	var re = '['+ (rowI+1)  +']';
	$liLast.find('[for*="['+ rowI +']"]').each(function() {EGisCoordsRowAttrUpdate($(this), 'for', se, re)});
	$liLast.find('[name*="['+ rowI +']"]').each(function() {EGisCoordsRowAttrUpdate($(this), 'name', se, re)});
	$liLast.find('[id*="['+ rowI +']"]').each(function() {EGisCoordsRowAttrUpdate($(this), 'id', se, re)});
	$liLast.find('[tabindex]').each(EGisUpdateTabindex);
	$liLast.attr('row-i', (rowI+1));
	//now updating tabindexes of following fields
	$ol.parents('.item').next('.item').find('[tabindex]').each(EGisUpdateTabindex);

	if(x != undefined)
		$liLast.prev().find('input[name*="[lon]"]').val(x);	//it's important to set value of a previous one: the last input pair should be empty
	if(y != undefined)
		$liLast.prev().find('input[name*="[lat]"]').val(y);
	$liLast.find('input[name*="[lon]"],input[name*="[lat]"]').val('');
}
function EGisRemoveCoordsRow($coords, i)
{
	$lis = $coords.find('li[row-i]');
	if(i==-1)
	{
		$lis.each(function() {EGisRemoveCoordsRow($coords, $(this).attr('row-i'))});
		return;
	}
	else if(i>=0)
	{
		if($lis.length > 1)
		{
			var $li = $coords.find('li[row-i="'+i+'"]');
			EGistabIndex = $li.find('[tabindex]').first().attr('tabindex') - 1;
			$li.next('li').find('[tabindex]').each(EGisUpdateTabindex);
			$li.parents('.item').next('.item').find('[tabindex]').each(EGisUpdateTabindex);
			$li.remove();
		}
		else
		{
			$lis.find('input').val('');
		}
	}
}
function EGisUpdateTabindex()
{
	$(this).attr('tabindex', ++EGistabIndex);
}
function EGisCoordsRowAttrUpdate($obj, attr, se, re)
{
	$obj.attr(attr, $obj.attr(attr).replace(se, re));
}

$(document).ready(function(){
	$('#editform .item[class*="block_Gis"] .collapse').click(function() {
		$(this).toggleClass('opened').prev('.coords').toggleClass('opened');
	});
	$('#editform .item .coords .delrow').click(function() {
		EGisRemoveCoordsRow($(this).parents('.coords'), $(this).parents('li').attr('row-i'));
	});
	$('#editform .item .coords .newrow').click(function() {
		EGisAddCoordsRow($(this).parents('.coords'));
	});
});
