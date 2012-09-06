var EGistabIndex;
function EGisUpdateTabindex()
{
	$(this).attr('tabindex', ++EGistabIndex);
}

$(document).ready(function(){
	$('#editform .item[class*="block_Gis"] .collapse').click(function() {
		$(this).toggleClass('opened').prev('.coords').toggleClass('opened');
	});
	$('#editform .item .coords .delrow').click(function() {
		var $li = $(this).parents('li');
		EGistabIndex = $li.find('[tabindex]').first().attr('tabindex') - 1;
		$li.next('li').find('[tabindex]').each(EGisUpdateTabindex);
		$li.parents('.item').next('.item').find('[tabindex]').each(EGisUpdateTabindex);
		$li.remove();
	});
	$('#editform .item .coords .newrow').click(function() {
		function coordRowAttrUpdate($this, attr)
		{
			$this.attr(attr, $this.attr(attr).replace(se, re));
		}

		var $this = $(this);
		var $ol = $this.parents('.coords').find('ol');
		var $liLast = $ol.find('li').last();
		EGistabIndex = parseInt($liLast.find('[tabindex]').last().attr('tabindex'));
		var rowI = parseInt($liLast.attr('row-i'));
		$liLast.clone(true, true).appendTo($ol);
		$liLast = $ol.find('li').last();	//now it's a new row
		var se = new RegExp('\\['+ rowI +'\\]');
		var re = '['+ (rowI+1)  +']';
		$liLast.find('[for*="['+ rowI +']"]').each(function() {coordRowAttrUpdate($(this), 'for')});
		$liLast.find('[name*="['+ rowI +']"]').each(function() {coordRowAttrUpdate($(this), 'name')});
		$liLast.find('[id*="['+ rowI +']"]').each(function() {coordRowAttrUpdate($(this), 'id')});
		$liLast.find('[tabindex]').each(EGisUpdateTabindex);
		$liLast.attr('row-i', (rowI+1));
		//now updating tabindexes of following fields
		//$ol.parents('.coords').next('[tabindex]').each(EGisUpdateTabindex);
		$ol.parents('.item').next('.item').find('[tabindex]').each(EGisUpdateTabindex);
	});
});
