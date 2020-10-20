calculateHeight = ->
	
	$content = $('#overlay-content')
	contentHeight = parseInt($content.height()) + parseInt($content.css('margin-top')) + parseInt($content.css('margin-bottom'))
	windowHeight = $(window).height()
	
	finalHeight = if windowHeight > contentHeight then windowHeight else contentHeight
	
	return finalHeight

$(document).ready ->
	
	$(window).resize ->
		
		if $(window).height() < 560 and $(window).width() > 600
			$('#overlay').addClass 'short'
		else
			$('#overlay').removeClass 'short'
		
		$('#overlay-background').height calculateHeight()
	
	$(window).trigger 'resize'
	
	# open
	$('#popup-trigger').click ->
		$('#overlay')
			.addClass 'open'
			.find('.signup-form input:first').select()
	
	# close
	$('#overlay-background,#overlay-close').click ->
		$('#overlay').removeClass 'open'